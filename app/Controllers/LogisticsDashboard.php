<?php

namespace App\Controllers;

class LogisticsDashboard extends BaseController
{
    protected $db;
    
    public function __construct()
    {
        try {
            $this->db = \Config\Database::connect();
        } catch (\Exception $e) {
            log_message('error', 'Database connection error in LogisticsDashboard: ' . $e->getMessage());
            throw $e;
        }
    }

    private function checkLogisticsRole()
    {
        $role = (string) (session('role') ?? '');
        if ($role !== 'logistics_coordinator') {
            return redirect()->to(site_url('dashboard'));
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkLogisticsRole()) {
            return $redirect;
        }

        try {
            // Get statistics from purchase_orders
            $totalShipments = $this->db->table('purchase_orders')
                ->whereIn('status', ['approved', 'ordered', 'delivered'])
                ->countAllResults();

            $pendingDeliveries = $this->db->table('purchase_orders')
                ->whereIn('status', ['approved', 'ordered'])
                ->countAllResults();

            $deliveredThisMonth = $this->db->table('purchase_orders')
                ->where('status', 'delivered')
                ->where('MONTH(actual_delivery_date)', date('m'))
                ->where('YEAR(actual_delivery_date)', date('Y'))
                ->countAllResults();

            // Get recent shipments with branch and supplier info
            $recentShipments = $this->db->table('purchase_orders po')
                ->select('po.*, b.branch_name, s.supplier_name')
                ->join('branches b', 'b.branch_id = po.branch_id', 'left')
                ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
                ->whereIn('po.status', ['approved', 'ordered', 'delivered'])
                ->orderBy('po.created_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            // Get status distribution for chart
            $statusDistribution = $this->db->table('purchase_orders')
                ->select('status, COUNT(*) as count')
                ->whereIn('status', ['approved', 'ordered', 'delivered'])
                ->groupBy('status')
                ->get()
                ->getResultArray();

            // Get route efficiency (orders by branch)
            $routeEfficiency = $this->db->table('purchase_orders po')
                ->select('b.branch_name, COUNT(*) as order_count')
                ->join('branches b', 'b.branch_id = po.branch_id', 'left')
                ->whereIn('po.status', ['approved', 'ordered', 'delivered'])
                ->groupBy('b.branch_name')
                ->orderBy('order_count', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $data = [
                'title' => 'Logistics Dashboard',
                'active' => 'logistics',
                'totalShipments' => $totalShipments,
                'pendingDeliveries' => $pendingDeliveries,
                'deliveredThisMonth' => $deliveredThisMonth,
                'recentShipments' => $recentShipments,
                'statusDistribution' => $statusDistribution,
                'routeEfficiency' => $routeEfficiency,
            ];

            return view('dashboard/logistics_coordinator/logistics_dashboard', $data);
        } catch (\Exception $e) {
            log_message('error', 'Logistics Dashboard Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading logistics dashboard');
        }
    }

    public function shipments()
    {
        if ($redirect = $this->checkLogisticsRole()) {
            return $redirect;
        }

        try {
            // Get all shipments (approved and ordered POs)
            $shipments = $this->db->table('purchase_orders po')
                ->select('po.*, b.branch_name, s.supplier_name, u.username as requested_by_name')
                ->join('branches b', 'b.branch_id = po.branch_id', 'left')
                ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
                ->join('users u', 'u.user_id = po.requested_by', 'left')
                ->whereIn('po.status', ['approved', 'ordered', 'delivered'])
                ->orderBy('po.expected_delivery_date', 'ASC')
                ->get()
                ->getResultArray();

            $data = [
                'title' => 'Shipments Management',
                'active' => 'shipments',
                'shipments' => $shipments
            ];

            return view('dashboard/logistics_coordinator/shipments', $data);
        } catch (\Exception $e) {
            log_message('error', 'Shipments Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading shipments');
        }
    }

    public function routes()
    {
        if ($redirect = $this->checkLogisticsRole()) {
            return $redirect;
        }

        try {
            // Get routes grouped by branch with pending deliveries
            $routes = $this->db->table('purchase_orders po')
                ->select('b.branch_id, b.branch_name, b.address, b.phone,
                         COUNT(po.purchase_order_id) as pending_orders,
                         MIN(po.expected_delivery_date) as earliest_delivery')
                ->join('branches b', 'b.branch_id = po.branch_id', 'left')
                ->whereIn('po.status', ['approved', 'ordered'])
                ->groupBy('b.branch_id')
                ->orderBy('earliest_delivery', 'ASC')
                ->get()
                ->getResultArray();

            // Get individual orders for each route
            if (!empty($routes)) {
                foreach ($routes as &$route) {
                    $route['orders'] = $this->db->table('purchase_orders po')
                        ->select('po.*, s.supplier_name')
                        ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
                        ->where('po.branch_id', $route['branch_id'])
                        ->whereIn('po.status', ['approved', 'ordered'])
                        ->orderBy('po.expected_delivery_date', 'ASC')
                        ->get()
                        ->getResultArray();
                }
            }

            $data = [
                'title' => 'Delivery Routes',
                'active' => 'routes',
                'routes' => $routes
            ];

            return view('dashboard/logistics_coordinator/routes', $data);
        } catch (\Exception $e) {
            log_message('error', 'Routes Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error loading routes: ' . $e->getMessage());
        }
    }

    public function updateDeliveryStatus($orderId)
    {
        if ($redirect = $this->checkLogisticsRole()) {
            return $redirect;
        }

        if (!$this->request->isAJAX() && !$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $status = $this->request->getPost('status');
            $expectedDate = $this->request->getPost('expected_delivery_date');
            $actualDate = $this->request->getPost('actual_delivery_date');

            $updateData = ['updated_at' => date('Y-m-d H:i:s')];

            if ($status && in_array($status, ['approved', 'ordered', 'delivered'])) {
                $updateData['status'] = $status;
            }

            if ($expectedDate) {
                $updateData['expected_delivery_date'] = $expectedDate;
            }

            if ($actualDate || $status === 'delivered') {
                $updateData['actual_delivery_date'] = $actualDate ?: date('Y-m-d');
            }

            $this->db->table('purchase_orders')
                ->where('purchase_order_id', $orderId)
                ->update($updateData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Delivery status updated successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Update Delivery Status Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating delivery status: ' . $e->getMessage()
            ]);
        }
    }

    public function trackShipment($orderId)
    {
        if ($redirect = $this->checkLogisticsRole()) {
            return $redirect;
        }

        try {
            $shipment = $this->db->table('purchase_orders po')
                ->select('po.*, b.branch_name, b.address, b.phone as contact_number,
                         s.supplier_name, s.contact_person, s.phone as supplier_phone,
                         u.username as requested_by_name')
                ->join('branches b', 'b.branch_id = po.branch_id', 'left')
                ->join('suppliers s', 's.supplier_id = po.supplier_id', 'left')
                ->join('users u', 'u.user_id = po.requested_by', 'left')
                ->where('po.purchase_order_id', $orderId)
                ->get()
                ->getRowArray();

            if (!$shipment) {
                return redirect()->back()->with('error', 'Shipment not found');
            }

            // Get items for this order
            $items = $this->db->table('purchase_order_items poi')
                ->select('poi.*, p.product_name, p.product_code')
                ->join('products p', 'p.product_id = poi.product_id', 'left')
                ->where('poi.purchase_order_id', $orderId)
                ->get()
                ->getResultArray();

            $data = [
                'title' => 'Track Shipment - ' . $shipment['po_number'],
                'active' => 'shipments',
                'shipment' => $shipment,
                'items' => $items
            ];

            return view('dashboard/logistics_coordinator/track_shipment', $data);
        } catch (\Exception $e) {
            log_message('error', 'Track Shipment Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error tracking shipment');
        }
    }
}
