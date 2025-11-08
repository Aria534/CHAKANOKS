<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PurchaseOrderItemSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Fetch PO IDs
        $poRows = $db->table('purchase_orders')->select('purchase_order_id')->get()->getResultArray();
        $poIds = array_column($poRows, 'purchase_order_id');

        // Fetch product IDs
        $productRows = $db->table('products')->select('product_id, unit_price')->get()->getResultArray();

        if (empty($poIds) || empty($productRows)) {
            return;
        }

        $data = [];

        foreach ($poIds as $poId) {
            $numItems = rand(1, 3); // 1 to 3 items per order
            $usedProducts = [];

            for ($i = 0; $i < $numItems; $i++) {
                do {
                    $product = $productRows[array_rand($productRows)];
                } while (in_array($product['product_id'], $usedProducts));

                $usedProducts[] = $product['product_id'];
                $quantity = rand(5, 50);
                $unitPrice = $product['unit_price'];

                $data[] = [
                    'purchase_order_id' => $poId,
                    'product_id' => $product['product_id'],
                    'quantity_requested' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $quantity * $unitPrice,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        $db->table('purchase_order_items')->insertBatch($data);
    }
}
