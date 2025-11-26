<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'inventory_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'branch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'current_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'reserved_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Stock reserved for pending orders'
            ],
            'available_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'current_stock - reserved_stock'
            ],
            'last_updated' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        
        $this->forge->addKey('inventory_id', true);
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('branch_id', 'branches', 'branch_id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['product_id', 'branch_id']);
        $this->forge->createTable('inventory');
    }

    public function down()
    {
        $this->forge->dropTable('inventory');
    }
}
