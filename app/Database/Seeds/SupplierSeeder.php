<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'supplier_name' => 'Fresh Produce Supply Co.',
                'contact_person' => 'Aira Verola',
                'phone' => '+63 82 111-2222',
                'email' => 'Aira@freshproduce.com',
                'address' => '456 Market Street, Davao City, Philippines',
                'payment_terms' => 'Net 30',
                'delivery_time' => 2,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'supplier_name' => 'Meat & Poultry Distributors',
                'contact_person' => 'Jhondell Ranises',
                'phone' => '+63 82 222-3333',
                'email' => 'Jhondell@meatpoultry.com',
                'address' => '789 Industrial Road, Davao City, Philippines',
                'payment_terms' => 'Net 15',
                'delivery_time' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'supplier_name' => 'Beverage Solutions Inc.',
                'contact_person' => 'Ralph Terrado',
                'phone' => '+63 82 333-4444',
                'email' => 'Ralph@beveragesolutions.com',
                'address' => '321 Commerce Avenue, Davao City, Philippines',
                'payment_terms' => 'Net 30',
                'delivery_time' => 3,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'supplier_name' => 'Kitchen Essentials Supply',
                'contact_person' => 'Mj Dimson',
                'phone' => '+63 82 444-5555',
                'email' => 'Mj@kitchenessentials.com',
                'address' => '654 Supply Chain Blvd, Davao City, Philippines',
                'payment_terms' => 'Net 45',
                'delivery_time' => 5,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'supplier_name' => 'Dairy & Frozen Foods Co.',
                'contact_person' => 'Mark Owen Lu',
                'phone' => '+63 82 555-6666',
                'email' => 'Mark@dairyfrozen.com',
                'address' => '987 Cold Storage Complex, Davao City, Philippines',
                'payment_terms' => 'Net 30',
                'delivery_time' => 2,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $builder = $this->db->table('suppliers');
        foreach ($data as $row) {
            $email = trim(strtolower($row['email'] ?? ''));
            if ($email !== '') {
                $existing = $builder->where('email', $email)->get()->getRowArray();
            } else {
                $existing = $builder->where('supplier_name', $row['supplier_name'])->get()->getRowArray();
            }
            $builder->resetQuery();

            if ($existing && isset($existing['supplier_id'])) {
                $builder->where('supplier_id', (int)$existing['supplier_id'])->update($row);
            } else {
                $builder->insert($row);
            }
            $builder->resetQuery();
        }
    }
}
