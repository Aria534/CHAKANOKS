<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Fetch branch IDs (exclude central)
        $branchRows = $db->table('branches')->select('branch_id')->where('branch_code !=', 'CENTRAL')->get()->getResultArray();
        $branchIds = array_column($branchRows, 'branch_id');

        // Fetch product IDs
        $productRows = $db->table('products')->select('product_id')->get()->getResultArray();
        $productIds = array_column($productRows, 'product_id');

        // Fetch user IDs for created_by
        $userRows = $db->table('users')->select('user_id')->get()->getResultArray();
        $userIds = array_column($userRows, 'user_id');

        if (empty($branchIds) || empty($productIds) || empty($userIds)) {
            return;
        }

        $movementTypes = ['in', 'out', 'adjustment'];
        $data = [];

        // Create movements for each branch
        foreach ($branchIds as $branchId) {
            for ($i = 0; $i < 10; $i++) { // 10 movements per branch
                $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' days'));
                $quantity = rand(1, 20) * (rand(0, 1) ? 1 : -1); // Positive or negative

                $data[] = [
                    'product_id' => $productIds[array_rand($productIds)],
                    'branch_id' => $branchId,
                    'movement_type' => $movementTypes[array_rand($movementTypes)],
                    'quantity' => $quantity,
                    'reference_type' => null,
                    'reference_id' => null,
                    'unit_price' => null,
                    'total_value' => null,
                    'notes' => 'Sample movement',
                    'created_by' => $userIds[array_rand($userIds)],
                    'created_at' => $createdAt,
                ];
            }
        }

        $db->table('stock_movements')->insertBatch($data);
    }
}
