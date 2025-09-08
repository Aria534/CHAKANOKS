<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSuppliersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'supplier_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'supplier_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'contact_person' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'address' => [
                'type' => 'TEXT'
            ],
            'payment_terms' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'delivery_time' => [
                'type' => 'INT',
                'constraint' => 3,
                'comment' => 'Delivery time in days'
            ],
            'status' => [
                'type' => "ENUM('active','inactive')",
                'default' => 'active'
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
        
        $this->forge->addKey('supplier_id', true);
        $this->forge->createTable('suppliers');
    }

    public function down()
    {
        $this->forge->dropTable('suppliers');
    }
}
