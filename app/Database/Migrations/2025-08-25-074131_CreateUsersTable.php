<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username'  => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'password'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'      => ['type' => "ENUM('admin','staff','supplier','customer')", 'default' => 'staff'],
            'status'    => ['type' => "ENUM('active','inactive')", 'default' => 'active'],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('user_id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
