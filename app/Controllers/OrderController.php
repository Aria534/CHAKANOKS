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
        $suppliers = $db->table('suppliers')->select('supplier_id, supplier_name as name')->orderBy('supplier_name')->get()->getResultArray();
        $products = $db->table('products')->select('product_id, product_name, unit_price')->orderBy('product_name')->get()->getResultArray();

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
            return redirect()->back()->with('error', 'Unauthorized');
        }
        $db = db_connect();

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
        $supplierId = (int) $this->request->getPost('supplier_id');
        $items = (array) $this->request->getPost('items'); // [ [product_id, qty] ... ]

        if ($branchId <= 0 || $supplierId <= 0 || empty($items)) {
            return redirect()->back()->with('error', 'Please select branch, supplier, and at least one item.');
        }

        // Generate PO number (simple scheme)
        $poNumber = 'PO-' . date('Ymd-His');
        $now = date('Y-m-d H:i:s');

        // Compute total
        $total = 0.0;
        $productPrices = [];
        $productRows = $db->table('products')->whereIn('product_id', array_column($items, 'product_id'))->get()->getResultArray();
        foreach ($productRows as $pr) { $productPrices[(int)$pr['product_id']] = (float)$pr['unit_price']; }
        foreach ($items as $it) {
            $pid = (int)($it['product_id'] ?? 0);
            $qty = (int)($it['qty'] ?? 0);
            if ($pid <= 0 || $qty <= 0) continue;
            $up = $productPrices[$pid] ?? 0.0;
            $total += ($up * $qty);
        }

        // Insert purchase order (status pending = PR submitted)
        $poModel = model('App\\Models\\PurchaseOrderModel');
        $poId = $poModel->insert([
            'po_number' => $poNumber,
            'branch_id' => $branchId,
            'supplier_id' => $supplierId,
            'requested_by' => $userId,
            'status' => 'pending',
            'total_amount' => $total,
            'requested_date' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ], true);

        // Insert items
        $itemModel = model('App\\Models\\PurchaseOrderItemModel');
        foreach ($items as $it) {
            $pid = (int)($it['product_id'] ?? 0);
            $qty = (int)($it['qty'] ?? 0);
            if ($pid <= 0 || $qty <= 0) continue;
            $up = $productPrices[$pid] ?? 0.0;
            $itemModel->insert([
                'purchase_order_id' => $poId,
                'product_id' => $pid,
                'quantity_requested' => $qty,
                'quantity_delivered' => 0,
                'unit_price' => $up,
                'total_price' => $up * $qty,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return redirect()->to(site_url('/orders'))->with('success', 'Purchase Request submitted.');
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
