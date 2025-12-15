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

        return view('dashboard/shared/products', ['products' => $products]);
    }

    public function create()
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        if (!in_array($role, ['inventory_staff','central_admin','system_admin'])) {
            return redirect()->back()->with('error', 'Only inventory staff can create products.');
        }

        $db = db_connect();
        
        $categories = $db->table('categories')
            ->where('status', 'active')
            ->orderBy('category_name', 'ASC')
            ->get()->getResultArray();
        
        $suppliers = $db->table('suppliers')
            ->where('status', 'active')
            ->orderBy('supplier_name', 'ASC')
            ->get()->getResultArray();
        
        $branches = $db->table('branches')
            ->where('status', 'active')
            ->orderBy('branch_name', 'ASC')
            ->get()->getResultArray();

        return view('dashboard/shared/create_product', [
            'categories' => $categories,
            'suppliers' => $suppliers,
            'branches' => $branches
        ]);
    }

    public function store()
    {
        $session = session();
        $role = (string)($session->get('role') ?? '');
        $userId = (int)($session->get('user_id') ?? 0);
        
        if (!in_array($role, ['inventory_staff','central_admin','system_admin'])) {
            return redirect()->back()->with('error', 'Only inventory staff can create products.');
        }

        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $productName = trim((string)$this->request->getPost('product_name'));
        $productCode = trim((string)$this->request->getPost('product_code'));
        $categoryId = (int)$this->request->getPost('category_id');
        $supplierId = (int)$this->request->getPost('supplier_id');
        $unitPrice = (float)$this->request->getPost('unit_price');
        $description = trim((string)$this->request->getPost('description'));
        $isPerishable = (int)($this->request->getPost('is_perishable') ?? 0);
        $shelfLifeDays = (int)($this->request->getPost('shelf_life_days') ?? 0);
        $minimumStock = (int)($this->request->getPost('minimum_stock') ?? 10);
        $branchId = (int)$this->request->getPost('branch_id');
        $initialStock = (int)($this->request->getPost('initial_stock') ?? 0);

        if (empty($productName)) {
            return redirect()->back()->with('error', 'Product name is required.')->withInput();
        }

        if ($categoryId <= 0) {
            return redirect()->back()->with('error', 'Please select a category.')->withInput();
        }

        if ($supplierId <= 0) {
            return redirect()->back()->with('error', 'Please select a supplier.')->withInput();
        }

        if ($unitPrice <= 0) {
            return redirect()->back()->with('error', 'Unit price must be greater than 0.')->withInput();
        }

        if ($branchId <= 0) {
            return redirect()->back()->with('error', 'Please select a branch.')->withInput();
        }

        try {
            $db->table('products')->insert([
                'product_name' => $productName,
                'product_code' => $productCode ?: null,
                'category_id' => $categoryId,
                'supplier_id' => $supplierId,
                'unit_price' => $unitPrice,
                'description' => $description ?: null,
                'is_perishable' => $isPerishable,
                'shelf_life_days' => $shelfLifeDays > 0 ? $shelfLifeDays : null,
                'minimum_stock' => $minimumStock,
                'status' => 'active'
            ]);

            $productId = $db->insertID();

            if ($initialStock > 0) {
                $db->table('inventory')->insert([
                    'product_id' => $productId,
                    'branch_id' => $branchId,
                    'current_stock' => $initialStock,
                    'reserved_stock' => 0,
                    'available_stock' => $initialStock,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);

                $db->table('stock_movements')->insert([
                    'product_id' => $productId,
                    'branch_id' => $branchId,
                    'movement_type' => 'in',
                    'quantity' => $initialStock,
                    'reference_type' => 'product_creation',
                    'reference_id' => $productId,
                    'unit_price' => $unitPrice,
                    'total_value' => $unitPrice * $initialStock,
                    'notes' => 'Initial stock for new product',
                    'created_by' => $userId,
                    'created_at' => $now
                ]);
            }

            return redirect()->to(site_url('inventory'))->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Product Creation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating product: ' . $e->getMessage())->withInput();
        }
    }
}
