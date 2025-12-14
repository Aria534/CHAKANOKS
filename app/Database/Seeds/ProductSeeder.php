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

        // Fetch supplier IDs with names
        $supplierRows = $db->table('suppliers')->select('supplier_id, supplier_name')->get()->getResultArray();
        $suppliers = [];
        foreach ($supplierRows as $sup) {
            $suppliers[$sup['supplier_name']] = $sup['supplier_id'];
        }

        if (empty($categories) || empty($suppliers)) {
            return; // No data to seed
        }

        $data = [
            // Fresh Produce Supply Co. - Vegetables & Fruits
            [
                'product_name' => 'Fresh Tomatoes',
                'product_code' => 'FP001',
                'barcode' => '223456789001',
                'category_id' => $categories['Vegetables'] ?? 1,
                'supplier_id' => $suppliers['Fresh Produce Supply Co.'] ?? 1,
                'unit_of_measure' => 'kg',
                'unit_price' => 85.00,
                'minimum_stock' => 15,
                'maximum_stock' => 100,
                'is_perishable' => true,
                'shelf_life_days' => 5,
                'description' => 'Fresh ripe tomatoes',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Fresh Onions',
                'product_code' => 'FP002',
                'barcode' => '223456789002',
                'category_id' => $categories['Vegetables'] ?? 1,
                'supplier_id' => $suppliers['Fresh Produce Supply Co.'] ?? 1,
                'unit_of_measure' => 'kg',
                'unit_price' => 60.00,
                'minimum_stock' => 20,
                'maximum_stock' => 150,
                'is_perishable' => true,
                'shelf_life_days' => 14,
                'description' => 'Fresh yellow onions',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Fresh Garlic',
                'product_code' => 'FP003',
                'barcode' => '223456789003',
                'category_id' => $categories['Vegetables'] ?? 1,
                'supplier_id' => $suppliers['Fresh Produce Supply Co.'] ?? 1,
                'unit_of_measure' => 'kg',
                'unit_price' => 120.00,
                'minimum_stock' => 10,
                'maximum_stock' => 80,
                'is_perishable' => true,
                'shelf_life_days' => 21,
                'description' => 'Fresh garlic bulbs',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Fresh Cabbage',
                'product_code' => 'FP004',
                'barcode' => '223456789004',
                'category_id' => $categories['Vegetables'] ?? 1,
                'supplier_id' => $suppliers['Fresh Produce Supply Co.'] ?? 1,
                'unit_of_measure' => 'kg',
                'unit_price' => 45.00,
                'minimum_stock' => 12,
                'maximum_stock' => 120,
                'is_perishable' => true,
                'shelf_life_days' => 10,
                'description' => 'Fresh green cabbage',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Fresh Carrots',
                'product_code' => 'FP005',
                'barcode' => '223456789005',
                'category_id' => $categories['Vegetables'] ?? 1,
                'supplier_id' => $suppliers['Fresh Produce Supply Co.'] ?? 1,
                'unit_of_measure' => 'kg',
                'unit_price' => 55.00,
                'minimum_stock' => 15,
                'maximum_stock' => 100,
                'is_perishable' => true,
                'shelf_life_days' => 14,
                'description' => 'Fresh orange carrots',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Meat & Poultry Distributors - Chicken Products
            [
                'product_name' => 'Chicken Breast',
                'product_code' => 'MP001',
                'barcode' => '323456789001',
                'category_id' => $categories['Fresh Chicken Cuts'] ?? 1,
                'supplier_id' => $suppliers['Meat & Poultry Distributors'] ?? 2,
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
                'product_code' => 'MP002',
                'barcode' => '323456789002',
                'category_id' => $categories['Fresh Chicken Cuts'] ?? 1,
                'supplier_id' => $suppliers['Meat & Poultry Distributors'] ?? 2,
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
                'product_code' => 'MP003',
                'barcode' => '323456789003',
                'category_id' => $categories['Whole Chickens'] ?? 1,
                'supplier_id' => $suppliers['Meat & Poultry Distributors'] ?? 2,
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
                'product_code' => 'MP004',
                'barcode' => '323456789004',
                'category_id' => $categories['Fresh Chicken Cuts'] ?? 1,
                'supplier_id' => $suppliers['Meat & Poultry Distributors'] ?? 2,
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

            // Beverage Solutions Inc. - Drinks
            [
                'product_name' => 'Orange Juice 1L',
                'product_code' => 'BS001',
                'barcode' => '423456789001',
                'category_id' => $categories['Beverages'] ?? 1,
                'supplier_id' => $suppliers['Beverage Solutions Inc.'] ?? 3,
                'unit_of_measure' => 'bottles',
                'unit_price' => 75.00,
                'minimum_stock' => 30,
                'maximum_stock' => 300,
                'is_perishable' => true,
                'shelf_life_days' => 30,
                'description' => 'Fresh orange juice 1 liter bottle',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Iced Tea 500ml',
                'product_code' => 'BS002',
                'barcode' => '423456789002',
                'category_id' => $categories['Beverages'] ?? 1,
                'supplier_id' => $suppliers['Beverage Solutions Inc.'] ?? 3,
                'unit_of_measure' => 'bottles',
                'unit_price' => 35.00,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'is_perishable' => false,
                'shelf_life_days' => 365,
                'description' => 'Refreshing iced tea 500ml bottle',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Mineral Water 1.5L',
                'product_code' => 'BS003',
                'barcode' => '423456789003',
                'category_id' => $categories['Beverages'] ?? 1,
                'supplier_id' => $suppliers['Beverage Solutions Inc.'] ?? 3,
                'unit_of_measure' => 'bottles',
                'unit_price' => 25.00,
                'minimum_stock' => 100,
                'maximum_stock' => 1000,
                'is_perishable' => false,
                'shelf_life_days' => 365,
                'description' => 'Pure mineral water 1.5 liter bottle',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Soft Drink 2L',
                'product_code' => 'BS004',
                'barcode' => '423456789004',
                'category_id' => $categories['Beverages'] ?? 1,
                'supplier_id' => $suppliers['Beverage Solutions Inc.'] ?? 3,
                'unit_of_measure' => 'bottles',
                'unit_price' => 55.00,
                'minimum_stock' => 40,
                'maximum_stock' => 400,
                'is_perishable' => false,
                'shelf_life_days' => 365,
                'description' => 'Carbonated soft drink 2 liter bottle',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Kitchen Essentials Supply - Condiments & Spices
            [
                'product_name' => 'Cooking Oil 1L',
                'product_code' => 'KE001',
                'barcode' => '523456789001',
                'category_id' => $categories['Condiments'] ?? 1,
                'supplier_id' => $suppliers['Kitchen Essentials Supply'] ?? 4,
                'unit_of_measure' => 'bottles',
                'unit_price' => 95.00,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'is_perishable' => false,
                'shelf_life_days' => 365,
                'description' => 'Premium cooking oil 1 liter',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Salt 1kg',
                'product_code' => 'KE002',
                'barcode' => '523456789002',
                'category_id' => $categories['Condiments'] ?? 1,
                'supplier_id' => $suppliers['Kitchen Essentials Supply'] ?? 4,
                'unit_of_measure' => 'bags',
                'unit_price' => 35.00,
                'minimum_stock' => 25,
                'maximum_stock' => 250,
                'is_perishable' => false,
                'shelf_life_days' => 365,
                'description' => 'Fine table salt 1kg bag',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Black Pepper 500g',
                'product_code' => 'KE003',
                'barcode' => '523456789003',
                'category_id' => $categories['Spices'] ?? 1,
                'supplier_id' => $suppliers['Kitchen Essentials Supply'] ?? 4,
                'unit_of_measure' => 'bags',
                'unit_price' => 150.00,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'is_perishable' => false,
                'shelf_life_days' => 365,
                'description' => 'Ground black pepper 500g',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Soy Sauce 1L',
                'product_code' => 'KE004',
                'barcode' => '523456789004',
                'category_id' => $categories['Condiments'] ?? 1,
                'supplier_id' => $suppliers['Kitchen Essentials Supply'] ?? 4,
                'unit_of_measure' => 'bottles',
                'unit_price' => 65.00,
                'minimum_stock' => 15,
                'maximum_stock' => 150,
                'is_perishable' => false,
                'shelf_life_days' => 365,
                'description' => 'Premium soy sauce 1 liter',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Dairy & Frozen Foods Co. - Dairy & Frozen
            [
                'product_name' => 'Whole Milk 1L',
                'product_code' => 'DF001',
                'barcode' => '623456789001',
                'category_id' => $categories['Dairy'] ?? 1,
                'supplier_id' => $suppliers['Dairy & Frozen Foods Co.'] ?? 5,
                'unit_of_measure' => 'bottles',
                'unit_price' => 55.00,
                'minimum_stock' => 30,
                'maximum_stock' => 300,
                'is_perishable' => true,
                'shelf_life_days' => 7,
                'description' => 'Fresh whole milk 1 liter',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Butter 250g',
                'product_code' => 'DF002',
                'barcode' => '623456789002',
                'category_id' => $categories['Dairy'] ?? 1,
                'supplier_id' => $suppliers['Dairy & Frozen Foods Co.'] ?? 5,
                'unit_of_measure' => 'packs',
                'unit_price' => 120.00,
                'minimum_stock' => 15,
                'maximum_stock' => 150,
                'is_perishable' => true,
                'shelf_life_days' => 30,
                'description' => 'Fresh butter 250g pack',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Cheese 500g',
                'product_code' => 'DF003',
                'barcode' => '623456789003',
                'category_id' => $categories['Dairy'] ?? 1,
                'supplier_id' => $suppliers['Dairy & Frozen Foods Co.'] ?? 5,
                'unit_of_measure' => 'packs',
                'unit_price' => 180.00,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'is_perishable' => true,
                'shelf_life_days' => 21,
                'description' => 'Cheddar cheese 500g pack',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Frozen Vegetables 1kg',
                'product_code' => 'DF004',
                'barcode' => '623456789004',
                'category_id' => $categories['Frozen Foods'] ?? 1,
                'supplier_id' => $suppliers['Dairy & Frozen Foods Co.'] ?? 5,
                'unit_of_measure' => 'packs',
                'unit_price' => 95.00,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'is_perishable' => true,
                'shelf_life_days' => 180,
                'description' => 'Mixed frozen vegetables 1kg pack',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Frozen Fries 1kg',
                'product_code' => 'DF005',
                'barcode' => '623456789005',
                'category_id' => $categories['Frozen Foods'] ?? 1,
                'supplier_id' => $suppliers['Dairy & Frozen Foods Co.'] ?? 5,
                'unit_of_measure' => 'packs',
                'unit_price' => 85.00,
                'minimum_stock' => 25,
                'maximum_stock' => 250,
                'is_perishable' => true,
                'shelf_life_days' => 180,
                'description' => 'Frozen french fries 1kg pack',
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
