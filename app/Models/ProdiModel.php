<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdiModel extends Model
{
    protected $table = 'prodi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'fakultas_id', 'nama_prodi', 'kode_prodi', 'jenjang',
        'kaprodi', 'akreditasi', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_active' => 'boolean',
        'fakultas_id' => 'integer',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'fakultas_id' => 'required|integer|is_not_unique[fakultas.id]',
        'nama_prodi' => 'required|min_length[3]|max_length[100]',
        'kode_prodi' => 'required|min_length[2]|max_length[20]|is_unique[prodi.kode_prodi,id,{id}]',
        'jenjang' => 'required|in_list[D3,D4,S1,S2,S3]',
        'kaprodi' => 'permit_empty|max_length[100]',
        'akreditasi' => 'permit_empty|in_list[A,B,C,Belum Terakreditasi]',
    ];

    protected $validationMessages = [
        'fakultas_id' => [
            'is_not_unique' => 'Fakultas tidak ditemukan'
        ],
        'kode_prodi' => [
            'is_unique' => 'Kode program studi sudah digunakan'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getProdiWithFakultas(): array
    {
        return $this->select('prodi.*, fakultas.nama_fakultas, fakultas.kode_fakultas')
                   ->join('fakultas', 'fakultas.id = prodi.fakultas_id')
                   ->findAll();
    }

    public function getProdiByFakultas(int $fakultasId): array
    {
        return $this->where('fakultas_id', $fakultasId)
                   ->where('is_active', true)
                   ->findAll();
    }

    public function getActiveProdi(): array
    {
        return $this->where('is_active', true)->findAll();
    }

    public function getProdiWithStats(): array
    {
        return $this->select('prodi.*, fakultas.nama_fakultas,
                            COUNT(DISTINCT users.id) as total_users,
                            COUNT(DISTINCT surat.id) as total_surat')
                   ->join('fakultas', 'fakultas.id = prodi.fakultas_id')
                   ->join('users', 'users.prodi_id = prodi.id', 'left')
                   ->join('surat', 'surat.prodi_id = prodi.id', 'left')
                   ->groupBy('prodi.id')
                   ->findAll();
    }
}