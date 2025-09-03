<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSuratWorkflowTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'surat_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'from_state' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'to_state' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'actor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'action' => [
                'type'       => 'ENUM',
                'constraint' => ['submit', 'approve', 'reject', 'forward', 'revise', 'complete', 'cancel'],
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('surat_id', 'surat', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('actor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey('surat_id');
        $this->forge->addKey('created_at');
        $this->forge->createTable('surat_workflow');
    }

    public function down()
    {
        $this->forge->dropTable('surat_workflow');
    }
}
