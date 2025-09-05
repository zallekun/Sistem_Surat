<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'surat_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['WORKFLOW', 'SYSTEM', 'REMINDER', 'DEADLINE'],
                'default'    => 'SYSTEM',
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'action_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'is_read' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
            'is_email_sent' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['LOW', 'NORMAL', 'HIGH', 'URGENT'],
                'default'    => 'NORMAL',
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('surat_id', 'surat', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addKey('user_id');
        $this->forge->addKey(['is_read', 'created_at']);
        $this->forge->addKey('type');
        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}