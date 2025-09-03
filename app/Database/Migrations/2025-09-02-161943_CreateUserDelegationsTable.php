<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserDelegationsTable extends Migration
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
            'from_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'User yang mendelegasikan wewenang',
            ],
            'to_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'User yang menerima delegasi wewenang',
            ],
            'permissions' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Specific permissions yang didelegasikan dalam format JSON',
            ],
            'alasan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Alasan delegasi (cuti, dinas luar, sakit, dll)',
            ],
            'start_date' => [
                'type'    => 'DATE',
                'comment' => 'Tanggal mulai delegasi',
            ],
            'end_date' => [
                'type'    => 'DATE',
                'comment' => 'Tanggal berakhir delegasi',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'expired', 'revoked', 'completed'],
                'default'    => 'active',
                'comment'    => 'Status delegasi: active, expired, revoked, completed',
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => true,
                'comment' => 'Flag aktif/non-aktif delegasi',
            ],
            'auto_revoke' => [
                'type'    => 'BOOLEAN',
                'default' => true,
                'comment' => 'Otomatis revoke setelah end_date',
            ],
            'scope' => [
                'type'       => 'ENUM',
                'constraint' => ['all', 'specific_surat', 'specific_category'],
                'default'    => 'all',
                'comment'    => 'Scope delegasi: all (semua), specific_surat, specific_category',
            ],
            'scope_data' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Data scope jika specific (ID surat atau kategori)',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan tambahan untuk delegasi',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'User yang membuat record delegasi',
            ],
            'approved_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'User yang menyetujui delegasi (untuk approval workflow)',
            ],
            'revoked_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'User yang membatalkan delegasi',
            ],
            'revoked_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp pembatalan delegasi',
            ],
            'revoke_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Alasan pembatalan delegasi',
            ],
            'usage_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Jumlah penggunaan delegasi (untuk monitoring)',
            ],
            'last_used_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp terakhir kali delegasi digunakan',
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
        
        // Primary Key
        $this->forge->addPrimaryKey('id');
        
        // Foreign Keys
        $this->forge->addForeignKey('from_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('to_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('revoked_by', 'users', 'id', 'CASCADE', 'SET NULL');
        
        // Indexes untuk performance
        $this->forge->addKey(['from_user_id', 'status']);
        $this->forge->addKey(['to_user_id', 'is_active']);
        $this->forge->addKey(['start_date', 'end_date']);
        $this->forge->addKey('status');
        $this->forge->addKey('is_active');
        
        // Unique constraint: satu user tidak bisa delegate ke user yang sama dalam periode overlap
        $this->forge->addUniqueKey(['from_user_id', 'to_user_id', 'start_date', 'end_date'], 'unique_delegation_period');
        
        // Create table
        $this->forge->createTable('user_delegations');
        
        // Add check constraint untuk memastikan end_date >= start_date
        $this->db->query("ALTER TABLE user_delegations ADD CONSTRAINT chk_delegation_dates CHECK (end_date >= start_date)");
    }

    public function down()
    {
        $this->forge->dropTable('user_delegations');
    }
}