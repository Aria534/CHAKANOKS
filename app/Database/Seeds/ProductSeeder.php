<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Fresh Produce
            [
                'product_name' => 'Fresh Tomatoes',
                'product_code' => 'FP001',
                'barcode' => '123456789001',
                'category_id' => 1,
                'supplier_id' => 1,
                'unit_of_measure' => 'kg',
                'unit_price' => 45.00,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'is_perishable' => true,
                'shelf_life_days' => 7,
                'description' => 'Fresh red tomatoes for cooking',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Fresh Onions',
                'product_code' => 'FP002',
                'barcode' => '123456789002',
                'category_id' => 1,
                'supplier_id' => 1,
                'unit_of_measure' => 'kg',
                'unit_price' => 35.00,
                'minimum_stock' => 15,
                'maximum_stock' => 150,
                'is_perishable' => true,
                'shelf_life_days' => 14,
                'description' => 'Fresh white onions',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Fresh Lettuce',
                'product_code' => 'FP003',
                'barcode' => '123456789003',
                'category_id' => 1,
                'supplier_id' => 1,
                'unit_of_measure' => 'pieces',
                'unit_price' => 25.00,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'is_perishable' => true,
                'shelf_life_days' => 5,
                'description' => 'Fresh green lettuce heads',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Meat & Poultry
            [
                'product_name' => 'Chicken Breast',
                'product_code' => 'MP001',
                'barcode' => '123456789004',
                'category_id' => 2,
                'supplier_id' => 2,
                'unit_of_measure' => 'kg',
                'unit_price' => 180.00,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'is_perishable' => true,
                'shelf_life_days' => 3,
                'description' => 'Fresh chicken breast fillets',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Ground Beef',
                'product_code' => 'MP002',
                'barcode' => '123456789005',
                'category_id' => 2,
                'supplier_id' => 2,
                'unit_of_measure' => 'kg',
                'unit_price' => 220.00,
                'minimum_stock' => 3,
                'maximum_stock' => 30,
                'is_perishable' => true,
                'shelf_life_days' => 2,
                'description' => 'Fresh ground beef',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Beverages
            [
                'product_name' => 'Coca Cola 1.5L',
                'product_code' => 'BV001',
                'barcode' => '123456789006',
                'category_id' => 3,
                'supplier_id' => 3,
                'unit_of_measure' => 'bottles',
                'unit_price' => 35.00,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'is_perishable' => false,
                'shelf_life_days' => null,
                'description' => 'Coca Cola 1.5 liter bottles',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Orange Juice 1L',
                'product_code' => 'BV002',
                'barcode' => '123456789007',
                'category_id' => 3,
                'supplier_id' => 3,
                'unit_of_measure' => 'bottles',
                'unit_price' => 45.00,
                'minimum_stock' => 15,
                'maximum_stock' => 150,
                'is_perishable' => true,
                'shelf_life_days' => 7,
                'description' => 'Fresh orange juice 1 liter',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Dairy & Frozen
            [
                'product_name' => 'Fresh Milk 1L',
                'product_code' => 'DF001',
                'barcode' => '123456789008',
                'category_id' => 4,
                'supplier_id' => 5,
                'unit_of_measure' => 'bottles',
                'unit_price' => 55.00,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'is_perishable' => true,
                'shelf_life_days' => 5,
                'description' => 'Fresh whole milk 1 liter',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Frozen French Fries',
                'product_code' => 'DF002',
                'barcode' => '123456789009',
                'category_id' => 4,
                'supplier_id' => 5,
                'unit_of_measure' => 'kg',
                'unit_price' => 85.00,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'is_perishable' => true,
                'shelf_life_days' => 30,
                'description' => 'Frozen french fries 1kg pack',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Kitchen Supplies
            [
                'product_name' => 'Cooking Oil 1L',
                'product_code' => 'KS001',
                'barcode' => '123456789010',
                'category_id' => 5,
                'supplier_id' => 4,
                'unit_of_measure' => 'bottles',
                'unit_price' => 65.00,
                'minimum_stock' => 8,
                'maximum_stock' => 80,
                'is_perishable' => false,
                'shelf_life_days' => null,
                'description' => 'Vegetable cooking oil 1 liter',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_name' => 'Salt 1kg',
                'product_code' => 'KS002',
                'barcode' => '123456789011',
                'category_id' => 5,
                'supplier_id' => 4,
                'unit_of_measure' => 'bags',
                'unit_price' => 25.00,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'is_perishable' => false,
                'shelf_life_days' => null,
                'description' => 'Table salt 1kg bag',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $builder = $this->db->table('products');
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
