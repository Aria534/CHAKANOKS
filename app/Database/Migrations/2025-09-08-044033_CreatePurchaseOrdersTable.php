<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'purchase_order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'po_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true
            ],
            'branch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'supplier_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'requested_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'User ID who requested the order'
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User ID who approved the order'
            ],
            'status' => [
                'type' => "ENUM('pending','approved','rejected','ordered','delivered','cancelled')",
                'default' => 'pending'
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00
            ],
            'requested_date' => [
                'type' => 'DATETIME'
            ],
            'approved_date' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'expected_delivery_date' => [
                'type' => 'DATE',
                'null' => true
            ],
            'actual_delivery_date' => [
                'type' => 'DATE',
                'null' => true
            ],
            'notes' => [
                'type' => 'TEXT',
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
        
        $this->forge->addKey('purchase_order_id', true);
        $this->forge->addForeignKey('branch_id', 'branches', 'branch_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'supplier_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('requested_by', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_orders');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_orders');
    }
}
