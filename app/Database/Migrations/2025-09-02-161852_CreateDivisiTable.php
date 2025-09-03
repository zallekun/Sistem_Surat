<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDivisiTable extends Migration
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
            'nama_divisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_divisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kepala_divisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addKey(['fakultas_id', 'kode_divisi']);
        $this->forge->createTable('divisi');
    }

    public function down()
    {
        $this->forge->dropTable('divisi');
    }
}