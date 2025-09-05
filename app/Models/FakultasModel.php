<?php

namespace App\Models;

use CodeIgniter\Model;

class FakultasModel extends Model
{
    protected $table = 'fakultas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama_fakultas', 'kode_fakultas', 'alamat', 'telepon', 
        'email', 'website', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_active' => 'boolean',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'nama_fakultas' => 'required|min_length[3]|max_length[100]',
        'kode_fakultas' => 'required|min_length[2]|max_length[10]|is_unique[fakultas.kode_fakultas,id,{id}]',
        'email' => 'permit_empty|valid_email|max_length[100]',
        'telepon' => 'permit_empty|max_length[20]',
        'website' => 'permit_empty|max_length[100]',
    ];

    protected $validationMessages = [
        'kode_fakultas' => [
            'is_unique' => 'Kode fakultas sudah digunakan'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getFakultasWithStats(): array
    {
        return $this->select('fakultas.*, 
                            COUNT(DISTINCT prodi.id) as total_prodi,
                            COUNT(DISTINCT divisi.id) as total_divisi,
                            COUNT(DISTINCT users.id) as total_users')
                   ->join('prodi', 'prodi.fakultas_id = fakultas.id', 'left')
                   ->join('divisi', 'divisi.fakultas_id = fakultas.id', 'left')
                   ->join('users', 'users.prodi_id = prodi.id OR users.divisi_id = divisi.id', 'left')
                   ->groupBy('fakultas.id')
                   ->findAll();
    }

    public function getActiveFakultas(): array
    {
        return $this->where('is_active', true)->findAll();
    }
}