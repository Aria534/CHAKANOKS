<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Fetch category IDs dynamically
        $categoryRows = $db->table('categories')->select('category_id, category_name')->get()->getResultArray();
        $categories = [];
        foreach ($categoryRows as $cat) {
            $categories[$cat['category_name']] = $cat['category_id'];
        }

        // Fetch supplier IDs
        $supplierRows = $db->table('suppliers')->select('supplier_id')->get()->getResultArray();
        $supplierIds = array_column($supplierRows, 'supplier_id');

        if (empty($categories) || empty($supplierIds)) {
            return; // No data to seed
        }

        $freshChickenCutsCategoryId = $categories['Fresh Chicken Cuts'] ?? 1;
        $defaultSupplierId = $supplierIds[0] ?? 1;

        $data = [
            // Chicken Products
            [
                'product_name' => 'Chicken Breast',
                'product_code' => 'CP001',
                'barcode' => '123456789001',
                'category_id' => $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 180.00,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'is_perishable' => true,
                'shelf_life_days' => 3,
                'description' => 'Fresh chicken breast fillets',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Chicken Thighs',
                'product_code' => 'CP002',
                'barcode' => '123456789002',
                'category_id' => $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 150.00,
                'minimum_stock' => 15,
                'maximum_stock' => 150,
                'is_perishable' => true,
                'shelf_life_days' => 3,
                'description' => 'Fresh chicken thighs',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Whole Chicken',
                'product_code' => 'CP003',
                'barcode' => '123456789003',
                'category_id' => $categories['Whole Chickens'] ?? $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'pieces',
                'unit_price' => 250.00,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'is_perishable' => true,
                'shelf_life_days' => 2,
                'description' => 'Fresh whole chicken',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Chicken Wings',
                'product_code' => 'CP004',
                'barcode' => '123456789004',
                'category_id' => $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 120.00,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'is_perishable' => true,
                'shelf_life_days' => 3,
                'description' => 'Fresh chicken wings',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Ground Chicken',
                'product_code' => 'CP005',
                'barcode' => '123456789005',
                'category_id' => $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 160.00,
                'minimum_stock' => 3,
                'maximum_stock' => 30,
                'is_perishable' => true,
                'shelf_life_days' => 2,
                'description' => 'Fresh ground chicken',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Chicken Drumsticks',
                'product_code' => 'CP006',
                'barcode' => '123456789006',
                'category_id' => $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 140.00,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'is_perishable' => true,
                'shelf_life_days' => 3,
                'description' => 'Fresh chicken drumsticks',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Chicken Gizzards',
                'product_code' => 'CP007',
                'barcode' => '123456789007',
                'category_id' => $categories['Chicken By-Products'] ?? $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 80.00,
                'minimum_stock' => 15,
                'maximum_stock' => 150,
                'is_perishable' => true,
                'shelf_life_days' => 2,
                'description' => 'Fresh chicken gizzards',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Chicken Liver',
                'product_code' => 'CP008',
                'barcode' => '123456789008',
                'category_id' => $categories['Chicken By-Products'] ?? $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 90.00,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'is_perishable' => true,
                'shelf_life_days' => 2,
                'description' => 'Fresh chicken liver',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Frozen Chicken Nuggets',
                'product_code' => 'CP009',
                'barcode' => '123456789009',
                'category_id' => $categories['Frozen Chicken'] ?? $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 200.00,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'is_perishable' => true,
                'shelf_life_days' => 30,
                'description' => 'Frozen chicken nuggets 1kg pack',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Chicken Skin',
                'product_code' => 'CP010',
                'barcode' => '123456789010',
                'category_id' => $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 50.00,
                'minimum_stock' => 8,
                'maximum_stock' => 80,
                'is_perishable' => true,
                'shelf_life_days' => 2,
                'description' => 'Fresh chicken skin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Chicken Bones',
                'product_code' => 'CP011',
                'barcode' => '123456789011',
                'category_id' => $freshChickenCutsCategoryId,
                'supplier_id' => $defaultSupplierId,
                'unit_of_measure' => 'kg',
                'unit_price' => 30.00,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'is_perishable' => true,
                'shelf_life_days' => 3,
                'description' => 'Chicken bones for broth',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $builder = $db->table('products');
        foreach ($data as $row) {
            // Upsert by unique product_code
            $existing = $builder->where('product_code', $row['product_code'])->get()->getRowArray();
            $builder->resetQuery();

            if ($existing && isset($existing['product_id'])) {
                $builder->where('product_id', (int)$existing['product_id'])->update($row);
            } else {
                $builder->insert($row);
            }
            $builder->resetQuery();
        }
    }
}
