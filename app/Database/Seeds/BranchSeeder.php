<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $builder = $this->db->table('branches');

        // Skip if CENTRAL already exists to avoid duplicate key error
        $exists = $builder->select('branch_id')->where('branch_code', 'CENTRAL')->get()->getFirstRow();
        if ($exists) {
            return;
        }

        $data = [
            [
                'branch_name' => 'ChakaNoks Central Office',
                'branch_code' => 'CENTRAL',
                'address' => '123 Business District, Davao City, Philippines',
                'phone' => '+63 82 123-4567',
                'email' => 'central@chakanoks.com',
                'manager_name' => 'Maria Santos',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'branch_name' => 'ChakaNoks SM Lanang',
                'branch_code' => 'SM001',
                'address' => 'SM Lanang Premier, Davao City, Philippines',
                'phone' => '+63 82 234-5678',
                'email' => 'smlanang@chakanoks.com',
                'manager_name' => 'Juan Dela Cruz',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'branch_name' => 'ChakaNoks Abreeza',
                'branch_code' => 'ABR001',
                'address' => 'Abreeza Mall, Davao City, Philippines',
                'phone' => '+63 82 345-6789',
                'email' => 'abreeza@chakanoks.com',
                'manager_name' => 'Ana Rodriguez',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'branch_name' => 'ChakaNoks Gaisano Mall',
                'branch_code' => 'GAI001',
                'address' => 'Gaisano Mall, Davao City, Philippines',
                'phone' => '+63 82 456-7890',
                'email' => 'gaisano@chakanoks.com',
                'manager_name' => 'Pedro Martinez',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'branch_name' => 'ChakaNoks NCCC Mall',
                'branch_code' => 'NCC001',
                'address' => 'NCCC Mall, Davao City, Philippines',
                'phone' => '+63 82 567-8901',
                'email' => 'nccc@chakanoks.com',
                'manager_name' => 'Carmen Lopez',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'branch_name' => 'ChakaNoks Victoria Plaza',
                'branch_code' => 'VIC001',
                'address' => 'Victoria Plaza, Davao City, Philippines',
                'phone' => '+63 82 678-9012',
                'email' => 'victoria@chakanoks.com',
                'manager_name' => 'Roberto Garcia',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $builder->insertBatch($data);
    }
}
