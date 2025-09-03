<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DivisiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Divisi untuk Fakultas Teknik (fakultas_id = 1)
            [
                'fakultas_id' => 1,
                'nama_divisi' => 'Tata Usaha Fakultas Teknik',
                'kode_divisi' => 'TU-FT',
                'deskripsi' => 'Divisi administrasi dan pelayanan umum fakultas teknik',
                'kepala_divisi' => 'Dra. Siti Nurhalimah',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 1,
                'nama_divisi' => 'Sub Bagian Akademik FT',
                'kode_divisi' => 'AKAD-FT',
                'deskripsi' => 'Sub bagian yang menangani administrasi akademik mahasiswa',
                'kepala_divisi' => 'Drs. Ahmad Fauzi, M.Si',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 1,
                'nama_divisi' => 'Sub Bagian Kemahasiswaan FT',
                'kode_divisi' => 'KEMHS-FT',
                'deskripsi' => 'Sub bagian yang menangani kegiatan kemahasiswaan',
                'kepala_divisi' => 'Sri Wahyuni, S.Pd',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Divisi untuk Fakultas Ekonomi dan Bisnis (fakultas_id = 2)
            [
                'fakultas_id' => 2,
                'nama_divisi' => 'Tata Usaha Fakultas Ekonomi',
                'kode_divisi' => 'TU-FEB',
                'deskripsi' => 'Divisi administrasi dan pelayanan umum fakultas ekonomi',
                'kepala_divisi' => 'Drs. Budi Santoso',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 2,
                'nama_divisi' => 'Sub Bagian Keuangan FEB',
                'kode_divisi' => 'KEU-FEB',
                'deskripsi' => 'Sub bagian yang menangani administrasi keuangan fakultas',
                'kepala_divisi' => 'Ani Rahayu, S.E',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Divisi untuk Fakultas Ilmu Komputer (fakultas_id = 3)
            [
                'fakultas_id' => 3,
                'nama_divisi' => 'Tata Usaha FIKOM',
                'kode_divisi' => 'TU-FIKOM',
                'deskripsi' => 'Divisi administrasi dan pelayanan umum fakultas ilmu komputer',
                'kepala_divisi' => 'Ir. Bambang Setiawan, M.T',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'fakultas_id' => 3,
                'nama_divisi' => 'Sub Bagian IT Support',
                'kode_divisi' => 'IT-FIKOM',
                'deskripsi' => 'Sub bagian yang menangani dukungan teknologi informasi',
                'kepala_divisi' => 'Eko Prasetyo, S.Kom, M.T',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('divisi')->insertBatch($data);
    }
}
