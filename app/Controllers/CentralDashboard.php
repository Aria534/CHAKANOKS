<?php 
namespace App\Controllers;

class CentralDashboard extends BaseController {
    public function index() {
        $db = db_connect();

        // Total Stock Value
        $stockValue = $db->table('inventory i')
            ->select('SUM(i.current_stock * p.unit_price) as total_value')
            ->join('products p', 'p.product_id = i.product_id')
            ->get()
            ->getRowArray();

        // Low Stock Alerts (siguraduhin tama column name sa DB mo: current_stock o available_stock)
        $lowStock = $db->table('inventory i')
            ->select('COUNT(*) as low_count')
            ->join('products p', 'p.product_id = i.product_id')
            ->where('i.current_stock <= p.minimum_stock')
            ->get()
            ->getRowArray();

        // Pending Orders
        $pendingOrders = $db->table('purchase_orders')
            ->where('status', 'pending')
            ->countAllResults();

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

        // Pass data to view
        return view('dashboard/central_admin', [
            'stockValue'    => $stockValue['total_value'] ?? 0,
            'lowStock'      => $lowStock['low_count'] ?? 0,
            'pendingOrders' => $pendingOrders ?? 0,
            'categoryStats' => $categoryStats ?: [],
            'ordersTrend'   => $ordersTrend ?: []
        ]);
    }
}
