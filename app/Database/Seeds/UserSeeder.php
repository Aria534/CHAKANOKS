<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            
            [
                'username' => 'Central Office Admin',
                'password' => password_hash('central123', PASSWORD_DEFAULT),
                'email' => 'central@chakanoks.com',
                'first_name' => 'Jhondell',
                'last_name' => 'Ranises',
                'phone' => '+63 82 123-4567',
                'role' => 'central_admin',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // System Administrator (IT) - Super Admin
            [
                'username' => 'Super Admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'email' => 'admin@chakanoks.com',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'phone' => '+63 82 111-0000',
                'role' => 'system_admin',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Franchise Manager
            [
                'username' => 'franchise.mgr',
                'password' => password_hash('franchise123', PASSWORD_DEFAULT),
                'email' => 'franchise@chakanoks.com',
                'first_name' => 'Franchise',
                'last_name' => 'Manager',
                'phone' => '+63 82 222-0000',
                'role' => 'franchise_manager',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Logistics Coordinator
            [
                'username' => 'logistics.coord',
                'password' => password_hash('logistics123', PASSWORD_DEFAULT),
                'email' => 'logistics@chakanoks.com',
                'first_name' => 'Logistics',
                'last_name' => 'Coordinator',
                'phone' => '+63 82 333-0000',
                'role' => 'logistics_coordinator',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Supplier user (external portal)
            [
                'username' => 'supplier.user',
                'password' => password_hash('supplier123', PASSWORD_DEFAULT),
                'email' => 'supplier@chakanoks.com',
                'first_name' => 'Supplier',
                'last_name' => 'User',
                'phone' => '+63 82 444-0000',
                'role' => 'supplier',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Branch Managers (one per branch)
            [
                'username' => 'Branch Manager1',
                'password' => password_hash('branch1', PASSWORD_DEFAULT),
                'email' => 'branch1@chakanoks.com',
                'first_name' => 'SM',
                'last_name' => 'Lanang Manager',
                'phone' => '+63 82 200-0001',
                'role' => 'branch_manager',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'Branch Manager2',
                'password' => password_hash('branch2', PASSWORD_DEFAULT),
                'email' => 'branch2@chakanoks.com',
                'first_name' => 'Abreeza',
                'last_name' => 'Manager',
                'phone' => '+63 82 200-0002',
                'role' => 'branch_manager',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'Branch Manager3',
                'password' => password_hash('branch3', PASSWORD_DEFAULT),
                'email' => 'branch3@chakanoks.com',
                'first_name' => 'Gaisano',
                'last_name' => 'Manager',
                'phone' => '+63 82 200-0003',
                'role' => 'branch_manager',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'Branch Manager4',
                'password' => password_hash('branch4', PASSWORD_DEFAULT),
                'email' => 'branch4@chakanoks.com',
                'first_name' => 'NCCC',
                'last_name' => 'Manager',
                'phone' => '+63 82 200-0004',
                'role' => 'branch_manager',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'Branch Manager5',
                'password' => password_hash('branch5', PASSWORD_DEFAULT),
                'email' => 'branch5@chakanoks.com',
                'first_name' => 'Victoria',
                'last_name' => 'Manager',
                'phone' => '+63 82 200-0005',
                'role' => 'branch_manager',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Inventory Staff (single account only)
            [
                'username' => 'staff1',
                'password' => password_hash('staff123', PASSWORD_DEFAULT),
                'email' => 'inventory1@chakanoks.com',
                'first_name' => 'Inventory',
                'last_name' => 'Staff 1',
                'phone' => '+63 82 789-0123',
                'role' => 'inventory_staff',
                'status' => 'active',
                'last_login' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
