<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSuratTable extends Migration
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
            'nomor_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'perihal' => [
                'type' => 'TEXT',
            ],
            'tanggal_surat' => [
                'type' => 'DATE',
            ],
            'kategori' => [
                'type'       => 'ENUM',
                'constraint' => ['akademik', 'kemahasiswaan', 'kepegawaian', 'keuangan', 'umum'],
                'default'    => 'umum',
            ],
            'prioritas' => [
                'type'       => 'ENUM',
                'constraint' => ['normal', 'urgent', 'sangat_urgent'],
                'default'    => 'normal',
            ],
            'tujuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'prodi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'current_holder' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'User yang sedang handle surat ini',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'DRAFT',
                    'SUBMITTED', 
                    'UNDER_REVIEW',
                    'NEED_REVISION',
                    'APPROVED_L1',
                    'APPROVED_L2',
                    'READY_DISPOSISI',
                    'IN_PROCESS',
                    'COMPLETED',
                    'REJECTED',
                    'CANCELLED'
                ],
                'default'    => 'DRAFT',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deadline' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('prodi_id', 'prodi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('current_holder', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addKey('status');
        $this->forge->addKey('kategori');
        $this->forge->addKey('prioritas');
        $this->forge->addKey('tanggal_surat');
        $this->forge->createTable('surat');
    }

    public function down()
    {
        $this->forge->dropTable('surat');
    }
}