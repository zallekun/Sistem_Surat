<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApprovalTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'level' => [
                'type'       => 'INT',
                'constraint' => 2,
                'comment'    => 'Approval level: 1=admin, 2=operational, 3=managerial, 4=executive',
            ],
            'status_approval' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
            'alasan_reject' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_approval' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('surat_id', 'surat', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['surat_id', 'level']);
        $this->forge->addKey('status_approval');
        $this->forge->createTable('approval');
    }

    public function down()
    {
        $this->forge->dropTable('approval');
    }
}
