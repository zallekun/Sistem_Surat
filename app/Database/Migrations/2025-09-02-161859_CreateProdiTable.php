<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProdiTable extends Migration
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
            'fakultas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_prodi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_prodi' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
            ],
            'jenjang' => [
                'type'       => 'ENUM',
                'constraint' => ['D3', 'D4', 'S1', 'S2', 'S3'],
                'default'    => 'S1',
            ],
            'kaprodi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'akreditasi' => [
                'type'       => 'ENUM',
                'constraint' => ['A', 'B', 'C', 'Belum Terakreditasi'],
                'null'       => true,
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => true,
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
        $this->forge->addForeignKey('fakultas_id', 'fakultas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('prodi');
    }

    public function down()
    {
        $this->forge->dropTable('prodi');
    }
}