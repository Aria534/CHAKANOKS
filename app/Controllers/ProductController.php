<?php
namespace App\Controllers;

class ProductController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $products = $db->table('products p')
            ->select('p.product_id, p.product_name, p.unit_price, c.category_name')
            ->join('categories c', 'c.category_id = p.category_id')
            ->get()->getResultArray();

        return view('dashboard/products', ['products' => $products]);
    }
}
