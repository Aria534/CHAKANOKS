<?php 
namespace App\Controllers;

class CentralDashboard extends BaseController {
    public function index() {
        $role = (string) (session('role') ?? '');
        if (!in_array($role, ['central_admin','system_admin'])) {
            return redirect()->to(site_url('dashboard'));
        }
        $db = db_connect();

        // Total Stock Value
        $stockValue = $db->table('inventory i')
            ->select('SUM(i.current_stock * p.unit_price) as total_value')
            ->join('products p', 'p.product_id = i.product_id')
            ->get()
            ->getRowArray();

        // Low Stock Alerts (uses available_stock vs product minimum)
        $lowStock = $db->table('inventory i')
            ->select('COUNT(*) as low_count')
            ->join('products p', 'p.product_id = i.product_id')
            ->where('i.available_stock <= p.minimum_stock')
            ->get()
            ->getRowArray();

        // Pending Orders
        $pendingOrders = $db->table('purchase_orders')
            ->where('status', 'pending')
            ->countAllResults();

        // Pending Orders List (latest 5)
        $pendingOrdersList = $db->table('purchase_orders po')
            ->select('po.po_number, po.requested_date, b.branch_name')
            ->join('branches b', 'b.branch_id = po.branch_id', 'left')
            ->where('po.status', 'pending')
            ->orderBy('po.requested_date', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Stock per category (for Donut Chart)
        $categoryStats = $db->table('inventory i')
            ->select('c.category_name, SUM(i.current_stock) as qty')
            ->join('products p', 'p.product_id = i.product_id')
            ->join('categories c', 'c.category_id = p.category_id')
            ->groupBy('c.category_name')
            ->get()
            ->getResultArray();

        // Orders trend per month (for Line Chart)
        $ordersTrend = $db->table('purchase_orders')
            ->select("MONTH(created_at) as month, COUNT(*) as total")
            ->where('YEAR(created_at)', date('Y'))
            ->groupBy('MONTH(created_at)')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();

        // Low stock list across branches (top 10)
        $lowStockList = $db->table('inventory i')
            ->select('b.branch_name, p.product_name, i.available_stock, p.minimum_stock')
            ->join('branches b', 'b.branch_id = i.branch_id')
            ->join('products p', 'p.product_id = i.product_id')
            ->where('i.available_stock <= p.minimum_stock')
            ->orderBy('b.branch_name', 'ASC')
            ->orderBy('p.product_name', 'ASC')
            ->limit(10)
            ->get()
            ->getResultArray();
        // Pass data to view
        return view('dashboard/central_admin', [
            'stockValue'    => $stockValue['total_value'] ?? 0,
            'lowStock'      => $lowStock['low_count'] ?? 0,
            'pendingOrders' => $pendingOrders ?? 0,
            'categoryStats' => $categoryStats ?: [],
            'ordersTrend'   => $ordersTrend ?: [],
            'lowStockList'  => $lowStockList ?: [],
            'pendingOrdersList' => $pendingOrdersList ?: [],
        ]);
    }
}
