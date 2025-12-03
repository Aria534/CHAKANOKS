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
                    $reportData = $this->generateInventoryReport($db, $startDate, $endDate);
                    break;
                    
                case 'sales':
                    $reportData = $this->generateSalesReport($db, $startDate, $endDate);
                    break;
                    
                case 'orders':
                    $reportData = $this->generateOrdersReport($db, $startDate, $endDate);
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

    private function generateInventoryReport($db, $startDate, $endDate)
    {
        // Get inventory data with product details
        $inventory = $db->table('products p')
            ->select('p.product_id, p.product_name, p.product_code, p.unit_price, p.minimum_stock,
                     b.branch_name,
                     COALESCE(i.current_stock, 0) as current_stock,
                     COALESCE(i.available_stock, 0) as available_stock,
                     (COALESCE(i.current_stock, 0) * p.unit_price) as stock_value')
            ->join('inventory i', 'i.product_id = p.product_id', 'left')
            ->join('branches b', 'b.branch_id = i.branch_id', 'left')
            ->where('p.status', 'active')
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

        return [
            'type' => 'inventory',
            'title' => 'Inventory Report',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('Y-m-d H:i:s'),
            'data' => $inventory,
            'summary' => [
                'total_products' => count($inventory),
                'total_value' => $totalValue,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount
            ]
        ];
    }

    private function generateSalesReport($db, $startDate, $endDate)
    {
        // Get stock movements (outgoing) within date range
        $sales = $db->table('stock_movements sm')
            ->select('sm.created_at as date, sm.reference_id as order_id, 
                     p.product_name, sm.quantity, sm.total_value,
                     b.branch_name')
            ->join('products p', 'p.product_id = sm.product_id', 'left')
            ->join('branches b', 'b.branch_id = sm.branch_id', 'left')
            ->where('sm.movement_type', 'out')
            ->where('DATE(sm.created_at) >=', $startDate)
            ->where('DATE(sm.created_at) <=', $endDate)
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

        return [
            'type' => 'sales',
            'title' => 'Sales Report',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('Y-m-d H:i:s'),
            'data' => $sales,
            'summary' => [
                'total_transactions' => count($sales),
                'total_revenue' => $totalSales,
                'total_quantity_sold' => $totalQuantity
            ]
        ];
    }

    private function generateOrdersReport($db, $startDate, $endDate)
    {
        // Get purchase orders within date range
        $orders = $db->table('purchase_orders po')
            ->select('po.po_number, po.status, po.requested_date, po.approved_date, 
                     po.actual_delivery_date, po.total_amount,
                     b.branch_name, s.supplier_name,
                     u.username as requested_by')
            ->join('branches b', 'b.branch_id = po.branch_id', 'left')
            ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
            ->join('users u', 'u.user_id = po.requested_by', 'left')
            ->where('DATE(po.requested_date) >=', $startDate)
            ->where('DATE(po.requested_date) <=', $endDate)
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

        return [
            'type' => 'orders',
            'title' => 'Purchase Orders Report',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => date('Y-m-d H:i:s'),
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
