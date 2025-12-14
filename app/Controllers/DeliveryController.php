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
}
