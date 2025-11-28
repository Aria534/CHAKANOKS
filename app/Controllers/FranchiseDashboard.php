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
}