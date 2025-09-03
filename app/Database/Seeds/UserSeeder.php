<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Super Admin
            [
                'nama' => 'Super Administrator',
                'email' => 'superadmin@universitas.ac.id',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'super_admin',
                'prodi_id' => null,
                'divisi_id' => null,
                'nip' => '199001011990011001',
                'jabatan' => 'Super Administrator Sistem',
                'telepon' => '081234567890',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Dekan
            [
                'nama' => 'Prof. Dr. Ir. Hartono, M.T',
                'email' => 'dekan@universitas.ac.id',
                'password' => password_hash('dekan123', PASSWORD_BCRYPT),
                'role' => 'dekan',
                'prodi_id' => null,
                'divisi_id' => 1, // TU-FT
                'nip' => '196505051990011001',
                'jabatan' => 'Dekan',
                'telepon' => '081234567891',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Wakil Dekan Akademik
            [
                'nama' => 'Dr. Ir. Suyanto, M.T',
                'email' => 'wd.akademik@universitas.ac.id',
                'password' => password_hash('wdakademik123', PASSWORD_BCRYPT),
                'role' => 'wd_akademik',
                'prodi_id' => null,
                'divisi_id' => 2, // AKAD-FT
                'nip' => '197203151998011001',
                'jabatan' => 'Wakil Dekan Bidang Akademik',
                'telepon' => '081234567892',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Wakil Dekan Kemahasiswaan
            [
                'nama' => 'Dr. Dra. Sri Mulyani, M.Si',
                'email' => 'wd.kemahasiswaan@universitas.ac.id',
                'password' => password_hash('wdkemahasiswaan123', PASSWORD_BCRYPT),
                'role' => 'wd_kemahasiswa',
                'prodi_id' => null,
                'divisi_id' => 3, // KEMHS-FT
                'nip' => '197408201999031001',
                'jabatan' => 'Wakil Dekan Bidang Kemahasiswaan',
                'telepon' => '081234567893',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Wakil Dekan Umum
            [
                'nama' => 'Ir. Bambang Santoso, M.T',
                'email' => 'wd.umum@universitas.ac.id',
                'password' => password_hash('wdumum123', PASSWORD_BCRYPT),
                'role' => 'wd_umum',
                'prodi_id' => null,
                'divisi_id' => 1, // TU-FT
                'nip' => '196812121995011001',
                'jabatan' => 'Wakil Dekan Bidang Umum',
                'telepon' => '081234567894',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Kepala Bagian Tata Usaha
            [
                'nama' => 'Dra. Siti Nurhalimah',
                'email' => 'kabag.tu@universitas.ac.id',
                'password' => password_hash('kabagtu123', PASSWORD_BCRYPT),
                'role' => 'kabag_tu',
                'prodi_id' => null,
                'divisi_id' => 1, // TU-FT
                'nip' => '196709251992032001',
                'jabatan' => 'Kepala Bagian Tata Usaha',
                'telepon' => '081234567895',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Staff Umum
            [
                'nama' => 'Ahmad Fauzi, S.Pd',
                'email' => 'staff.umum@universitas.ac.id',
                'password' => password_hash('staffumum123', PASSWORD_BCRYPT),
                'role' => 'staff_umum',
                'prodi_id' => null,
                'divisi_id' => 1, // TU-FT
                'nip' => '198503121010011001',
                'jabatan' => 'Staff Administrasi Umum',
                'telepon' => '081234567896',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Kepala Urusan Akademik
            [
                'nama' => 'Drs. Ahmad Fauzi, M.Si',
                'email' => 'kaur.akademik@universitas.ac.id',
                'password' => password_hash('kaurakademik123', PASSWORD_BCRYPT),
                'role' => 'kaur_akademik',
                'prodi_id' => null,
                'divisi_id' => 2, // AKAD-FT
                'nip' => '196801151994031001',
                'jabatan' => 'Kepala Urusan Akademik',
                'telepon' => '081234567897',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Kepala Urusan Kemahasiswaan
            [
                'nama' => 'Sri Wahyuni, S.Pd',
                'email' => 'kaur.kemahasis@universitas.ac.id',
                'password' => password_hash('kaurkemahasis123', PASSWORD_BCRYPT),
                'role' => 'kaur_kemahasis',
                'prodi_id' => null,
                'divisi_id' => 3, // KEMHS-FT
                'nip' => '198007182005012001',
                'jabatan' => 'Kepala Urusan Kemahasiswaan',
                'telepon' => '081234567898',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Kepala Urusan Kepegawaian
            [
                'nama' => 'Drs. Budi Santoso',
                'email' => 'kaur.kepegawai@universitas.ac.id',
                'password' => password_hash('kaurkepegawai123', PASSWORD_BCRYPT),
                'role' => 'kaur_kepegawai',
                'prodi_id' => null,
                'divisi_id' => 4, // TU-FEB
                'nip' => '196504121990031001',
                'jabatan' => 'Kepala Urusan Kepegawaian',
                'telepon' => '081234567899',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Kepala Urusan Keuangan
            [
                'nama' => 'Ani Rahayu, S.E',
                'email' => 'kaur.keuangan@universitas.ac.id',
                'password' => password_hash('kaurkeuangan123', PASSWORD_BCRYPT),
                'role' => 'kaur_keuangan',
                'prodi_id' => null,
                'divisi_id' => 5, // KEU-FEB
                'nip' => '197902142003122001',
                'jabatan' => 'Kepala Urusan Keuangan',
                'telepon' => '081234567800',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Admin Prodi - Teknik Informatika
            [
                'nama' => 'Rina Sari, S.T',
                'email' => 'admin.ti@universitas.ac.id',
                'password' => password_hash('adminprodi123', PASSWORD_BCRYPT),
                'role' => 'admin_prodi',
                'prodi_id' => 1, // Teknik Informatika
                'divisi_id' => null,
                'nip' => '199105252018032001',
                'jabatan' => 'Admin Program Studi',
                'telepon' => '081234567801',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Admin Prodi - Teknik Sipil
            [
                'nama' => 'Budi Prasetyo, S.T',
                'email' => 'admin.ts@universitas.ac.id',
                'password' => password_hash('adminprodi123', PASSWORD_BCRYPT),
                'role' => 'admin_prodi',
                'prodi_id' => 2, // Teknik Sipil
                'divisi_id' => null,
                'nip' => '198908142015031001',
                'jabatan' => 'Admin Program Studi',
                'telepon' => '081234567802',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Admin Prodi - Manajemen
            [
                'nama' => 'Sari Dewi, S.E',
                'email' => 'admin.mnj@universitas.ac.id',
                'password' => password_hash('adminprodi123', PASSWORD_BCRYPT),
                'role' => 'admin_prodi',
                'prodi_id' => 5, // Manajemen
                'divisi_id' => null,
                'nip' => '199203182017032001',
                'jabatan' => 'Admin Program Studi',
                'telepon' => '081234567803',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Admin Prodi - Ilmu Komputer
            [
                'nama' => 'Eko Prasetyo, S.Kom',
                'email' => 'admin.ilkom@universitas.ac.id',
                'password' => password_hash('adminprodi123', PASSWORD_BCRYPT),
                'role' => 'admin_prodi',
                'prodi_id' => 9, // Ilmu Komputer
                'divisi_id' => null,
                'nip' => '199401262019031001',
                'jabatan' => 'Admin Program Studi',
                'telepon' => '081234567804',
                'workload' => 0,
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
