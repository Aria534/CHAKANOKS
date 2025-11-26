<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
<<<<<<< HEAD
                'category_name' => 'Fresh Chicken Cuts',
                'description' => 'Fresh chicken parts and cuts',
=======
                'category_name' => 'Fresh Produce',
                'description' => 'Fresh vegetables, fruits, and herbs',
>>>>>>> 1e32aec7eed9bc8b1e2e3358829a23410772375f
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
<<<<<<< HEAD
                'category_name' => 'Whole Chickens',
                'description' => 'Whole fresh chickens',
=======
                'category_name' => 'Meat & Poultry',
                'description' => 'Fresh and frozen meat products',
>>>>>>> 1e32aec7eed9bc8b1e2e3358829a23410772375f
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
<<<<<<< HEAD
                'category_name' => 'Chicken By-Products',
                'description' => 'Chicken gizzards, liver, and other by-products',
=======
                'category_name' => 'Beverages',
                'description' => 'Drinks, juices, and liquid products',
>>>>>>> 1e32aec7eed9bc8b1e2e3358829a23410772375f
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
<<<<<<< HEAD
                'category_name' => 'Frozen Chicken',
                'description' => 'Frozen chicken products',
=======
                'category_name' => 'Dairy & Frozen',
                'description' => 'Dairy products and frozen foods',
>>>>>>> 1e32aec7eed9bc8b1e2e3358829a23410772375f
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
<<<<<<< HEAD
                'category_name' => 'Chicken Processing',
                'description' => 'Chicken processing and packaging materials',
=======
                'category_name' => 'Kitchen Supplies',
                'description' => 'Cooking utensils, equipment, and supplies',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Packaging Materials',
                'description' => 'Containers, bags, and packaging supplies',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_name' => 'Cleaning Supplies',
                'description' => 'Cleaning agents and maintenance supplies',
>>>>>>> 1e32aec7eed9bc8b1e2e3358829a23410772375f
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('categories')->insertBatch($data);
    }
}
