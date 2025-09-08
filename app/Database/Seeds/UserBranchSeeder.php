<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserBranchSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Map usernames to IDs
        $users = $db->table('users')->select('user_id, username')->get()->getResultArray();
        $usernameToId = [];
        foreach ($users as $u) {
            $usernameToId[$u['username']] = (int) $u['user_id'];
        }

        // Map branch codes to IDs
        $branches = $db->table('branches')->select('branch_id, branch_code')->get()->getResultArray();
        $codeToBranchId = [];
        foreach ($branches as $b) {
            $codeToBranchId[strtoupper($b['branch_code'])] = (int) $b['branch_id'];
        }

        $links = [
            // Central Admins -> CENTRAL
            ['username' => 'admin', 'branch_code' => 'CENTRAL', 'is_primary' => true],
            ['username' => 'Central Admin', 'branch_code' => 'CENTRAL', 'is_primary' => true],

            // Branch Managers -> their branches (matching new usernames)
            ['username' => 'Branch Manager1', 'branch_code' => 'SM001', 'is_primary' => true],
            ['username' => 'Branch Manager2', 'branch_code' => 'ABR001', 'is_primary' => true],
            ['username' => 'Branch Manager3', 'branch_code' => 'GAI001', 'is_primary' => true],
            ['username' => 'Branch Manager4', 'branch_code' => 'NCC001', 'is_primary' => true],
            ['username' => 'Branch Manager5', 'branch_code' => 'VIC001', 'is_primary' => true],

            // Inventory staff (single account) -> assign to two branches
            ['username' => 'staff1', 'branch_code' => 'SM001', 'is_primary' => false],
            ['username' => 'staff1', 'branch_code' => 'ABR001', 'is_primary' => false],
        ];

        foreach ($links as $link) {
            $userId = $usernameToId[$link['username']] ?? null;
            $branchId = $codeToBranchId[strtoupper($link['branch_code'])] ?? null;
            if (!$userId || !$branchId) {
                continue;
            }
            $exists = $db->table('user_branches')
                ->where('user_id', $userId)
                ->where('branch_id', $branchId)
                ->get()->getFirstRow();
            if ($exists) {
                continue;
            }
            $db->table('user_branches')->insert([
                'user_id' => $userId,
                'branch_id' => $branchId,
                'is_primary' => (bool) $link['is_primary'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
