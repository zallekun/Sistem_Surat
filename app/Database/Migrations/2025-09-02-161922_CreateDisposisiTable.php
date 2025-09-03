<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDisposisiTable extends Migration
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
            'nomor_disposisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
            'jumlah_lampiran' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'dari' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'kepada' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'instruksi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sifat' => [
                'type'       => 'ENUM',
                'constraint' => ['biasa', 'segera', 'sangat_segera', 'rahasia'],
                'default'    => 'biasa',
            ],
            'batas_waktu' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('surat_id', 'surat', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey('tanggal_masuk');
        $this->forge->createTable('disposisi');
    }

    public function down()
    {
        $this->forge->dropTable('disposisi');
    }
}
