<?php
namespace App\Controllers;

class DeliveryController extends BaseController
{
    public function index()
    {
        $session = session();
        $userId = (int)($session->get('user_id') ?? 0);
        $role = (string)($session->get('role') ?? '');

        if ($role !== 'branch_manager') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $db = db_connect();

        // Setup map coordinates if not already done
        $this->setupMapCoordinates($db);

        $branchId = $db->table('user_branches')
            ->select('branch_id')
            ->where('user_id', $userId)
            ->get()->getRowArray();

        $branchId = (int)($branchId['branch_id'] ?? 0);

        $query = $db->table('purchase_orders po')
            ->select('po.purchase_order_id, po.po_number, po.status, po.expected_delivery_date, po.actual_delivery_date, s.supplier_name, GROUP_CONCAT(p.product_name SEPARATOR ", ") as items')
            ->join('suppliers s', 's.supplier_id = po.supplier_id')
            ->join('purchase_order_items poi', 'poi.purchase_order_id = po.purchase_order_id')
            ->join('products p', 'p.product_id = poi.product_id')
            ->where('po.branch_id', $branchId)
            ->whereIn('po.status', ['pending', 'approved', 'ordered', 'in_transit', 'delivered']);

        // Filter by status
        if (!empty($_GET['status'])) {
            $statusMap = [
                'pending' => 'pending',
                'approved' => 'approved',
                'in_transit' => 'ordered',
                'delivered' => 'delivered'
            ];
            $filterStatus = $_GET['status'];
            if (isset($statusMap[$filterStatus])) {
                $query->where('po.status', $statusMap[$filterStatus]);
            }
        }

        // Search by PO number
        if (!empty($_GET['search'])) {
            $query->like('po.po_number', $_GET['search']);
        }

        $deliveries = $query->groupBy('po.purchase_order_id')
            ->orderBy('po.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('dashboard/deliveries', [
            'deliveries' => $deliveries
        ]);
    }

    public function view($id)
    {
        $session = session();
        $userId = (int)($session->get('user_id') ?? 0);
        $role = (string)($session->get('role') ?? '');

        if ($role !== 'branch_manager') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $db = db_connect();

        $purchaseOrder = $db->table('purchase_orders po')
            ->select('po.*, s.supplier_name, s.contact_person')
            ->join('suppliers s', 's.supplier_id = po.supplier_id')
            ->where('po.purchase_order_id', $id)
            ->get()
            ->getRowArray();

        if (!$purchaseOrder) {
            return redirect()->back()->with('error', 'Delivery not found');
        }

        $items = $db->table('purchase_order_items poi')
            ->select('poi.*, p.product_name, p.product_code')
            ->join('products p', 'p.product_id = poi.product_id')
            ->where('poi.purchase_order_id', $id)
            ->get()
            ->getResultArray();

        return view('dashboard/delivery_view', [
            'purchaseOrder' => $purchaseOrder,
            'items' => $items
        ]);
    }

    public function updateStatus()
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');

        if ($role !== 'branch_manager') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $id = (int)$this->request->getPost('purchase_order_id');
        $status = trim((string)$this->request->getPost('status'));
        $deliveryDate = $this->request->getPost('delivery_date');
        $notes = trim((string)$this->request->getPost('notes'));
        $now = date('Y-m-d H:i:s');

        if (!in_array($status, ['in_transit', 'delivered'])) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        $db = db_connect();

        $updateData = [
            'status' => $status,
            'updated_at' => $now
        ];

        if ($status === 'delivered' && !empty($deliveryDate)) {
            $updateData['actual_delivery_date'] = $deliveryDate;
        }

        $db->table('purchase_orders')
            ->where('purchase_order_id', $id)
            ->update($updateData);

        // Log the delivery update
        if (!empty($notes)) {
            $db->table('stock_movements')->insert([
                'product_id' => 0,
                'branch_id' => 0,
                'movement_type' => 'delivery_update',
                'quantity' => 0,
                'reference_type' => 'delivery',
                'reference_id' => $id,
                'notes' => 'Delivery Status: ' . ucfirst(str_replace('_', ' ', $status)) . ' - ' . $notes,
                'created_by' => (int)$session->get('user_id'),
                'created_at' => $now
            ]);
        }

        return redirect()->to(site_url('deliveries'))->with('success', 'Delivery status updated successfully!');
    }

    public function getTracking($id)
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');

        if ($role !== 'branch_manager') {
            return response()->setJSON(['error' => 'Unauthorized access'], 403);
        }

        $db = db_connect();

        $order = $db->table('purchase_orders po')
            ->select('po.*, b.branch_name, b.latitude, b.longitude, s.supplier_name, s.latitude as supplier_lat, s.longitude as supplier_lng')
            ->join('branches b', 'b.branch_id = po.branch_id')
            ->join('suppliers s', 's.supplier_id = po.supplier_id')
            ->where('po.purchase_order_id', (int)$id)
            ->get()
            ->getRowArray();

        if (!$order) {
            return response()->setJSON(['error' => 'Order not found'], 404);
        }

        // Map status to display text
        $statusMap = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'ordered' => 'In Transit',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered'
        ];

        // Use Davao coordinates as default if not set
        $latitude = !empty($order['latitude']) ? (float)$order['latitude'] : 7.0731;
        $longitude = !empty($order['longitude']) ? (float)$order['longitude'] : 125.6121;
        $supplierLat = !empty($order['supplier_lat']) ? (float)$order['supplier_lat'] : 7.0731;
        $supplierLng = !empty($order['supplier_lng']) ? (float)$order['supplier_lng'] : 125.6121;

        return response()->setJSON([
            'status' => $statusMap[$order['status']] ?? ucfirst($order['status']),
            'expected_date' => !empty($order['expected_delivery_date']) ? date('M d, Y', strtotime($order['expected_delivery_date'])) : '-',
            'location' => $order['branch_name'] ?? 'In Transit',
            'supplier_name' => $order['supplier_name'] ?? 'Supplier',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'supplier_lat' => $supplierLat,
            'supplier_lng' => $supplierLng
        ]);
    }

    private function setupMapCoordinates($db)
    {
        try {
            // Add columns to suppliers if they don't exist
            $result = $db->query("SHOW COLUMNS FROM suppliers LIKE 'latitude'");
            if ($result->getNumRows() == 0) {
                $db->query("ALTER TABLE suppliers ADD COLUMN latitude DECIMAL(10, 6) DEFAULT 7.0500");
                $db->query("ALTER TABLE suppliers ADD COLUMN longitude DECIMAL(10, 6) DEFAULT 125.6000");
            }

            // Add columns to branches if they don't exist
            $result = $db->query("SHOW COLUMNS FROM branches LIKE 'latitude'");
            if ($result->getNumRows() == 0) {
                $db->query("ALTER TABLE branches ADD COLUMN latitude DECIMAL(10, 6) DEFAULT 7.0731");
                $db->query("ALTER TABLE branches ADD COLUMN longitude DECIMAL(10, 6) DEFAULT 125.6121");
            }

            // Update suppliers with different Davao locations
            $suppliers = [
                'Fresh Produce Supply Co.' => ['lat' => 7.0500, 'lng' => 125.6000],
                'Meat & Poultry Distributors' => ['lat' => 7.0800, 'lng' => 125.6200],
                'Beverage Solutions Inc.' => ['lat' => 7.0600, 'lng' => 125.6100],
                'Kitchen Essentials Supply' => ['lat' => 7.0700, 'lng' => 125.5900],
                'Dairy & Frozen Foods Co.' => ['lat' => 7.0900, 'lng' => 125.6300]
            ];

            foreach ($suppliers as $name => $coords) {
                $db->table('suppliers')
                    ->where('supplier_name', $name)
                    ->update([
                        'latitude' => $coords['lat'],
                        'longitude' => $coords['lng']
                    ]);
            }

            // Update all branches with Davao coordinates
            $db->table('branches')->update([
                'latitude' => 7.0731,
                'longitude' => 125.6121
            ]);
        } catch (\Exception $e) {
            // Silently fail if setup fails
            log_message('error', 'Map setup error: ' . $e->getMessage());
        }
    }
}





