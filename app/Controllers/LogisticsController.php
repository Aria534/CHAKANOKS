<?php
namespace App\Controllers;

class LogisticsController extends BaseController
{
    public function index()
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        
        if ($role !== 'logistics_coordinator') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $db = db_connect();
        
        // Get approved orders ready for assignment
        $approvedOrders = $db->table('purchase_orders po')
            ->select('po.*, s.supplier_name, GROUP_CONCAT(p.product_name SEPARATOR ", ") as items')
            ->join('suppliers s', 's.supplier_id = po.supplier_id')
            ->join('purchase_order_items poi', 'poi.purchase_order_id = po.purchase_order_id')
            ->join('products p', 'p.product_id = poi.product_id')
            ->where('po.status', 'approved')
            ->groupBy('po.purchase_order_id')
            ->orderBy('po.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get assigned shipments
        $shipments = $db->table('shipments sh')
            ->select('sh.*, po.po_number, d.driver_name, d.phone_number, b.branch_name')
            ->join('purchase_orders po', 'po.purchase_order_id = sh.purchase_order_id')
            ->join('drivers d', 'd.driver_id = sh.driver_id', 'left')
            ->join('branches b', 'b.branch_id = po.branch_id')
            ->orderBy('sh.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get available drivers
        $drivers = $db->table('drivers')
            ->where('status', 'active')
            ->get()
            ->getResultArray();

        return view('dashboard/logistics_orders', [
            'approvedOrders' => $approvedOrders,
            'shipments' => $shipments,
            'drivers' => $drivers
        ]);
    }

    public function assignDriver()
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        
        if ($role !== 'logistics_coordinator') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $purchaseOrderId = (int)$this->request->getPost('purchase_order_id');
        $driverId = (int)$this->request->getPost('driver_id');
        $estimatedDelivery = $this->request->getPost('estimated_delivery');

        if (!$purchaseOrderId || !$driverId) {
            return redirect()->back()->with('error', 'Invalid order or driver');
        }

        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        // Create shipment record
        $db->table('shipments')->insert([
            'purchase_order_id' => $purchaseOrderId,
            'driver_id' => $driverId,
            'status' => 'assigned',
            'estimated_delivery_date' => $estimatedDelivery,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // Update order status to in_transit
        $db->table('purchase_orders')
            ->where('purchase_order_id', $purchaseOrderId)
            ->update([
                'status' => 'in_transit',
                'updated_at' => $now
            ]);

        return redirect()->to(site_url('logistics/orders'))->with('success', 'Driver assigned successfully!');
    }

    public function updateShipmentStatus()
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        
        if ($role !== 'logistics_coordinator') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $shipmentId = (int)$this->request->getPost('shipment_id');
        $status = trim((string)$this->request->getPost('status'));
        $notes = trim((string)$this->request->getPost('notes'));

        if (!in_array($status, ['in_transit', 'delivered', 'delayed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $updateData = [
            'status' => $status,
            'updated_at' => $now
        ];

        if ($status === 'delivered') {
            $updateData['delivered_at'] = $now;
        }

        $db->table('shipments')
            ->where('shipment_id', $shipmentId)
            ->update($updateData);

        // Log the update
        if (!empty($notes)) {
            $db->table('shipment_logs')->insert([
                'shipment_id' => $shipmentId,
                'status' => $status,
                'notes' => $notes,
                'created_at' => $now
            ]);
        }

        return redirect()->to(site_url('logistics/orders'))->with('success', 'Shipment status updated!');
    }

    public function routes()
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        
        if ($role !== 'logistics_coordinator') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $db = db_connect();

        $routes = $db->table('routes r')
            ->select('r.*, d.driver_name, COUNT(sh.shipment_id) as shipment_count')
            ->join('drivers d', 'd.driver_id = r.driver_id', 'left')
            ->join('shipments sh', 'sh.route_id = r.route_id', 'left')
            ->groupBy('r.route_id')
            ->orderBy('r.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('dashboard/logistics_routes', [
            'routes' => $routes
        ]);
    }

    public function drivers()
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        
        if ($role !== 'logistics_coordinator') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $db = db_connect();

        $drivers = $db->table('drivers d')
            ->select('d.*, COUNT(sh.shipment_id) as total_shipments, COUNT(CASE WHEN sh.status = "delivered" THEN 1 END) as completed_shipments')
            ->join('shipments sh', 'sh.driver_id = d.driver_id', 'left')
            ->groupBy('d.driver_id')
            ->orderBy('d.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('dashboard/logistics_drivers', [
            'drivers' => $drivers
        ]);
    }
}
