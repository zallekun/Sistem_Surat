<?php

// ========================================
// CreateLampiranTable.php
// ========================================

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLampiranTable extends Migration
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
            'nama_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'nama_asli' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'path_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'ukuran_file' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'versi' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 1,
            ],
            'is_final' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'checksum' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'comment'    => 'File integrity check',
            ],
            'uploaded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('surat_id', 'surat', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['surat_id', 'versi']);
        $this->forge->createTable('lampiran');
    }

    public function down()
    {
        $this->forge->dropTable('lampiran');
    }
}