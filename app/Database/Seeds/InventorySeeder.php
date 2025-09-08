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

        $data = [];
        foreach ($branchIds as $branchId) {
            foreach ($productIds as $productId) {
                $currentStock = rand(20, 100);
                $reservedStock = rand(0, 10);
                $availableStock = max(0, $currentStock - $reservedStock);

                $data[] = [
                    'product_id' => (int) $productId,
                    'branch_id' => (int) $branchId,
                    'current_stock' => $currentStock,
                    'reserved_stock' => $reservedStock,
                    'available_stock' => $availableStock,
                    'last_updated' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        // Insert in chunks to be safe
        $chunkSize = 500;
        foreach (array_chunk($data, $chunkSize) as $chunk) {
            $db->table('inventory')->insertBatch($chunk);
        }
    }
}
