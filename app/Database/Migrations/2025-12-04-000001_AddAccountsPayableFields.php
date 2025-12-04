<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountsPayableFields extends Migration
{
    public function up()
    {
        // Add payment-related columns to purchase_orders table
        $fields = [
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['unpaid', 'partial', 'paid'],
                'default' => 'unpaid',
                'null' => false,
                'after' => 'status'
            ],
            'payment_date' => [
                'type' => 'DATE',
                'null' => true,
                'default' => null,
                'after' => 'payment_status'
            ],
            'payment_due_date' => [
                'type' => 'DATE',
                'null' => true,
                'default' => null,
                'after' => 'payment_date'
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => null,
                'after' => 'payment_due_date'
            ],
            'payment_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'default' => null,
                'after' => 'payment_method'
            ],
            'payment_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
                'after' => 'payment_reference'
            ]
        ];

        $this->forge->addColumn('purchase_orders', $fields);

        // Add indexes for better performance
        $this->db->query('ALTER TABLE `purchase_orders` ADD INDEX `idx_payment_status` (`payment_status`)');
        $this->db->query('ALTER TABLE `purchase_orders` ADD INDEX `idx_payment_due_date` (`payment_due_date`)');

        // Create payment_logs table
        $this->forge->addField([
            'payment_log_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false,
                'auto_increment' => true
            ],
            'purchase_order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false
            ],
            'payment_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00'
            ],
            'payment_date' => [
                'type' => 'DATE'
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'reference_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'processed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => false,
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ]
        ]);

        $this->forge->addKey('payment_log_id', true);
        $this->forge->addKey('purchase_order_id');
        $this->forge->addKey('processed_by');
        
        $this->forge->createTable('payment_logs', true);

        // Add foreign keys
        $this->forge->addForeignKey('purchase_order_id', 'purchase_orders', 'purchase_order_id', 'CASCADE', 'CASCADE', 'payment_logs');
        $this->forge->addForeignKey('processed_by', 'users', 'user_id', 'SET NULL', 'CASCADE', 'payment_logs');

        // Update existing delivered orders to set default payment due date (30 days from delivery)
        $this->db->query("
            UPDATE `purchase_orders` 
            SET `payment_due_date` = DATE_ADD(`actual_delivery_date`, INTERVAL 30 DAY)
            WHERE `status` = 'delivered' 
              AND `actual_delivery_date` IS NOT NULL 
              AND `payment_due_date` IS NULL
        ");

        // Set default payment status for delivered orders
        $this->db->query("
            UPDATE `purchase_orders` 
            SET `payment_status` = 'unpaid'
            WHERE `status` = 'delivered' 
              AND `payment_status` IS NULL
        ");
    }

    public function down()
    {
        // Drop foreign keys first
        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query('ALTER TABLE `payment_logs` DROP FOREIGN KEY `payment_logs_purchase_order_id_foreign`');
            $this->db->query('ALTER TABLE `payment_logs` DROP FOREIGN KEY `payment_logs_processed_by_foreign`');
        }

        // Drop payment_logs table
        $this->forge->dropTable('payment_logs', true);

        // Drop indexes from purchase_orders
        $this->db->query('ALTER TABLE `purchase_orders` DROP INDEX `idx_payment_status`');
        $this->db->query('ALTER TABLE `purchase_orders` DROP INDEX `idx_payment_due_date`');

        // Drop columns from purchase_orders table
        $this->forge->dropColumn('purchase_orders', [
            'payment_status',
            'payment_date',
            'payment_due_date',
            'payment_method',
            'payment_reference',
            'payment_notes'
        ]);
    }
}

