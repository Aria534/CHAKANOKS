<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'product_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'product_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true
            ],
            'barcode' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'supplier_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'unit_of_measure' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'comment' => 'kg, pieces, liters, etc.'
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2'
            ],
            'minimum_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'maximum_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'is_perishable' => [
                'type' => 'BOOLEAN',
                'default' => false
            ],
            'shelf_life_days' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
                'comment' => 'Shelf life in days for perishable items'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true
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
        
        $this->forge->addKey('product_id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'category_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'supplier_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
