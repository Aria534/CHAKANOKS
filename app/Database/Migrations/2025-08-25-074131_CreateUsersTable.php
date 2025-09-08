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
			'email'     => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
			'first_name' => ['type' => 'VARCHAR', 'constraint' => 50],
			'last_name' => ['type' => 'VARCHAR', 'constraint' => 50],
			'phone'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
			'role'      => ['type' => "ENUM('central_admin','branch_manager','inventory_staff','supplier','logistics_coordinator','franchise_manager','system_admin')", 'default' => 'inventory_staff'],
			'status'    => ['type' => "ENUM('active','inactive')", 'default' => 'active'],
			'last_login' => ['type' => 'DATETIME', 'null' => true],
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
