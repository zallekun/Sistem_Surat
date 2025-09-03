<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Program Studi Fakultas Teknik (fakultas_id = 1)
            [
                'fakultas_id' => 1,
                'nama_prodi' => 'Teknik Informatika',
                'kode_prodi' => 'TI',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Ir. Agus Setiawan, M.T',
                'akreditasi' => 'A',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 1,
                'nama_prodi' => 'Teknik Sipil',
                'kode_prodi' => 'TS',
                'jenjang' => 'S1',
                'kaprodi' => 'Prof. Dr. Ir. Bambang Riyanto, M.T',
                'akreditasi' => 'A',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 1,
                'nama_prodi' => 'Teknik Mesin',
                'kode_prodi' => 'TM',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Ir. Suharto, M.T',
                'akreditasi' => 'B',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 1,
                'nama_prodi' => 'Diploma Teknik Elektro',
                'kode_prodi' => 'DTE',
                'jenjang' => 'D3',
                'kaprodi' => 'Ir. Ahmad Fauzi, M.T',
                'akreditasi' => 'B',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Program Studi Fakultas Ekonomi dan Bisnis (fakultas_id = 2)
            [
                'fakultas_id' => 2,
                'nama_prodi' => 'Manajemen',
                'kode_prodi' => 'MNJ',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Dra. Sri Hartini, M.Si',
                'akreditasi' => 'A',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 2,
                'nama_prodi' => 'Akuntansi',
                'kode_prodi' => 'AKT',
                'jenjang' => 'S1',
                'kaprodi' => 'Prof. Dr. Bambang Sutopo, M.Com, Akt',
                'akreditasi' => 'A',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 2,
                'nama_prodi' => 'Ekonomi Pembangunan',
                'kode_prodi' => 'EP',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Ir. Retno Setyorini, M.Si',
                'akreditasi' => 'B',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 2,
                'nama_prodi' => 'Magister Manajemen',
                'kode_prodi' => 'MM',
                'jenjang' => 'S2',
                'kaprodi' => 'Prof. Dr. Slamet Riyadi, M.M',
                'akreditasi' => 'A',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Program Studi Fakultas Ilmu Komputer (fakultas_id = 3)
            [
                'fakultas_id' => 3,
                'nama_prodi' => 'Ilmu Komputer',
                'kode_prodi' => 'ILKOM',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Eng. Yohanes Suyanto, S.T, M.Eng',
                'akreditasi' => 'A',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 3,
                'nama_prodi' => 'Sistem Informasi',
                'kode_prodi' => 'SI',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Ir. Eko Sediyono, M.T',
                'akreditasi' => 'A',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 3,
                'nama_prodi' => 'Teknologi Informasi',
                'kode_prodi' => 'TIF',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Christy Atika Sari, S.T, M.T',
                'akreditasi' => 'B',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 3,
                'nama_prodi' => 'Diploma Sistem Informasi',
                'kode_prodi' => 'DSI',
                'jenjang' => 'D3',
                'kaprodi' => 'Ir. Aris Puji Widodo, M.T',
                'akreditasi' => 'B',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('prodi')->insertBatch($data);
    }
}
