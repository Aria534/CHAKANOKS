<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserBranchesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_branch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'branch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'is_primary' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'comment' => 'Primary branch for the user'
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
        
        $this->forge->addKey('user_branch_id', true);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('branch_id', 'branches', 'branch_id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['user_id', 'branch_id']);
        $this->forge->createTable('user_branches');
    }

    public function down()
    {
        $this->forge->dropTable('user_branches');
    }
}
