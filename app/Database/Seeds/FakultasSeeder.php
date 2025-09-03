<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FakultasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_fakultas' => 'Fakultas Teknik',
                'kode_fakultas' => 'FT',
                'alamat' => 'Jl. Raya Kampus No. 1',
                'telepon' => '0271-123456',
                'email' => 'ft@universitas.ac.id',
                'website' => 'https://ft.universitas.ac.id',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_fakultas' => 'Fakultas Ekonomi dan Bisnis',
                'kode_fakultas' => 'FEB',
                'alamat' => 'Jl. Raya Kampus No. 2',
                'telepon' => '0271-123457',
                'email' => 'feb@universitas.ac.id',
                'website' => 'https://feb.universitas.ac.id',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_fakultas' => 'Fakultas Ilmu Komputer',
                'kode_fakultas' => 'FIKOM',
                'alamat' => 'Jl. Raya Kampus No. 3',
                'telepon' => '0271-123458',
                'email' => 'fikom@universitas.ac.id',
                'website' => 'https://fikom.universitas.ac.id',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('fakultas')->insertBatch($data);
    }
}
