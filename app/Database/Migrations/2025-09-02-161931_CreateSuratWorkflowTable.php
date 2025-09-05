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
            'from_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'to_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'action_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'action_type' => [
                'type'       => 'ENUM',
                'constraint' => ['SUBMIT', 'APPROVE', 'REJECT', 'REVISE', 'DISPOSE', 'COMPLETE', 'CANCEL', 'UPDATE'],
            ],
            'keterangan' => [
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
        $this->forge->addForeignKey('action_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey('surat_id');
        $this->forge->addKey('created_at');
        $this->forge->createTable('surat_workflow');
    }

    public function down()
    {
        $this->forge->dropTable('surat_workflow');
    }
}
