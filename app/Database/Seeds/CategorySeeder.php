<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'category_name' => 'Fresh Chicken Cuts',
                'description' => 'Fresh chicken parts and cuts',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Whole Chickens',
                'description' => 'Whole fresh chickens',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Chicken By-Products',
                'description' => 'Chicken gizzards, liver, and other by-products',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Frozen Chicken',
                'description' => 'Frozen chicken products',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Chicken Processing',
                'description' => 'Chicken processing and packaging materials',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Vegetables',
                'description' => 'Fresh vegetables',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Beverages',
                'description' => 'Drinks and beverages',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Condiments',
                'description' => 'Sauces and condiments',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Spices',
                'description' => 'Spices and seasonings',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Dairy',
                'description' => 'Milk, cheese, butter and dairy products',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Frozen Foods',
                'description' => 'Frozen vegetables, fries and other frozen items',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Upsert by category_name to avoid duplicates
        $builder = $this->db->table('categories');
        foreach ($data as $row) {
            $existing = $builder->where('category_name', $row['category_name'])->get()->getRowArray();
            $builder->resetQuery();

            if ($existing && isset($existing['category_id'])) {
                $builder->where('category_id', (int)$existing['category_id'])->update($row);
            } else {
                $builder->insert($row);
            }
            $builder->resetQuery();
        }
    }
}
