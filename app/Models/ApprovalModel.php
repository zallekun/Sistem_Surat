<?php

namespace App\Models;

use CodeIgniter\Model;

class ApprovalModel extends Model
{
    protected $table = 'approval';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'surat_id', 'level', 'user_id', 'status_approval',
        'tanggal_approval', 'alasan_reject', 'catatan', 'ip_address'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'surat_id' => 'integer',
        'level' => 'integer',
        'user_id' => 'integer',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    protected $validationRules = [
        'surat_id' => 'required|integer|is_not_unique[surat.id]',
        'level' => 'required|integer|greater_than[0]',
        'user_id' => 'required|integer|is_not_unique[users.id]',
        'status_approval' => 'required|in_list[pending,approved,rejected]',
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function getApprovalChain(int $suratId): array
    {
        return $this->select('approval.*, users.nama as approver_name, users.role')
                   ->join('users', 'users.id = approval.user_id')
                   ->where('approval.surat_id', $suratId)
                   ->orderBy('approval.level', 'ASC')
                   ->findAll();
    }

    public function createApprovalChain(int $suratId, string $kategori): bool
    {
        $chain = $this->buildApprovalChain($kategori);
        
        foreach ($chain as $level => $approver) {
            $data = [
                'surat_id' => $suratId,
                'level' => $level,
                'user_id' => $approver['user_id'],
                'status_approval' => self::STATUS_PENDING
            ];
            
            $this->insert($data);
        }
        
        return true;
    }

    private function buildApprovalChain(string $kategori): array
    {
        $userModel = new UserModel();
        
        // Get users by role
        $staffUmum = $userModel->getUsersByRole('staff_umum')[0] ?? null;
        $kabagTU = $userModel->getUsersByRole('kabag_tu')[0] ?? null;
        $dekan = $userModel->getUsersByRole('dekan')[0] ?? null;
        
        $chain = [];
        
        if ($staffUmum) {
            $chain[1] = ['user_id' => $staffUmum['id'], 'role' => 'staff_umum'];
        }
        
        if ($kabagTU) {
            $chain[2] = ['user_id' => $kabagTU['id'], 'role' => 'kabag_tu'];
        }
        
        if ($dekan) {
            $chain[3] = ['user_id' => $dekan['id'], 'role' => 'dekan'];
        }
        
        return $chain;
    }
}