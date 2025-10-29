<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersAddFields extends Migration
{
    public function up()
    {
        // Add columns expected by seeders and app code
        $this->forge->addColumn('users', [
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'unique'     => true,
                'after'      => 'username',
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'email',
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'first_name',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'last_name',
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status',
            ],
        ]);

        // Expand role enum to match roles used by the seeders/app
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'    => "ENUM('central_admin','system_admin','franchise_manager','logistics_coordinator','supplier','branch_manager','inventory_staff')",
                'default' => 'branch_manager',
            ],
        ]);
    }

    public function down()
    {
        // Revert role enum back to the original definition
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'    => "ENUM('admin','staff','supplier','customer')",
                'default' => 'staff',
            ],
        ]);

        // Drop the added columns
        if ($this->db->fieldExists('email', 'users')) {
            $this->forge->dropColumn('users', 'email');
        }
        if ($this->db->fieldExists('first_name', 'users')) {
            $this->forge->dropColumn('users', 'first_name');
        }
        if ($this->db->fieldExists('last_name', 'users')) {
            $this->forge->dropColumn('users', 'last_name');
        }
        if ($this->db->fieldExists('phone', 'users')) {
            $this->forge->dropColumn('users', 'phone');
        }
        if ($this->db->fieldExists('last_login', 'users')) {
            $this->forge->dropColumn('users', 'last_login');
        }
    }
}
