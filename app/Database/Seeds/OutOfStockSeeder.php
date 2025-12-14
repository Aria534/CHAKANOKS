<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OutOfStockSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Get product IDs and branch IDs
        $products = $db->table('products')
            ->select('product_id')
            ->get()
            ->getResultArray();

        $branches = $db->table('branches')
            ->select('branch_id')
            ->where('status', 'active')
            ->get()
            ->getResultArray();

        if (empty($products) || empty($branches)) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        // Create ONE out of stock item per branch
        foreach ($branches as $index => $branch) {
            // Select one product per branch (rotate through products)
            $productIndex = $index % count($products);
            $product = $products[$productIndex];

            // Check if inventory record exists
            $existing = $db->table('inventory')
                ->where('product_id', $product['product_id'])
                ->where('branch_id', $branch['branch_id'])
                ->get()
                ->getRowArray();

            if ($existing) {
                // Update to out of stock
                $db->table('inventory')
                    ->where('inventory_id', $existing['inventory_id'])
                    ->update([
                        'current_stock' => 0,
                        'available_stock' => 0,
                        'reserved_stock' => 0,
                        'updated_at' => $now
                    ]);
            } else {
                // Create new out of stock record
                $db->table('inventory')->insert([
                    'product_id' => $product['product_id'],
                    'branch_id' => $branch['branch_id'],
                    'current_stock' => 0,
                    'reserved_stock' => 0,
                    'available_stock' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
    }
}
