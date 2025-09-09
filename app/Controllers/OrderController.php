<?php
namespace App\Controllers;

class OrderController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        $orders = $db->table('purchase_orders po')
            ->select('po.purchase_order_id, po.po_number, po.status, po.total_amount, po.requested_date, po.created_at, b.branch_name')
            ->join('branches b', 'b.branch_id = po.branch_id', 'left')
            ->orderBy('po.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('dashboard/orders', ['orders' => $orders]);
    }
}
