<?php
namespace App\Controllers;

class InventoryController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        $session = session();
        $userRole = $session->get('role') ?? '';
        $userId = $session->get('user_id') ?? 0;

        // Default query builder
        $builder = $db->table('inventory i')
            ->select('i.inventory_id, b.branch_name, p.product_name, i.current_stock, p.unit_price, (i.current_stock * p.unit_price) as stock_value')
            ->join('branches b', 'b.branch_id = i.branch_id', 'left')
            ->join('products p', 'p.product_id = i.product_id', 'left');

        // Filter inventory based on user role
        if ($userRole === 'inventory_staff') {
            // Get user's branch_id from user_branches table
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->where('is_primary', 1)
                ->get()
                ->getRowArray();

            if (!$branch || !isset($branch['branch_id'])) {
                // No branch assigned, return empty result in staff dashboard
                return view('dashboard/inventory_staff', ['lowStockItems' => []]);
            }

            $branchId = (int)$branch['branch_id'];
            $builder->where('i.branch_id', $branchId);

            // Low stock list for this branch
            $lowStockItems = $db->table('inventory i')
                ->select('p.product_name, i.available_stock, b.branch_name')
                ->join('products p', 'p.product_id = i.product_id', 'left')
                ->join('branches b', 'b.branch_id = i.branch_id', 'left')
                ->where('i.branch_id', $branchId)
                ->where('i.available_stock <= p.minimum_stock')
                ->orderBy('p.product_name', 'ASC')
                ->get()
                ->getResultArray();

            // Pending receives (approved or ordered) for this branch
            $pendingReceives = $db->table('purchase_orders po')
                ->select('po.purchase_order_id, po.po_number, po.status, po.expected_delivery_date, s.name as supplier_name')
                ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
                ->where('po.branch_id', $branchId)
                ->whereIn('po.status', ['approved','ordered'])
                ->orderBy('po.expected_delivery_date', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();

            // Products for adjust dropdown (also used for fallback rows)
            $products = $db->table('products')
                ->select('product_id, product_name, unit_price, minimum_stock')
                ->orderBy('product_name', 'ASC')
                ->get()
                ->getResultArray();

            // My branch inventory (real-time view) – show ALL products, including zero stock
            $branchInventory = $db->table('products p')
                ->select('p.product_name, COALESCE(i.current_stock,0) as current_stock, COALESCE(i.available_stock,0) as available_stock, p.unit_price, (COALESCE(i.current_stock,0) * p.unit_price) as stock_value, p.minimum_stock')
                ->join('inventory i', 'i.product_id = p.product_id AND i.branch_id = '.(int)$branchId, 'left')
                ->orderBy('p.product_name', 'ASC')
                ->get()->getResultArray();

            // Fallback: if no rows returned (e.g., empty inventory table), build zero-stock rows from products list
            if (empty($branchInventory) && !empty($products)) {
                $branchInventory = array_map(static function($p) {
                    return [
                        'product_name'    => $p['product_name'],
                        'current_stock'   => 0,
                        'available_stock' => 0,
                        'unit_price'      => $p['unit_price'] ?? 0,
                        'stock_value'     => 0,
                        'minimum_stock'   => $p['minimum_stock'] ?? 0,
                    ];
                }, $products);
            }

            // Expiry soon (within next 14 days) parsed from stock_movements.notes 'expires_at=YYYY-MM-DD'
            $expirySoon = $db->query(
                "SELECT p.product_name, sm.created_at, SUBSTRING_INDEX(sm.notes,'=',-1) AS expires_at
                 FROM stock_movements sm
                 JOIN products p ON p.product_id = sm.product_id
                 WHERE sm.branch_id = ? AND sm.notes LIKE 'expires_at=%' AND DATE(SUBSTRING_INDEX(sm.notes,'=',-1)) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
                 ORDER BY expires_at ASC",
                [$branchId]
            )->getResultArray();

            return view('dashboard/inventory_staff', [
                'lowStockItems' => $lowStockItems,
                'pendingReceives' => $pendingReceives,
                'products' => $products,
                'branchInventory' => $branchInventory,
                'expirySoon' => $expirySoon,
                'lowStockCount' => count($lowStockItems),
            ]);
        }

        // Default: central/system/others see consolidated inventory list
        $inventory = $builder->orderBy('b.branch_name', 'ASC')->get()->getResultArray();
        return view('dashboard/inventory', ['inventory' => $inventory]);
    }

    public function adjust()
    {
        $session = session();
        $role = (string) ($session->get('role') ?? '');
        $userId = (int) ($session->get('user_id') ?? 0);
        if (!in_array($role, ['inventory_staff','central_admin','system_admin'])) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $db = db_connect();
        // Determine staff branch (central/system may optionally post branch_id)
        $branchId = (int) ($this->request->getPost('branch_id') ?? 0);
        if ($branchId === 0) {
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->where('is_primary', 1)
                ->get()->getRowArray();
            $branchId = (int)($branch['branch_id'] ?? 0);
        }

        $productId = (int) $this->request->getPost('product_id');
        $qty = (int) $this->request->getPost('qty'); // negative to reduce
        $reason = trim((string) $this->request->getPost('reason'));

        if ($branchId <= 0 || $productId <= 0 || $qty === 0) {
            return redirect()->back()->with('error', 'Provide product, non-zero quantity, and valid branch.');
        }

        $now = date('Y-m-d H:i:s');
        $inv = $db->table('inventory')
            ->where(['branch_id' => $branchId, 'product_id' => $productId])
            ->get()->getRowArray();

        if ($inv) {
            $newCurrent = (int)$inv['current_stock'] + $qty;
            if ($newCurrent < 0) $newCurrent = 0;
            $newAvailable = $newCurrent - (int)$inv['reserved_stock'];
            $db->table('inventory')->where('inventory_id', $inv['inventory_id'])->update([
                'current_stock' => $newCurrent,
                'available_stock' => $newAvailable,
                'last_updated' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $db->table('inventory')->insert([
                'product_id' => $productId,
                'branch_id' => $branchId,
                'current_stock' => max(0, $qty),
                'reserved_stock' => 0,
                'available_stock' => max(0, $qty),
                'last_updated' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Movement log
        $movementType = 'adjustment';
        // Fetch unit price for value estimate
        $priceRow = $db->table('products')->select('unit_price')->where('product_id', $productId)->get()->getRowArray();
        $unitPrice = (float)($priceRow['unit_price'] ?? 0);
        $db->table('stock_movements')->insert([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'movement_type' => $movementType,
            'quantity' => $qty,
            'reference_type' => 'adjustment',
            'reference_id' => null,
            'unit_price' => $unitPrice,
            'total_value' => $unitPrice * $qty,
            'notes' => $reason,
            'created_by' => $userId,
            'created_at' => $now,
        ]);

        return redirect()->back()->with('success', 'Stock adjusted.');
    }

    public function scan()
    {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['inventory_staff','central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }
        return view('dashboard/inventory_scan');
    }

    public function processScan()
    {
        $session = session();
        $role = (string) ($session->get('role') ?? '');
        $userId = (int) ($session->get('user_id') ?? 0);
        if (!in_array($role, ['inventory_staff','central_admin','system_admin'])) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $db = db_connect();

        // Determine branch
        $branchId = (int) ($this->request->getPost('branch_id') ?? 0);
        if ($branchId === 0) {
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->where('is_primary', 1)
                ->get()->getRowArray();
            $branchId = (int)($branch['branch_id'] ?? 0);
        }

        $barcode = trim((string) $this->request->getPost('barcode'));
        $qty = (int) $this->request->getPost('qty'); // + receive, - issue
        if ($branchId <= 0 || $barcode === '' || $qty === 0) {
            return redirect()->back()->with('error', 'Provide barcode, non-zero quantity, and valid branch.');
        }

        // Find product by barcode (fallback to product_code)
        $product = $db->table('products')
            ->where('barcode', $barcode)
            ->orWhere('product_code', $barcode)
            ->get()->getRowArray();
        if (!$product) {
            return redirect()->back()->with('error', 'Barcode not found.');
        }

        $productId = (int)$product['product_id'];
        $now = date('Y-m-d H:i:s');
        $inv = $db->table('inventory')->where(['branch_id' => $branchId, 'product_id' => $productId])->get()->getRowArray();

        if ($inv) {
            $newCurrent = max(0, (int)$inv['current_stock'] + $qty);
            $newAvailable = $newCurrent - (int)$inv['reserved_stock'];
            $db->table('inventory')->where('inventory_id', $inv['inventory_id'])->update([
                'current_stock' => $newCurrent,
                'available_stock' => $newAvailable,
                'last_updated' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $db->table('inventory')->insert([
                'product_id' => $productId,
                'branch_id' => $branchId,
                'current_stock' => max(0, $qty),
                'reserved_stock' => 0,
                'available_stock' => max(0, $qty),
                'last_updated' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Movement type: in for positive, out for negative
        $movementType = $qty > 0 ? 'in' : 'out';
        $unitPrice = (float)($product['unit_price'] ?? 0);
        $expiresAt = null;
        if ($qty > 0 && (int)($product['is_perishable'] ?? 0) === 1 && !empty($product['shelf_life_days'])) {
            $expiresAt = date('Y-m-d', strtotime('+'.((int)$product['shelf_life_days']).' days'));
        }

        $db->table('stock_movements')->insert([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'movement_type' => $movementType,
            'quantity' => $qty,
            'reference_type' => 'scan',
            'reference_id' => null,
            'unit_price' => $unitPrice,
            'total_value' => $unitPrice * $qty,
            'notes' => $expiresAt ? ('expires_at='.$expiresAt) : null,
            'created_by' => $userId,
            'created_at' => $now,
        ]);

        return redirect()->back()->with('success', 'Scan processed for '.$product['product_name']);
    }
}
