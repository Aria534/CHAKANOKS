<?php 
namespace App\Controllers;
use App\Models\InventoryModel;

class BranchDashboard extends BaseController {
    public function index() {
        $db = db_connect();

        // Kunin user ID galing session
       $userId = session()->get('user_id');

$branch = $db->table('user_branches ub')
    ->select('b.branch_id, b.branch_name, b.manager_name')
    ->join('branches b', 'b.branch_id = ub.branch_id')
    ->where('ub.user_id', $userId)
    ->where('ub.is_primary', 1)
    ->get()
    ->getRowArray();


        // Default branch_id (para safe kung wala makita)
        $branchId = $branch['branch_id'] ?? 0;

        // Get summary from inventory
        $inventoryModel = new InventoryModel();
        $summary = $inventoryModel->getBranchSummary($branchId);

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
        foreach ($poCounts as $po) {
            $poCountsArr[$po['status']] = $po['count'];
        }

        // Low Stock Items
        $lowStockItems = $db->table('inventory i')
            ->select('p.product_name, i.available_stock, p.minimum_stock')
            ->join('products p', 'p.product_id = i.product_id')
            ->where('i.branch_id', $branchId)
            ->where('i.available_stock <= p.minimum_stock')
            ->get()
            ->getResultArray();

        // Recent Stock Movements
        $recentMovements = $db->table('stock_movements sm')
            ->select('sm.created_at, sm.movement_type, p.product_name, sm.quantity')
            ->join('products p', 'p.product_id = sm.product_id')
            ->where('sm.branch_id', $branchId)
            ->orderBy('sm.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('dashboard/branch_manager', [
            'branch' => $branch,
            'summary' => $summary ?? ['stock_value' => 0, 'low_stock_items' => 0],
            'pendingPOs' => $pendingPOs ?? 0,
            'poCounts' => $poCountsArr,
            'lowStockItems' => $lowStockItems,
            'recentMovements' => $recentMovements
        ]);
    }
}
