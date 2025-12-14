<?php

namespace App\Controllers;

class FranchiseDashboard extends BaseController
{
    public function index()
    {
        $role = (string) (session('role') ?? '');
        if ($role !== 'franchise_manager') {
            return redirect()->to(site_url('dashboard'));
        }

        try {
            $db = \Config\Database::connect();

            $totalBranches = $db->table('branches')
                ->where('status', 'active')
                ->countAllResults();

            $totalInventoryValue = $db->table('inventory i')
                ->select('SUM(i.current_stock * p.unit_price) as total_value')
                ->join('products p', 'p.product_id = i.product_id')
                ->get()
                ->getRowArray();

            $totalOrders = $db->table('purchase_orders')
                ->countAllResults();

            $pendingOrders = $db->table('purchase_orders')
                ->where('status', 'pending')
                ->countAllResults();

            $branchPerformance = $db->table('branches b')
                ->select('b.branch_id, b.branch_name, COUNT(po.purchase_order_id) as order_count, SUM(i.current_stock * p.unit_price) as inventory_value')
                ->join('purchase_orders po', 'po.branch_id = b.branch_id', 'left')
                ->join('inventory i', 'i.branch_id = b.branch_id', 'left')
                ->join('products p', 'p.product_id = i.product_id', 'left')
                ->where('b.status', 'active')
                ->groupBy('b.branch_id, b.branch_name')
                ->get()
                ->getResultArray();

            $categoryDistribution = $db->table('inventory i')
                ->select('c.category_name, SUM(i.current_stock) as qty, SUM(i.current_stock * p.unit_price) as value')
                ->join('products p', 'p.product_id = i.product_id')
                ->join('categories c', 'c.category_id = p.category_id')
                ->groupBy('c.category_name')
                ->get()
                ->getResultArray();

            $recentOrders = $db->table('purchase_orders po')
                ->select('po.po_number, po.status, po.requested_date, b.branch_name, s.supplier_name')
                ->join('branches b', 'b.branch_id = po.branch_id', 'left')
                ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
                ->orderBy('po.created_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            return view('dashboard/franchise_dashboard', [
                'totalBranches' => $totalBranches,
                'totalInventoryValue' => $totalInventoryValue['total_value'] ?? 0,
                'totalOrders' => $totalOrders,
                'pendingOrders' => $pendingOrders,
                'branchPerformance' => $branchPerformance,
                'categoryDistribution' => $categoryDistribution,
                'recentOrders' => $recentOrders,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Franchise Dashboard Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading franchise data');
        }
    }
    
    public function exportBranchPerformance()
    {
        $role = (string) (session('role') ?? '');
        if ($role !== 'franchise_manager') {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized access'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Get branch performance data
            $branchPerformance = $db->table('branches b')
                ->select('b.branch_name, 
                         COUNT(po.purchase_order_id) as order_count, 
                         COALESCE(SUM(i.current_stock * p.unit_price), 0) as inventory_value')
                ->join('purchase_orders po', 'po.branch_id = b.branch_id', 'left')
                ->join('inventory i', 'i.branch_id = b.branch_id', 'left')
                ->join('products p', 'p.product_id = i.product_id', 'left')
                ->where('b.status', 'active')
                ->groupBy('b.branch_id, b.branch_name')
                ->get()
                ->getResultArray();

            // Set headers for CSV download
            $filename = 'branch_performance_' . date('Y-m-d') . '.csv';
            
            // Create output buffer
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($output, "\xEF\xBB\xBF");
            
            // Add headers
            fputcsv($output, ['Branch Name', 'Order Count', 'Inventory Value (₱)']);
            
            // Add data rows
            foreach ($branchPerformance as $branch) {
                fputcsv($output, [
                    $branch['branch_name'] ?? 'N/A',
                    $branch['order_count'] ?? 0,
                    number_format($branch['inventory_value'] ?? 0, 2)
                ]);
            }
            
            // Set the headers
            $response = service('response');
            $response->setHeader('Content-Type', 'text/csv; charset=utf-8');
            $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', '0');
            
            // Send the file
            $response->setBody(ob_get_clean());
            return $response;
            
        } catch (\Exception $e) {
            log_message('error', 'Export Branch Performance Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error exporting branch performance data: ' . $e->getMessage()
            ]);
        }
    }
}