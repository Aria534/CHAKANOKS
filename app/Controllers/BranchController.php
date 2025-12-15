<?php 
namespace App\Controllers;
use App\Models\InventoryModel;

class BranchController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        // ✅ No join needed, since manager_name is stored directly in branches
        $branches = $db->table('branches b')
            ->select('b.branch_id, b.branch_name, b.manager_name, b.address, b.phone, b.email, b.status')
            ->get()
            ->getResultArray();

        return view('dashboard/shared/branches', ['branches' => $branches]);
    }

    public function dashboard()
    {
        // Role guard: only Branch Manager may access this page directly
        $role = (string) (session('role') ?? '');
        if ($role !== 'branch_manager') {
            return redirect()->to(site_url('dashboard'));
        }
        
        try {
            $db = db_connect();
            if (!$db) {
                throw new \RuntimeException('Could not connect to database');
            }
            
            $userId = session()->get('user_id');
            if (!$userId) {
                throw new \RuntimeException('User not logged in');
            }

            $branch = $db->table('user_branches ub')
            ->select('b.branch_id, b.branch_name, b.manager_name')
            ->join('branches b', 'b.branch_id = ub.branch_id')
            ->where('ub.user_id', $userId)
            ->orderBy('ub.user_branch_id', 'ASC')
            ->get()
            ->getRowArray();

            // Default branch_id if none found
            $branchId = $branch['branch_id'] ?? 0;
            if ($branchId === 0) {
                return view('dashboard/branch_manager/branch_manager', [
                    'branch' => ['branch_name' => 'Not Assigned', 'manager_name' => session()->get('username')],
                    'summary' => ['stock_value' => 0, 'low_stock_items' => 0],
                    'pendingPOs' => 0,
                    'poCounts' => ['pending'=>0,'approved'=>0,'delivered'=>0],
                    'lowStockItems' => [],
                    'recentMovements' => [],
                    'error' => 'No branch assigned to this user'
                ]);
            }

            // Get summary from inventory
            $inventoryModel = new InventoryModel();
            $summary = $inventoryModel->getBranchSummary($branchId);
            
            if (!$summary) {
                $summary = ['stock_value' => 0, 'low_stock_items' => 0];
            }

            // Pending Purchase Orders
            $pendingPOs = $db->table('purchase_orders')
                ->where('branch_id', $branchId)
                ->where('status', 'pending')
                ->countAllResults();

            // Purchase Orders count by status
            $poCounts = $db->table('purchase_orders')
                ->select("status, COUNT(*) as count")
                ->where('branch_id', $branchId)
                ->groupBy('status')
                ->get()
                ->getResultArray();

            $poCountsArr = ['pending'=>0, 'approved'=>0, 'delivered'=>0];
            if ($poCounts) {
                foreach ($poCounts as $po) {
                    if (isset($po['status']) && array_key_exists($po['status'], $poCountsArr)) {
                        $poCountsArr[$po['status']] = (int)$po['count'];
                    }
                }
            }

            // Low Stock Items
            $lowStockItems = [];
            try {
                $lowStockItems = $db->table('inventory i')
                    ->select('p.product_name, i.available_stock, p.minimum_stock')
                    ->join('products p', 'p.product_id = i.product_id')
                    ->where('i.branch_id', $branchId)
                    ->where('i.available_stock <= p.minimum_stock')
                    ->get()
                    ->getResultArray();
            } catch (\Exception $e) {
                log_message('error', 'Error fetching low stock items: ' . $e->getMessage());
            }

            // Recent Stock Movements
            $recentMovements = [];
            try {
                $recentMovements = $db->table('stock_movements sm')
                    ->select('sm.created_at, sm.movement_type, p.product_name, sm.quantity')
                    ->join('products p', 'p.product_id = sm.product_id')
                    ->where('sm.branch_id', $branchId)
                    ->orderBy('sm.created_at', 'DESC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();
            } catch (\Exception $e) {
                log_message('error', 'Error fetching recent stock movements: ' . $e->getMessage());
            }

            return view('dashboard/branch_manager/branch_manager', [
                'branch' => $branch,
                'summary' => $summary,
                'pendingPOs' => $pendingPOs,
                'poCounts' => $poCountsArr,
                'lowStockItems' => $lowStockItems,
                'recentMovements' => $recentMovements,
                'error' => null
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in BranchController::dashboard: ' . $e->getMessage());
            
            return view('dashboard/branch_manager/branch_manager', [
                'branch' => ['branch_name' => 'Error', 'manager_name' => 'Error'],
                'summary' => ['stock_value' => 0, 'low_stock_items' => 0],
                'pendingPOs' => 0,
                'poCounts' => ['pending'=>0,'approved'=>0,'delivered'=>0],
                'lowStockItems' => [],
                'recentMovements' => [],
                'error' => 'An error occurred while loading the dashboard. Please try again later.'
            ]);
        }
    }
}
