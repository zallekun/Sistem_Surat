<?php

namespace App\Models;

use CodeIgniter\Model;

class DivisiModel extends Model
{
    protected $table = 'divisi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'fakultas_id', 'nama_divisi', 'kode_divisi', 
        'deskripsi', 'kepala_divisi', 'is_active'
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
        'nama_divisi' => 'required|min_length[3]|max_length[100]',
        'kode_divisi' => 'required|min_length[2]|max_length[20]',
        'kepala_divisi' => 'permit_empty|max_length[100]',
    ];

    protected $validationMessages = [
        'fakultas_id' => [
            'is_not_unique' => 'Fakultas tidak ditemukan'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getDivisiWithFakultas(): array
    {
        return $this->select('divisi.*, fakultas.nama_fakultas, fakultas.kode_fakultas')
                   ->join('fakultas', 'fakultas.id = divisi.fakultas_id')
                   ->findAll();
    }

    public function getDivisiByFakultas(int $fakultasId): array
    {
        return $this->where('fakultas_id', $fakultasId)
                   ->where('is_active', true)
                   ->findAll();
    }

    public function getActiveDivisi(): array
    {
        return $this->where('is_active', true)->findAll();
    }
}