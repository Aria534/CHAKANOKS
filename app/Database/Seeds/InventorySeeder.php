<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Fetch actual product IDs
        $productRows = $db->table('products')->select('product_id')->get()->getResultArray();
        if (empty($productRows)) {
            // No products to seed inventory for
            return;
        }
        $productIds = array_column($productRows, 'product_id');

        // Fetch branch IDs and exclude Central Office by branch_code
        $branchRows = $db->table('branches')->select('branch_id, branch_code')->get()->getResultArray();
        if (empty($branchRows)) {
            return;
        }
        $branchIds = [];
        foreach ($branchRows as $row) {
            if (strtoupper((string)($row['branch_code'] ?? '')) !== 'CENTRAL') {
                $branchIds[] = (int) $row['branch_id'];
            }
        }
        if (empty($branchIds)) {
            return;
        }

        // Upsert per (product_id, branch_id) to avoid duplicates
        $builder = $db->table('inventory');
        foreach ($branchIds as $branchId) {
            foreach ($productIds as $productId) {
                $currentStock = rand(20, 100);
                $reservedStock = rand(0, 10);
                $availableStock = max(0, $currentStock - $reservedStock);

                $existing = $builder
                    ->where('product_id', (int)$productId)
                    ->where('branch_id', (int)$branchId)
                    ->get()->getRowArray();
                $builder->resetQuery();

                $now = date('Y-m-d H:i:s');
                $row = [
                    'product_id' => (int) $productId,
                    'branch_id' => (int) $branchId,
                    'current_stock' => $currentStock,
                    'reserved_stock' => $reservedStock,
                    'available_stock' => $availableStock,
                    'last_updated' => $now,
                    'updated_at' => $now,
                ];

                if ($existing && isset($existing['inventory_id'])) {
                    $builder->where('inventory_id', (int)$existing['inventory_id'])->update($row);
                } else {
                    $row['created_at'] = $now;
                    $builder->insert($row);
                }
                $builder->resetQuery();
            }
        }
    }
}
