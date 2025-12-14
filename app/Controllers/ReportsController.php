<?php

namespace App\Controllers;

class ReportsController extends BaseController
{
    public function index()
    {
        $role = (string)(session('role') ?? '');
        if (empty($role)) {
            return redirect()->to(site_url('login'));
        }

        $data = [
            'title' => 'Reports',
            'active' => 'reports'
        ];

        return view('dashboard/reports', $data);
    }

    public function generate($type = 'inventory')
    {
        $role = (string)(session('role') ?? '');
        $userId = (int)(session('user_id') ?? 0);
        
        if (empty($role)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        // Get date range from request
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        
        $db = \Config\Database::connect();
        
        // Get branch ID for branch managers
        $branchId = null;
        if ($role === 'branch_manager') {
            $branch = $db->table('user_branches')
                ->select('branch_id')
                ->where('user_id', $userId)
                ->orderBy('user_branch_id', 'ASC')
                ->get()
                ->getRowArray();
            $branchId = (int)($branch['branch_id'] ?? 0);
            
            if ($branchId <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'No branch assigned to this manager'
                ]);
            }
        }
        
        $reportData = [
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('Y-m-d H:i:s'),
            'data' => [],
            'summary' => []
        ];

        try {
            switch ($type) {
                case 'inventory':
                    $reportData = $this->generateInventoryReport($db, $startDate, $endDate, $branchId);
                    break;
                    
                case 'sales':
                    $reportData = $this->generateSalesReport($db, $startDate, $endDate, $branchId);
                    break;
                    
                case 'orders':
                    $reportData = $this->generateOrdersReport($db, $startDate, $endDate, $branchId);
                    break;
                    
                default:
                    $reportData['error'] = 'Invalid report type';
            }
            
            $reportData['success'] = true;
            
        } catch (\Exception $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            $reportData['success'] = false;
            $reportData['error'] = 'Error generating report: ' . $e->getMessage();
        }

