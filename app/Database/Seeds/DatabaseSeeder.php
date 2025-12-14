<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Run seeders in the correct order to respect foreign key constraints
        $this->call('BranchSeeder');
        $this->call('SupplierSeeder');
        $this->call('CategorySeeder');
        $this->call('UserSeeder');
        $this->call('ProductSeeder');
        $this->call('UserBranchSeeder');
        $this->call('InventorySeeder');
        $this->call('OutOfStockSeeder');
        $this->call('PurchaseOrderSeeder');
        $this->call('PurchaseOrderItemSeeder');
        $this->call('StockMovementSeeder');
    }
}
