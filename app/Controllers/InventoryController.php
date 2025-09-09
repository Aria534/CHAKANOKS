<?php
namespace App\Controllers;

class InventoryController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        // Get inventory with branch and product info
        $inventory = $db->table('inventory i')
            ->select('i.inventory_id, b.branch_name, p.product_name, i.current_stock, p.unit_price, (i.current_stock * p.unit_price) as stock_value')
            ->join('branches b', 'b.branch_id = i.branch_id', 'left')
            ->join('products p', 'p.product_id = i.product_id', 'left')
            ->orderBy('b.branch_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('dashboard/inventory', ['inventory' => $inventory]);
    }
}