        // Return JSON response for AJAX requests
        return $this->response->setJSON($reportData);
    }

    private function generateInventoryReport($db, $startDate, $endDate, $branchId = null)
    {
        // Get inventory data with product details
        $builder = $db->table('products p')
            ->select('p.product_id, p.product_name, p.product_code, p.unit_price, p.minimum_stock,
                     b.branch_name,
                     COALESCE(i.current_stock, 0) as current_stock,
                     COALESCE(i.available_stock, 0) as available_stock,
                     (COALESCE(i.current_stock, 0) * p.unit_price) as stock_value')
            ->join('inventory i', 'i.product_id = p.product_id', 'left')
            ->join('branches b', 'b.branch_id = i.branch_id', 'left')
            ->where('p.status', 'active');
        
        // Filter by branch for branch managers
        if ($branchId) {
            $builder->where('i.branch_id', $branchId);
        }
        
        $inventory = $builder
            ->orderBy('b.branch_name', 'ASC')
            ->orderBy('p.product_name', 'ASC')
            ->get()
            ->getResultArray();

        // Calculate summary
        $totalValue = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;
        
        foreach ($inventory as &$item) {
            $totalValue += (float)$item['stock_value'];
            
            // Determine status
            if ($item['current_stock'] == 0) {
                $item['status'] = 'Out of Stock';
                $outOfStockCount++;
            } elseif ($item['current_stock'] <= $item['minimum_stock']) {
                $item['status'] = 'Low Stock';
                $lowStockCount++;
            } else {
                $item['status'] = 'In Stock';
            }
        }

        $branchName = '';
        if ($branchId && !empty($inventory)) {
            $branchName = $inventory[0]['branch_name'] ?? '';
        }

        return [
            'type' => 'inventory',
            'title' => $branchName ? "Inventory Report - $branchName" : 'Inventory Report',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('Y-m-d H:i:s'),
            'branch_filtered' => $branchId ? true : false,
            'data' => $inventory,
            'summary' => [
                'total_products' => count($inventory),
                'total_value' => $totalValue,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount
            ]
        ];
    }

    private function generateSalesReport($db, $startDate, $endDate, $branchId = null)
    {
        // Get stock movements (outgoing) within date range
        $builder = $db->table('stock_movements sm')
            ->select('sm.created_at as date, sm.reference_id as order_id, 
                     p.product_name, sm.quantity, sm.total_value,
                     b.branch_name')
            ->join('products p', 'p.product_id = sm.product_id', 'left')
            ->join('branches b', 'b.branch_id = sm.branch_id', 'left')
            ->where('sm.movement_type', 'out')
            ->where('DATE(sm.created_at) >=', $startDate)
            ->where('DATE(sm.created_at) <=', $endDate);
        
        // Filter by branch for branch managers
        if ($branchId) {
            $builder->where('sm.branch_id', $branchId);
        }
        
        $sales = $builder
            ->orderBy('sm.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Calculate summary
        $totalSales = 0;
        $totalQuantity = 0;
        
        foreach ($sales as $sale) {
            $totalSales += (float)$sale['total_value'];
            $totalQuantity += (int)$sale['quantity'];
        }

        $branchName = '';
        if ($branchId && !empty($sales)) {
            $branchName = $sales[0]['branch_name'] ?? '';
        }

        return [
            'type' => 'sales',
            'title' => $branchName ? "Sales Report - $branchName" : 'Sales Report',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('Y-m-d H:i:s'),
            'branch_filtered' => $branchId ? true : false,
            'data' => $sales,
            'summary' => [
                'total_transactions' => count($sales),
                'total_revenue' => $totalSales,
                'total_quantity_sold' => $totalQuantity
            ]
        ];
    }

    private function generateOrdersReport($db, $startDate, $endDate, $branchId = null)
    {
        // Get purchase orders within date range
        $builder = $db->table('purchase_orders po')
            ->select('po.po_number, po.status, po.requested_date, po.approved_date, 
                     po.actual_delivery_date, po.total_amount,
                     b.branch_name, s.supplier_name,
                     u.username as requested_by')
            ->join('branches b', 'b.branch_id = po.branch_id', 'left')
            ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
            ->join('users u', 'u.user_id = po.requested_by', 'left')
            ->where('DATE(po.requested_date) >=', $startDate)
            ->where('DATE(po.requested_date) <=', $endDate);
        
        // Filter by branch for branch managers
        if ($branchId) {
            $builder->where('po.branch_id', $branchId);
        }
        
        $orders = $builder
            ->orderBy('po.requested_date', 'DESC')
            ->get()
            ->getResultArray();

        // Calculate summary by status
        $statusCount = [
            'pending' => 0,
            'approved' => 0,
            'ordered' => 0,
            'delivered' => 0,
            'cancelled' => 0
        ];
        $totalAmount = 0;
        
        foreach ($orders as $order) {
            $status = strtolower($order['status']);
            if (isset($statusCount[$status])) {
                $statusCount[$status]++;
            }
            $totalAmount += (float)$order['total_amount'];
        }

        $branchName = '';
        if ($branchId && !empty($orders)) {
            $branchName = $orders[0]['branch_name'] ?? '';
        }

        return [
            'type' => 'orders',
            'title' => $branchName ? "Purchase Orders Report - $branchName" : 'Purchase Orders Report',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('Y-m-d H:i:s'),
            'branch_filtered' => $branchId ? true : false,
            'data' => $orders,
            'summary' => [
                'total_orders' => count($orders),
                'total_amount' => $totalAmount,
                'status_breakdown' => $statusCount
            ]
        ];
    }

    public function exportPdf($type = 'inventory')
    {
        // This is a placeholder for PDF export functionality
        // You would typically use a library like TCPDF or mPDF
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'PDF export functionality would be implemented here',
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
}
