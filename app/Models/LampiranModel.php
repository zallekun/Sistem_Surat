<?php

namespace App\Models;

use CodeIgniter\Model;

class LampiranModel extends Model
{
    protected $table = 'lampiran';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'surat_id', 'nama_file', 'nama_asli', 'path_file',
        'ukuran_file', 'mime_type', 'versi', 'keterangan',
        'uploaded_by', 'is_final', 'checksum'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'surat_id' => 'integer',
        'ukuran_file' => 'integer',
        'versi' => 'integer',
        'uploaded_by' => 'integer',
        'is_final' => 'boolean',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    protected $validationRules = [
        'surat_id' => 'required|integer|is_not_unique[surat.id]',
        'nama_file' => 'required|max_length[255]',
        'nama_asli' => 'required|max_length[255]',
        'path_file' => 'required|max_length[500]',
        'ukuran_file' => 'required|integer',
        'mime_type' => 'required|max_length[100]',
        'versi' => 'required|integer',
        'uploaded_by' => 'required|integer|is_not_unique[users.id]',
    ];

    protected $validationMessages = [
        'surat_id' => [
            'is_not_unique' => 'Surat tidak ditemukan'
        ],
        'uploaded_by' => [
            'is_not_unique' => 'User tidak ditemukan'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getLampiranBySurat(int $suratId): array
    {
        return $this->select('lampiran.*, users.nama as uploaded_by_name')
                   ->join('users', 'users.id = lampiran.uploaded_by')
                   ->where('lampiran.surat_id', $suratId)
                   ->orderBy('lampiran.versi', 'DESC')
                   ->findAll();
    }

    public function getLatestVersion(int $suratId): int
    {
        $latest = $this->select('MAX(versi) as latest_version')
                      ->where('surat_id', $suratId)
                      ->first();
        
        return $latest ? intval($latest['latest_version']) : 0;
    }

    public function getLampiranByVersion(int $suratId, int $versi): ?array
    {
        return $this->where('surat_id', $suratId)
                   ->where('versi', $versi)
                   ->first();
    }

    public function createNewVersion(int $suratId, array $fileData, int $uploadedBy): bool
    {
        $latestVersion = $this->getLatestVersion($suratId);
        $newVersion = $latestVersion + 1;

        $data = array_merge($fileData, [
            'surat_id' => $suratId,
            'versi' => $newVersion,
            'uploaded_by' => $uploadedBy,
            'is_final' => false
        ]);

        return $this->insert($data) !== false;
    }

    public function deactivateOldVersions(int $suratId, int $keepVersion): bool
    {
        return $this->where('surat_id', $suratId)
                   ->where('versi <', $keepVersion)
                   ->set('is_final', false)
                   ->update();
    }

    public function getFileHistory(int $suratId): array
    {
        return $this->select('lampiran.*, users.nama as uploaded_by_name')
                   ->join('users', 'users.id = lampiran.uploaded_by')
                   ->where('lampiran.surat_id', $suratId)
                   ->orderBy('lampiran.versi', 'DESC')
                   ->findAll();
    }

    public function getTotalFileSize(int $suratId): int
    {
        $result = $this->select('SUM(ukuran_file) as total_size')
                      ->where('surat_id', $suratId)
                      ->where('is_final', true)
                      ->first();
        
        return $result ? intval($result['total_size']) : 0;
    }

    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}