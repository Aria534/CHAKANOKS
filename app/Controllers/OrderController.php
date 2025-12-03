<?php
namespace App\Controllers;

class OrderController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        $builder = $db->table('purchase_orders po')
            ->select('po.purchase_order_id, po.po_number, po.status, po.total_amount, po.requested_date, po.created_at, b.branch_name')
            ->join('branches b', 'b.branch_id = po.branch_id', 'left');

        // Role-based scoping: staff and branch managers only see their branch
        $role = (string)(session('role') ?? '');
        $userId = (int)(session('user_id') ?? 0);
        if (in_array($role, ['inventory_staff','branch_manager'])) {
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->orderBy('user_branch_id', 'ASC')
                ->get()->getRowArray();
            if ($branch && isset($branch['branch_id'])) {
                $builder->where('po.branch_id', (int)$branch['branch_id']);
            } else {
                // If no branch, return empty
                return view('dashboard/orders', ['orders' => []]);
            }
        }

        $orders = $builder->orderBy('po.created_at', 'DESC')->get()->getResultArray();

        return view('dashboard/orders', ['orders' => $orders]);
    }

    public function create()
    {
        $session = session();
        $role = (string) ($session->get('role') ?? '');
        $userId = (int) ($session->get('user_id') ?? 0);

        if (!in_array($role, ['branch_manager','central_admin','system_admin'])) {
            return redirect()->to(site_url('/orders'))->with('error', 'Unauthorized');
        }

        $db = db_connect();
        $suppliers = $db->table('suppliers')
            ->select('supplier_id, supplier_name as name')
            ->where('status', 'active')
            ->orderBy('supplier_name')
            ->get()
            ->getResultArray();
        
        // Get all products with supplier info for filtering
        $products = $db->table('products p')
            ->select('p.product_id, p.product_name, p.unit_price, p.supplier_id, s.supplier_name')
            ->join('suppliers s', 's.supplier_id = p.supplier_id', 'left')
            ->where('p.status', 'active')
            ->orderBy('p.product_name')
            ->get()
            ->getResultArray();

        $selectedBranchId = 0;
        if ($role === 'branch_manager') {
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->orderBy('user_branch_id','ASC')
                ->get()->getRowArray();
            $selectedBranchId = (int)($branch['branch_id'] ?? 0);
            $branches = [];
            if ($selectedBranchId > 0) {
                $b = $db->table('branches')->select('branch_id, branch_name')->where('branch_id', $selectedBranchId)->get()->getRowArray();
                if ($b) { $branches = [$b]; }
            }
        } else {
            $branches = $db->table('branches')->select('branch_id, branch_name')->orderBy('branch_name')->get()->getResultArray();
        }

        return view('dashboard/orders_create', compact('branches','suppliers','products','selectedBranchId'));
    }

    public function store()
    {
        $session = session();
        $role = (string) ($session->get('role') ?? '');
        $userId = (int) ($session->get('user_id') ?? 0);
        
        if (!in_array($role, ['branch_manager','central_admin','system_admin'])) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $db = db_connect();

        // Get and validate branch
        $branchId = (int) $this->request->getPost('branch_id');
        if ($role === 'branch_manager') {
            // Force to manager's branch
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->orderBy('user_branch_id','ASC')
                ->get()->getRowArray();
            $branchId = (int)($branch['branch_id'] ?? 0);
        }
        
        // Get and validate supplier
        $supplierId = (int) $this->request->getPost('supplier_id');
        
        // Get items array - handle both array formats
        $items = $this->request->getPost('items');
        if (!is_array($items)) {
            $items = [];
        }

        // Debug logging
        log_message('debug', 'Purchase Request - Branch ID: ' . $branchId);
        log_message('debug', 'Purchase Request - Supplier ID: ' . $supplierId);
        log_message('debug', 'Purchase Request - Items received: ' . json_encode($items));

        // Validate required fields
        if ($branchId <= 0) {
            return redirect()->back()->with('error', 'Please select a branch.');
        }
        if ($supplierId <= 0) {
            return redirect()->back()->with('error', 'Please select a supplier.');
        }
        
        // Filter valid items (must have product_id and qty > 0)
        $validItems = [];
        foreach ($items as $it) {
            if (!is_array($it)) {
                continue;
            }
            $pid = (int)($it['product_id'] ?? 0);
            $qty = (int)($it['qty'] ?? 0);
            if ($pid > 0 && $qty > 0) {
                $validItems[] = ['product_id' => $pid, 'qty' => $qty];
            }
        }

        log_message('debug', 'Purchase Request - Valid items: ' . json_encode($validItems));

        if (empty($validItems)) {
            return redirect()->back()->with('error', 'Please select at least one product and enter a quantity greater than 0.');
        }

        // Generate PO number (unique scheme with timestamp and random)
        $timestamp = date('YmdHis');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $poNumber = 'PO-' . $timestamp . '-' . $random;
        
        // Ensure uniqueness
        $db = db_connect();
        $exists = $db->table('purchase_orders')->where('po_number', $poNumber)->countAllResults();
        if ($exists > 0) {
            $poNumber = 'PO-' . $timestamp . '-' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        }
        
        $now = date('Y-m-d H:i:s');

        // Get product prices
        $productIds = array_column($validItems, 'product_id');
        $productRows = $db->table('products')
            ->whereIn('product_id', $productIds)
            ->get()
            ->getResultArray();
        
        $productPrices = [];
        foreach ($productRows as $pr) {
            $productPrices[(int)$pr['product_id']] = (float)$pr['unit_price'];
        }

        // Compute total
        $total = 0.0;
        foreach ($validItems as $it) {
            $pid = (int)$it['product_id'];
            $qty = (int)$it['qty'];
            $up = $productPrices[$pid] ?? 0.0;
            if ($up > 0) {
                $total += ($up * $qty);
            }
        }

        if ($total <= 0) {
            return redirect()->back()->with('error', 'Invalid product prices. Please check your items.');
        }

        try {
            // Insert purchase order (status pending = PR submitted)
            $poModel = model('App\\Models\\PurchaseOrderModel');
            $poData = [
                'po_number' => $poNumber,
                'branch_id' => $branchId,
                'supplier_id' => $supplierId,
                'requested_by' => $userId,
                'status' => 'pending',
                'total_amount' => $total,
                'requested_date' => $now,
            ];
            
            // Model handles timestamps automatically, but set them explicitly if needed
            if (!$poModel->insert($poData)) {
                $errors = $poModel->errors();
                log_message('error', 'Failed to insert purchase order. Data: ' . json_encode($poData));
                log_message('error', 'Model errors: ' . json_encode($errors));
                return redirect()->back()->with('error', 'Failed to create purchase order: ' . (is_array($errors) ? implode(', ', $errors) : 'Unknown error'));
            }
            
            $poId = $poModel->getInsertID();
            
            if (!$poId || $poId <= 0) {
                log_message('error', 'Purchase order insert returned invalid ID');
                return redirect()->back()->with('error', 'Failed to create purchase order. Please try again.');
            }
            
            log_message('info', 'Purchase order created successfully. PO ID: ' . $poId);

            // Insert items
            $itemModel = model('App\\Models\\PurchaseOrderItemModel');
            $itemsInserted = 0;
            foreach ($validItems as $it) {
                $pid = (int)$it['product_id'];
                $qty = (int)$it['qty'];
                $up = $productPrices[$pid] ?? 0.0;
                
                if ($up <= 0) {
                    log_message('warning', 'Skipping item with invalid price. Product ID: ' . $pid);
                    continue; // Skip items with invalid prices
                }

                $itemData = [
                    'purchase_order_id' => $poId,
                    'product_id' => $pid,
                    'quantity_requested' => $qty,
                    'quantity_delivered' => 0,
                    'unit_price' => $up,
                    'total_price' => $up * $qty,
                ];
                
                if (!$itemModel->insert($itemData)) {
                    $itemErrors = $itemModel->errors();
                    log_message('error', 'Failed to insert order item. Data: ' . json_encode($itemData));
                    log_message('error', 'Item errors: ' . json_encode($itemErrors));
                    // Continue with other items
                } else {
                    $itemsInserted++;
                }
            }
            
            if ($itemsInserted === 0) {
                // Rollback: delete the purchase order if no items were inserted
                $poModel->delete($poId);
                return redirect()->back()->with('error', 'Failed to add items to purchase order. Please try again.');
            }
            
            log_message('info', 'Purchase order created with ' . $itemsInserted . ' items. PO ID: ' . $poId);
            return redirect()->to(site_url('/orders'))->with('success', 'Purchase Request submitted successfully.');
            
        } catch (\Exception $e) {
            log_message('error', 'Error creating purchase order: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        $session = session();
        $role = (string) ($session->get('role') ?? '');
        $userId = (int) ($session->get('user_id') ?? 0);
        
        // Check authorization
        if (!in_array($role, ['central_admin','system_admin','inventory_staff','branch_manager'])) {
            return redirect()->to(site_url('/orders'))->with('error', 'Unauthorized access');
        }
        
        $db = db_connect();
        
        // Get purchase order details with related data
        $po = $db->table('purchase_orders po')
            ->select('po.*, b.branch_name, s.supplier_name, s.contact_person, s.phone, s.email,
                     u1.username as requested_by_name, u2.username as approved_by_name')
            ->join('branches b', 'b.branch_id = po.branch_id', 'left')
            ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
            ->join('users u1', 'u1.user_id = po.requested_by', 'left')
            ->join('users u2', 'u2.user_id = po.approved_by', 'left')
            ->where('po.purchase_order_id', $id)
            ->get()
            ->getRowArray();
        
        if (!$po) {
            return redirect()->to(site_url('/orders'))->with('error', 'Purchase order not found');
        }
        
        // For branch staff, verify they have access to this PO's branch
        if (in_array($role, ['inventory_staff', 'branch_manager'])) {
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->orderBy('user_branch_id', 'ASC')
                ->get()->getRowArray();
            $myBranchId = (int)($branch['branch_id'] ?? 0);
            if ($myBranchId <= 0 || (int)$po['branch_id'] !== $myBranchId) {
                return redirect()->to(site_url('/orders'))->with('error', 'You do not have access to this purchase order');
            }
        }
        
        // Get purchase order items with product details
        $items = $db->table('purchase_order_items poi')
            ->select('poi.*, p.product_name, p.product_code')
            ->join('products p', 'p.product_id = poi.product_id', 'left')
            ->where('poi.purchase_order_id', $id)
            ->orderBy('p.product_name', 'ASC')
            ->get()
            ->getResultArray();
        
        return view('dashboard/order_view', [
            'po' => $po,
            'items' => $items,
            'userRole' => $role
        ]);
    }

    public function approve($id)
    {
        $session = session();
        $userId = (int) ($session->get('user_id') ?? 0);
        $role = (string) ($session->get('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->back()->with('error', 'Unauthorized');
        }
        $poModel = model('App\\Models\\PurchaseOrderModel');
        $po = $poModel->find($id);
        if (!$po) return redirect()->back()->with('error', 'Order not found');
        if ($po['status'] !== 'pending') return redirect()->back()->with('error', 'Only pending orders can be approved');
        $now = date('Y-m-d H:i:s');
        $poModel->update($id, [
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_date' => $now,
            'updated_at' => $now,
        ]);
        return redirect()->to(site_url('/orders'))->with('success', 'Order approved');
    }

    public function send($id)
    {
        $role = (string) (session()->get('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->back()->with('error', 'Unauthorized');
        }
        $poModel = model('App\\Models\\PurchaseOrderModel');
        $po = $poModel->find($id);
        if (!$po) return redirect()->back()->with('error', 'Order not found');
        if (!in_array($po['status'], ['approved','pending'])) return redirect()->back()->with('error', 'Only approved/pending can be sent');
        $poModel->update($id, [
            'status' => 'ordered',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->to(site_url('/orders'))->with('success', 'Order sent to supplier');
    }

    public function receive($id)
    {
        $session = session();
        $userId = (int) ($session->get('user_id') ?? 0);
        $role = (string) ($session->get('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin','inventory_staff','branch_manager'])) {
            return redirect()->back()->with('error', 'Unauthorized');
        }
        $db = db_connect();
        $poModel = model('App\\Models\\PurchaseOrderModel');
        $itemModel = model('App\\Models\\PurchaseOrderItemModel');
        $invModel = model('App\\Models\\InventoryModel');
        $moveModel = model('App\\Models\\StockMovementModel');

        $po = $poModel->find($id);
        if (!$po) return redirect()->back()->with('error', 'Order not found');
        if (!in_array($po['status'], ['ordered','approved','pending'])) return redirect()->back()->with('error', 'Order is not in receivable state');

        // For branch_manager or inventory_staff, ensure the PO belongs to their branch
        if (in_array($role, ['branch_manager','inventory_staff'])) {
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->orderBy('user_branch_id', 'ASC')
                ->get()->getRowArray();
            $myBranchId = (int)($branch['branch_id'] ?? 0);
            if ($myBranchId <= 0 || (int)$po['branch_id'] !== $myBranchId) {
                return redirect()->back()->with('error', 'Unauthorized branch');
            }
        }

        $items = $itemModel->where('purchase_order_id', $id)->findAll();
        $now = date('Y-m-d H:i:s');
        foreach ($items as $it) {
            $qty = (int) $it['quantity_requested'];
            $pid = (int) $it['product_id'];

            // Update delivered qty
            $itemModel->update($it['po_item_id'], [
                'quantity_delivered' => $qty,
                'updated_at' => $now,
            ]);

            // Update inventory for branch + product
            $inv = $db->table('inventory')->where(['branch_id' => $po['branch_id'], 'product_id' => $pid])->get()->getRowArray();
            if ($inv) {
                $newCurrent = (int)$inv['current_stock'] + $qty;
                $newAvailable = $newCurrent - (int)$inv['reserved_stock'];
                $db->table('inventory')->where('inventory_id', $inv['inventory_id'])->update([
                    'current_stock' => $newCurrent,
                    'available_stock' => $newAvailable,
                    'last_updated' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $db->table('inventory')->insert([
                    'product_id' => $pid,
                    'branch_id' => $po['branch_id'],
                    'current_stock' => $qty,
                    'reserved_stock' => 0,
                    'available_stock' => $qty,
                    'last_updated' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Compute expiry date for perishables (logged in notes)
            $expiresAt = null;
            $prodRow = $db->table('products')->select('is_perishable, shelf_life_days')->where('product_id', $pid)->get()->getRowArray();
            if ($prodRow && (int)($prodRow['is_perishable'] ?? 0) === 1 && !empty($prodRow['shelf_life_days'])) {
                $expiresAt = date('Y-m-d', strtotime('+'.((int)$prodRow['shelf_life_days']).' days'));
            }

            // Stock movement
            $db->table('stock_movements')->insert([
                'product_id' => $pid,
                'branch_id' => $po['branch_id'],
                'movement_type' => 'in',
                'quantity' => $qty,
                'reference_type' => 'purchase_order',
                'reference_id' => $id,
                'unit_price' => $it['unit_price'],
                'total_value' => $it['unit_price'] * $qty,
                'notes' => $expiresAt ? ('expires_at='.$expiresAt) : null,
                'created_by' => $userId,
                'created_at' => $now,
            ]);
        }

        $poModel->update($id, [
            'status' => 'delivered',
            'actual_delivery_date' => date('Y-m-d'),
            'updated_at' => $now,
        ]);

        return redirect()->to(site_url('/orders'))->with('success', 'Order received and inventory updated');
    }
}
