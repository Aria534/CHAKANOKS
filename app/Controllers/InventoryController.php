<?php
namespace App\Controllers;

class InventoryController extends BaseController
{
    /**
     * Get inventory view for a specific branch
     * 
     * @param object $db Database connection
     * @param int $branchId Branch ID
     * @param array $allBranches List of all branches for dropdown
     * @param string $userRole Current user's role
     * @param int $userId Current user ID
     * @return string Rendered view
     */
private function getBranchInventoryView($db, $branchId, $allBranches, $userRole, $userId, $activeState = 'inventory')
    {
        // Validate database connection
        if (!is_object($db)) {
            $db = \Config\Database::connect();
        }
        
        // Validate branch ID
        if (!is_numeric($branchId) || $branchId <= 0) {
            return view('errors/html/error_404', [
                'message' => 'Invalid branch ID',
                'code' => 404
            ]);
        }
        // Make sure we have a valid database connection
        if (!is_object($db)) {
            $db = \Config\Database::connect();
        }
        // Get branch name for the view
        $branchMeta = [];
        try {
            $branchMeta = $db->table('branches')
                ->select('branch_name')
                ->where('branch_id', $branchId)
                ->where('status', 'active')
                ->get()
                ->getRowArray();
                
            if (empty($branchMeta)) {
                throw new \Exception('Branch not found');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error fetching branch details: ' . $e->getMessage());
            return view('errors/html/error_404', [
                'message' => 'Branch not found',
                'code' => 404
            ]);
        }

        $canSwitch = in_array($userRole, ['central_admin','system_admin','inventory_staff']);
        
        // Get low stock items for this branch
        $lowStockItems = [];
        try {
            $lowStockItems = $db->table('inventory i')
                ->select('p.product_name, i.available_stock, p.minimum_stock')
                ->join('products p', 'p.product_id = i.product_id', 'left')
                ->where('i.branch_id', $branchId)
                ->where('i.available_stock <= p.minimum_stock')
                ->where('p.status', 'active')
                ->where('i.status', 'active')
                ->orderBy('p.product_name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching low stock items: ' . $e->getMessage());
            // Continue with empty array for low stock items
        }

        // Get pending purchase orders
        $pendingReceives = $db->table('purchase_orders po')
            ->select('po.purchase_order_id, po.po_number, po.status, po.expected_delivery_date, s.supplier_name')
            ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
            ->where('po.branch_id', $branchId)
            ->whereIn('po.status', ['approved', 'ordered'])
            ->orderBy('po.expected_delivery_date', 'ASC')
            ->limit(10)
            ->get()
            ->getResultArray();

        try {
            // Get all active products with their inventory for this branch
            $branchInventory = $db->table('products p')
                ->select('p.product_id, p.product_name, p.unit_price, p.minimum_stock, 
                         COALESCE(i.current_stock, 0) as current_stock,
                         COALESCE(i.available_stock, 0) as available_stock,
                         (COALESCE(i.current_stock, 0) * p.unit_price) as stock_value,
                         p.status as product_status')
                ->join('inventory i', 'i.product_id = p.product_id AND i.branch_id = ' . (int)$branchId, 'left')
                ->where('p.status', 'active')
                ->orderBy('p.product_name', 'ASC')
                ->get()
                ->getResultArray();

            // If no inventory records exist yet, create zero entries for all active products
            if (empty($branchInventory)) {
                $products = $db->table('products')
                    ->select('product_id, product_name, unit_price, minimum_stock')
                    ->where('status', 'active')
                    ->orderBy('product_name', 'ASC')
                    ->get()
                    ->getResultArray();

                $branchInventory = array_map(function($product) {
                    return [
                        'product_id' => $product['product_id'],
                        'product_name' => $product['product_name'],
                        'unit_price' => $product['unit_price'],
                        'minimum_stock' => $product['minimum_stock'],
                        'current_stock' => 0,
                        'available_stock' => 0,
                        'stock_value' => 0,
                        'product_status' => 'active'
                    ];
                }, $products);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error fetching branch inventory: ' . $e->getMessage());
            $branchInventory = [];
        }

        return view('dashboard/inventory_staff', [
            'lowStockItems' => $lowStockItems,
            'pendingReceives' => $pendingReceives,
            'products' => $branchInventory, // All products with inventory data
            'branchInventory' => $branchInventory,
            'branchName' => $branchMeta['branch_name'] ?? 'Unknown Branch',
            'branches' => $allBranches,
            'selectedBranchId' => $branchId,
            'canSwitchBranches' => $canSwitch && count($allBranches) > 1,
            'active' => $activeState,
        ]);
    }

    /**
     * Get aggregated inventory view for all branches
     * 
     * @param object $db Database connection
     * @param array $allBranches List of all branches
     * @param bool $isStaffView Whether this is for staff view
     * @return string Rendered view
     */
private function getAggregatedInventoryView($db, $allBranches, $isStaffView = false, $activeState = 'inventory')
    {
        // Make sure we have a valid database connection
        if (!is_object($db)) {
            $db = \Config\Database::connect();
        }
        // Aggregated view for staff across all branches
        $lowStockItems = $db->table('inventory i')
            ->select('b.branch_name, p.product_name, i.available_stock, p.minimum_stock')
            ->join('products p', 'p.product_id = i.product_id', 'left')
            ->join('branches b', 'b.branch_id = i.branch_id', 'left')
            ->where('i.available_stock <= p.minimum_stock')
            ->orderBy('b.branch_name', 'ASC')
            ->orderBy('p.product_name', 'ASC')
            ->get()
            ->getResultArray();

        $pendingReceives = $db->table('purchase_orders po')
            ->select('po.purchase_order_id, po.po_number, po.status, po.expected_delivery_date, s.supplier_name')
            ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
            ->whereIn('po.status', ['approved','ordered'])
            ->orderBy('po.expected_delivery_date', 'ASC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get all products with aggregated inventory
        $branchInventory = $db->table('products p')
            ->select('p.product_id, p.product_name, p.unit_price, p.minimum_stock,
                     COALESCE(SUM(i.current_stock), 0) as current_stock,
                     COALESCE(SUM(i.available_stock), 0) as available_stock,
                     (COALESCE(SUM(i.current_stock * p.unit_price), 0)) as stock_value')
            ->join('inventory i', 'i.product_id = p.product_id', 'left')
            ->groupBy('p.product_id')
            ->orderBy('p.product_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('dashboard/inventory_staff', [
            'lowStockItems' => $lowStockItems,
            'pendingReceives' => $pendingReceives,
            'products' => $branchInventory,
            'branchInventory' => $branchInventory,
            'expirySoon' => [],
            'lowStockCount' => count($lowStockItems),
            'branchName' => 'All Branches',
            'branches' => $allBranches,
            'selectedBranchId' => 0,
            'canSwitchBranches' => true,
            'active' => $activeState,
        ]);
    }

    public function dashboard()
    {
        // Dashboard view for inventory_staff - shows summary and overview
        $session = session();
        if (!$session->has('role') || !$session->has('user_id')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first');
        }
        
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        
        // Only inventory_staff should access this dashboard
        if ($userRole !== 'inventory_staff') {
            return redirect()->to(site_url('dashboard'));
        }
        
        try {
            $db = \Config\Database::connect();
            
            // Get user's assigned branch
            $branch = $db->table('user_branches ub')
                ->select('b.branch_id, b.branch_name')
                ->join('branches b', 'b.branch_id = ub.branch_id')
                ->where('ub.user_id', $userId)
                ->orderBy('ub.user_branch_id', 'ASC')
                ->get()
                ->getRowArray();
            
            $branchId = $branch['branch_id'] ?? 0;
            $branchName = $branch['branch_name'] ?? 'Not Assigned';
            
            if ($branchId === 0) {
                return view('dashboard/inventory_staff_dashboard', [
                    'branchName' => $branchName,
                    'summary' => ['stock_value' => 0, 'low_stock_items' => 0, 'total_products' => 0],
                    'pendingReceives' => 0,
                    'lowStockItems' => [],
                    'recentMovements' => [],
                    'error' => 'No branch assigned to this user'
                ]);
            }
            
            // Get inventory summary
            $summary = $db->table('inventory i')
                ->select('SUM(i.current_stock * p.unit_price) as stock_value, COUNT(DISTINCT CASE WHEN i.available_stock <= p.minimum_stock THEN i.product_id END) as low_stock_items, COUNT(DISTINCT i.product_id) as total_products')
                ->join('products p', 'p.product_id = i.product_id')
                ->where('i.branch_id', $branchId)
                ->get()
                ->getRowArray();
            
            $summary = [
                'stock_value' => (float)($summary['stock_value'] ?? 0),
                'low_stock_items' => (int)($summary['low_stock_items'] ?? 0),
                'total_products' => (int)($summary['total_products'] ?? 0)
            ];
            
            // Pending receives (approved/ordered POs)
            $pendingReceives = $db->table('purchase_orders')
                ->where('branch_id', $branchId)
                ->whereIn('status', ['approved', 'ordered'])
                ->countAllResults();
            
            // Low stock items
            $lowStockItems = $db->table('inventory i')
                ->select('p.product_name, i.available_stock, p.minimum_stock')
                ->join('products p', 'p.product_id = i.product_id')
                ->where('i.branch_id', $branchId)
                ->where('i.available_stock <= p.minimum_stock')
                ->where('p.status', 'active')
                ->orderBy('p.product_name', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();
            
            // Recent stock movements
            $recentMovements = $db->table('stock_movements sm')
                ->select('sm.created_at, sm.movement_type, p.product_name, sm.quantity, sm.notes')
                ->join('products p', 'p.product_id = sm.product_id')
                ->where('sm.branch_id', $branchId)
                ->orderBy('sm.created_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();
            
            return view('dashboard/inventory_staff_dashboard', [
                'branchName' => $branchName,
                'summary' => $summary,
                'pendingReceives' => $pendingReceives,
                'lowStockItems' => $lowStockItems,
                'recentMovements' => $recentMovements,
                'error' => null
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Inventory Dashboard Error: ' . $e->getMessage());
            return view('dashboard/inventory_staff_dashboard', [
                'branchName' => 'Error',
                'summary' => ['stock_value' => 0, 'low_stock_items' => 0, 'total_products' => 0],
                'pendingReceives' => 0,
                'lowStockItems' => [],
                'recentMovements' => [],
                'error' => 'Error loading dashboard data'
            ]);
        }
    }

    public function index()
    {
        // Initialize database connection
        $db = \Config\Database::connect();
        
        // Get session data
        $session = session();
        if (!$session->has('role') || !$session->has('user_id')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first');
        }
        
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        
        // Check if user has access to inventory
        if (!in_array($userRole, ['inventory_staff', 'central_admin', 'system_admin', 'branch_manager', 'franchise_manager'])) {
            return redirect()->back()->with('error', 'You do not have permission to access this page');
        }
        
        // For inventory_staff, this is always the inventory page (not dashboard)
        $activeState = 'inventory';
        $requestedBranchRaw = $this->request->getGet('branch_id');
        $requestedBranchId = is_numeric($requestedBranchRaw) ? (int) $requestedBranchRaw : 0;
        $requestedAll = (is_string($requestedBranchRaw) && strtolower($requestedBranchRaw) === 'all');

        // Get all branches for dropdown
        $allBranches = [];
        try {
            $query = $db->table('branches')
                ->select('branch_id, branch_name')
                ->where('status', 'active');
            
            // For inventory staff, only show branches they're assigned to
            if ($userRole === 'inventory_staff') {
                $query->whereIn('branch_id', function($builder) use ($userId) {
                    return $builder->select('branch_id')
                        ->from('user_branches')
                        ->where('user_id', $userId);
                });
            }
            
            $allBranches = $query->orderBy('branch_name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching branches: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading branch data');
        }

        // Default query builder for inventory listing
        try {
            $builder = $db->table('inventory i')
                ->select('i.*, b.branch_name, p.product_id, p.product_name, p.unit_price, p.minimum_stock, 
                         (i.available_stock * p.unit_price) as stock_value')
                ->join('branches b', 'b.branch_id = i.branch_id', 'left')
                ->join('products p', 'p.product_id = i.product_id', 'left')
                ->where('i.available_stock >=', 0);
                
            // Apply branch filter if specified
            $branchFilter = null;
            if ($requestedBranchId > 0) {
                $builder->where('i.branch_id', $requestedBranchId);
                $branchFilter = $requestedBranchId;
            }
            
            // Get inventory data with error handling
            $inventory = $builder->get()->getResultArray();
            
            // If filtering by specific branch, ensure all products are shown
            if ($branchFilter) {
                $existingProductIds = array_column($inventory, 'product_id');
                $allProducts = $db->table('products')
                    ->select('product_id, product_name, unit_price, minimum_stock')
                    ->where('status', 'active')
                    ->whereNotIn('product_id', $existingProductIds ?: [0])
                    ->get()
                    ->getResultArray();
                
                // Add missing products with zero stock
                foreach ($allProducts as $product) {
                    $inventory[] = [
                        'inventory_id' => 0,
                        'product_id' => $product['product_id'],
                        'product_name' => $product['product_name'],
                        'unit_price' => $product['unit_price'],
                        'minimum_stock' => $product['minimum_stock'],
                        'branch_id' => $branchFilter,
                        'branch_name' => '',
                        'current_stock' => 0,
                        'available_stock' => 0,
                        'reserved_stock' => 0,
                        'stock_value' => 0
                    ];
                }
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error fetching inventory: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading inventory data: ' . $e->getMessage());
        }

        // Filter inventory based on user role
        if (in_array($userRole, ['inventory_staff', 'branch_manager', 'central_admin', 'system_admin', 'franchise_manager']) || $requestedBranchId > 0 || $requestedAll) {
            // Resolve effective branch ID
            $branchId = 0;
            
            // If staff/manager, enforce their own branch unless they have permission to view all
            if (in_array($userRole, ['inventory_staff', 'branch_manager'])) {
                $branchId = (int) ($session->get('branch_id') ?? 0);
                if ($branchId <= 0) {
                    $branch = $db->table('user_branches')
                        ->select('branch_id')
                        ->where('user_id', $userId)
                        ->orderBy('user_branch_id', 'ASC')
                        ->get()
                        ->getRowArray();
                    if (!$branch || !isset($branch['branch_id'])) {
                        return view('dashboard/inventory_staff', [
                            'lowStockItems' => [],
                            'branchInventory' => [],
                            'pendingReceives' => [],
                            'products' => [],
                            'branchName' => 'Unknown Branch',
                            'branches' => $allBranches,
                            'canSwitchBranches' => false,
                            'active' => $activeState,
                        ]);
                    }
                    $branchId = (int)$branch['branch_id'];
                }
            }
            // Allow switching via URL for central/system, inventory_staff, and franchise_manager
            if (in_array($userRole, ['central_admin','system_admin','inventory_staff','franchise_manager']) && ($requestedBranchId > 0 || $requestedAll)) {
                $branchId = $requestedBranchId;
                
                // If 'all' is selected, show aggregated view for inventory_staff
                if ($requestedAll && $userRole === 'inventory_staff') {
                    return $this->getAggregatedInventoryView($db, $allBranches, true, $activeState);
                }
            }

            if ($branchId <= 0 || $requestedAll) {
                $canSwitch = in_array($userRole, ['central_admin','system_admin','inventory_staff','franchise_manager']);
                
                // If no specific branch and not requesting all, show inventory staff their branch or central view
                if ($userRole === 'inventory_staff' && !$requestedAll) {
                    // Get the first branch if none selected
                    if ($branchId <= 0 && !empty($allBranches)) {
                        $branchId = (int)$allBranches[0]['branch_id'];
                    }
                    return $this->getBranchInventoryView($db, $branchId, $allBranches, $userRole, $userId, $activeState);
                }
                
                // For central admin, system admin, franchise manager or when viewing all branches
                if ($requestedAll || $userRole === 'central_admin' || $userRole === 'system_admin' || $userRole === 'franchise_manager') {
                    // Aggregated view for staff across all branches, but keep the same dashboard UI
                    $lowStockItems = $db->table('inventory i')
                        ->select('b.branch_name, p.product_name, i.available_stock, p.minimum_stock')
                        ->join('products p', 'p.product_id = i.product_id', 'left')
                        ->join('branches b', 'b.branch_id = i.branch_id', 'left')
                        ->where('i.available_stock <= p.minimum_stock')
                        ->orderBy('b.branch_name', 'ASC')
                        ->orderBy('p.product_name', 'ASC')
                        ->get()->getResultArray();

                    $pendingReceives = $db->table('purchase_orders po')
                        ->select('po.purchase_order_id, po.po_number, po.status, po.expected_delivery_date, s.supplier_name as supplier_name')
                        ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
                        ->whereIn('po.status', ['approved','ordered'])
                        ->orderBy('po.expected_delivery_date', 'ASC')
                        ->limit(10)
                        ->get()->getResultArray();

                    // Aggregated branchInventory by product across all branches
                    $branchInventory = $db->table('inventory i')
                        ->select('p.product_id, p.product_name, SUM(i.current_stock) as current_stock, SUM(i.available_stock) as available_stock, p.unit_price, SUM(i.current_stock * p.unit_price) as stock_value, p.minimum_stock')
                        ->join('products p', 'p.product_id = i.product_id', 'left')
                        ->groupBy('p.product_id')
                        ->orderBy('p.product_name', 'ASC')
                        ->get()->getResultArray();

                    $products = $db->table('products')
                        ->select('product_id, product_name, unit_price, minimum_stock')
                        ->orderBy('product_name', 'ASC')
                        ->get()->getResultArray();

                    return view('dashboard/inventory_staff', [
                        'lowStockItems' => $lowStockItems,
                        'pendingReceives' => $pendingReceives,
                        'products' => $products,
                        'branchInventory' => $branchInventory,
                        'expirySoon' => [],
                        'lowStockCount' => count($lowStockItems),
                        'branchName' => 'All Branches',
                        'branches' => $allBranches,
                        'selectedBranchId' => 0,
                        'canSwitchBranches' => $canSwitch,
                        'active' => $activeState,
                    ]);
                }

                // Default consolidated table view (central/system or unresolved)
                // Use LEFT JOIN to show all products even with zero stock
                $inventory = $db->table('products p')
                    ->select('p.product_id, p.product_name, p.unit_price, p.minimum_stock,
                             b.branch_id, b.branch_name,
                             COALESCE(i.inventory_id, 0) as inventory_id,
                             COALESCE(i.current_stock, 0) as current_stock,
                             COALESCE(i.available_stock, 0) as available_stock,
                             COALESCE(i.reserved_stock, 0) as reserved_stock,
                             (COALESCE(i.available_stock, 0) * p.unit_price) as stock_value')
                    ->join('inventory i', 'i.product_id = p.product_id', 'left')
                    ->join('branches b', 'b.branch_id = i.branch_id', 'left')
                    ->where('p.status', 'active')
                    ->orderBy('b.branch_name', 'ASC')
                    ->orderBy('p.product_name', 'ASC')
                    ->get()
                    ->getResultArray();
                
                // Get all products for the adjustment form
                $products = $db->table('products')
                    ->select('product_id, product_name, unit_price, minimum_stock')
                    ->where('status', 'active')
                    ->orderBy('product_name', 'ASC')
                    ->get()
                    ->getResultArray();
                    
                return view('dashboard/inventory', [
                    'inventory' => $inventory,
                    'branches' => $allBranches,
                    'products' => $products,
                    'active' => $activeState
                ]);
            }

            $builder->where('i.branch_id', $branchId);
            $branchMeta = $db->table('branches')->select('branch_name')->where('branch_id', $branchId)->get()->getRowArray();
            $canSwitch = in_array($userRole, ['central_admin','system_admin','inventory_staff','franchise_manager']);
            $allBranches = $canSwitch
                ? $db->table('branches')->select('branch_id, branch_name')->orderBy('branch_name','ASC')->get()->getResultArray()
                : [];

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
                ->select('po.purchase_order_id, po.po_number, po.status, po.expected_delivery_date, s.supplier_name as supplier_name')
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
                ->select('p.product_id, p.product_name, COALESCE(i.current_stock,0) as current_stock, COALESCE(i.available_stock,0) as available_stock, p.unit_price, (COALESCE(i.current_stock,0) * p.unit_price) as stock_value, p.minimum_stock')
                ->join('inventory i', 'i.product_id = p.product_id AND i.branch_id = '.(int)$branchId, 'left')
                ->where('p.status', 'active')
                ->orderBy('p.product_name', 'ASC')
                ->get()->getResultArray();

            // Fallback: if no rows returned (e.g., empty inventory table), build zero-stock rows from products list
            if (empty($branchInventory) && !empty($products)) {
                $branchInventory = array_map(static function($p) {
                    return [
                        'product_id'      => $p['product_id'],
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
                'branchName' => $branchMeta['branch_name'] ?? 'Branch',
                'branches' => $allBranches,
                'selectedBranchId' => $branchId,
                'canSwitchBranches' => $canSwitch,
                'active' => $activeState,
            ]);
        }

        // Default: central/system/others see consolidated inventory list
        // Show all products with their inventory across all branches
        $inventory = $db->table('products p')
            ->select('p.product_id, p.product_name, p.unit_price, p.minimum_stock,
                     b.branch_id, b.branch_name,
                     COALESCE(i.inventory_id, 0) as inventory_id,
                     COALESCE(i.current_stock, 0) as current_stock,
                     COALESCE(i.available_stock, 0) as available_stock,
                     COALESCE(i.reserved_stock, 0) as reserved_stock,
                     (COALESCE(i.available_stock, 0) * p.unit_price) as stock_value')
            ->join('inventory i', 'i.product_id = p.product_id', 'left')
            ->join('branches b', 'b.branch_id = i.branch_id', 'left')
            ->where('p.status', 'active')
            ->orderBy('p.product_name', 'ASC')
            ->orderBy('b.branch_name', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get all products for the adjustment form
        $products = $db->table('products')
            ->select('product_id, product_name, unit_price, minimum_stock')
            ->where('status', 'active')
            ->orderBy('product_name', 'ASC')
            ->get()
            ->getResultArray();
            
        return view('dashboard/inventory', [
            'inventory' => $inventory,
            'branches' => $allBranches,
            'products' => $products,
            'active' => $activeState
        ]);
    }

    public function adjust()
    {
        $session = session();
        $role = (string) ($session->get('role') ?? '');
        $userId = (int) ($session->get('user_id') ?? 0);
        if (!in_array($role, ['inventory_staff','branch_manager','central_admin','system_admin','franchise_manager'])) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $db = db_connect();
        
        // Get form data
        $productId = (int) $this->request->getPost('product_id');
        $qty = (int) $this->request->getPost('qty'); // negative to reduce
        $reason = trim((string) $this->request->getPost('reason'));
        $mode = trim((string) $this->request->getPost('mode')); // 'add' or 'adjust'
        
        // Set default reason if empty
        if (empty($reason)) {
            if ($mode === 'add') {
                $reason = 'Stock addition';
            } else {
                $reason = $qty > 0 ? 'Stock increase' : 'Stock reduction';
            }
        }
        
        // Determine staff branch (central/system may optionally post branch_id)
        $branchId = (int) ($this->request->getPost('branch_id') ?? 0);
        
        // If no branch_id provided, try to get from user's assignment
        if ($branchId === 0) {
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->orderBy('user_branch_id', 'ASC')
                ->get()->getRowArray();
            $branchId = (int)($branch['branch_id'] ?? 0);
        }

        // Build redirect URL for errors
        $redirectUrl = site_url('inventory');
        if ($branchId > 0) {
            $redirectUrl .= '?branch_id=' . $branchId;
        }
        if ($mode) {
            $redirectUrl .= ($branchId > 0 ? '&' : '?') . 'mode=' . $mode;
        }
        
        // Validation
        if ($productId <= 0) {
            return redirect()->to($redirectUrl)->with('error', 'Please select a valid product.');
        }
        
        if ($qty === 0) {
            return redirect()->to($redirectUrl)->with('error', 'Quantity cannot be zero.');
        }
        
        // If in "add" mode, ensure quantity is positive
        if ($mode === 'add' && $qty < 0) {
            return redirect()->to($redirectUrl)->with('error', 'Add Stock mode requires positive quantity. Use Adjust Stock for deductions.');
        }
        
        if ($branchId <= 0) {
            return redirect()->to($redirectUrl)->with('error', 'Please select a valid branch.');
        }
        
        // Verify product exists
        $product = $db->table('products')->where('product_id', $productId)->get()->getRowArray();
        if (!$product) {
            return redirect()->to($redirectUrl)->with('error', 'Product not found.');
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
        $unitPrice = (float)($product['unit_price'] ?? 0);
        
        try {
            $db->table('stock_movements')->insert([
                'product_id' => $productId,
                'branch_id' => $branchId,
                'movement_type' => $movementType,
                'quantity' => $qty,
                'reference_type' => 'adjustment',
                'reference_id' => null,
                'unit_price' => $unitPrice,
                'total_value' => $unitPrice * abs($qty),
                'notes' => $reason ?: 'Manual adjustment',
                'created_by' => $userId,
                'created_at' => $now,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error logging stock movement: ' . $e->getMessage());
        }

        // Determine success message based on mode and quantity
        $productName = $product['product_name'] ?? 'Product';
        if ($mode === 'add') {
            $message = "Stock added successfully! {$productName}: +{$qty} units.";
        } else {
            $actionText = $qty > 0 ? 'added' : 'reduced';
            $message = "Stock {$actionText} successfully! {$productName}: " . ($qty > 0 ? '+' : '') . $qty . " units.";
        }
        
        // Redirect back to inventory page (without mode parameter to show clean table)
        $successUrl = site_url('inventory');
        if ($branchId > 0) {
            $successUrl .= '?branch_id=' . $branchId;
        }
        
        return redirect()->to($successUrl)->with('success', $message);
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
                ->orderBy('user_branch_id', 'ASC')
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
