<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Fetch branch IDs (exclude central)
        $branchRows = $db->table('branches')->select('branch_id')->where('branch_code !=', 'CENTRAL')->get()->getResultArray();
        $branchIds = array_column($branchRows, 'branch_id');

        // Fetch supplier IDs
        $supplierRows = $db->table('suppliers')->select('supplier_id')->get()->getResultArray();
        $supplierIds = array_column($supplierRows, 'supplier_id');

        // Fetch user IDs (assume some are branch users)
        $userRows = $db->table('users')->select('user_id')->get()->getResultArray();
        $userIds = array_column($userRows, 'user_id');

        if (empty($branchIds) || empty($supplierIds) || empty($userIds)) {
            return; // No data to seed
        }

        $statuses = ['pending', 'approved', 'delivered'];
        $data = [];
        $poCounter = 1;

        // Create orders for each branch
        foreach ($branchIds as $branchId) {
            for ($i = 0; $i < 5; $i++) { // 5 orders per branch
                $status = $statuses[array_rand($statuses)];
                $requestedDate = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
                $approvedDate = $status != 'pending' ? date('Y-m-d H:i:s', strtotime($requestedDate . ' +1 day')) : null;
                $expectedDelivery = date('Y-m-d', strtotime($requestedDate . ' +' . rand(7, 14) . ' days'));
                $actualDelivery = $status == 'delivered' ? date('Y-m-d', strtotime($expectedDelivery . ' -' . rand(0, 2) . ' days')) : null;

                $data[] = [
                    'po_number' => 'PO-' . date('Y') . '-' . str_pad($poCounter++, 3, '0', STR_PAD_LEFT),
                    'branch_id' => $branchId,
                    'supplier_id' => $supplierIds[array_rand($supplierIds)],
                    'requested_by' => $userIds[array_rand($userIds)],
                    'approved_by' => $status != 'pending' ? $userIds[array_rand($userIds)] : null,
                    'status' => $status,
                    'total_amount' => rand(1000, 50000) / 100, // Random amount
                    'requested_date' => $requestedDate,
                    'approved_date' => $approvedDate,
                    'expected_delivery_date' => $expectedDelivery,
                    'actual_delivery_date' => $actualDelivery,
                    'notes' => 'Sample order ' . ($i + 1),
                    'created_at' => $requestedDate,
                    'updated_at' => $requestedDate
                ];
            }
        }

        $db->table('purchase_orders')->insertBatch($data);
    }
}
