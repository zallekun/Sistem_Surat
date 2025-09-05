<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratWorkflowModel extends Model
{
    protected $table = 'surat_workflow';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'surat_id', 'from_status', 'to_status', 'action_by',
        'action_type', 'keterangan', 'ip_address', 'user_agent'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'surat_id' => 'integer',
        'action_by' => 'integer',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    protected $validationRules = [
        'surat_id' => 'required|integer|is_not_unique[surat.id]',
        'from_status' => 'required|max_length[50]',
        'to_status' => 'required|max_length[50]',
        'action_by' => 'required|integer|is_not_unique[users.id]',
        'action_type' => 'required|in_list[SUBMIT,APPROVE,REJECT,REVISE,DISPOSE,COMPLETE,CANCEL]',
    ];

    protected $validationMessages = [
        'surat_id' => [
            'is_not_unique' => 'Surat tidak ditemukan'
        ],
        'action_by' => [
            'is_not_unique' => 'User tidak ditemukan'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Action type constants
    public const ACTION_SUBMIT = 'SUBMIT';
    public const ACTION_APPROVE = 'APPROVE';
    public const ACTION_REJECT = 'REJECT';
    public const ACTION_REVISE = 'REVISE';
    public const ACTION_DISPOSE = 'DISPOSE';
    public const ACTION_COMPLETE = 'COMPLETE';
    public const ACTION_CANCEL = 'CANCEL';

    public function logWorkflow(int $suratId, string $fromStatus, string $toStatus, 
                               int $actionBy, string $actionType, ?string $keterangan = null): bool
    {
        $data = [
            'surat_id' => $suratId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'action_by' => $actionBy,
            'action_type' => $actionType,
            'keterangan' => $keterangan
        ];

        return $this->insert($data) !== false;
    }

    public function getWorkflowHistory(int $suratId): array
    {
        return $this->select('surat_workflow.*, users.nama as action_by_name, users.role')
                   ->join('users', 'users.id = surat_workflow.action_by')
                   ->where('surat_workflow.surat_id', $suratId)
                   ->orderBy('surat_workflow.created_at', 'ASC')
                   ->findAll();
    }

    public function getLatestWorkflow(int $suratId): ?array
    {
        return $this->select('surat_workflow.*, users.nama as action_by_name, users.role')
                   ->join('users', 'users.id = surat_workflow.action_by')
                   ->where('surat_workflow.surat_id', $suratId)
                   ->orderBy('surat_workflow.created_at', 'DESC')
                   ->first();
    }

    public function getWorkflowStats(?int $userId = null): array
    {
        $builder = $this->select('action_type, COUNT(*) as count, 
                                DATE(created_at) as date')
                       ->groupBy(['action_type', 'DATE(created_at)']);

        if ($userId) {
            $builder->where('action_by', $userId);
        }

        return $builder->orderBy('date', 'DESC')
                      ->limit(30) // Last 30 days
                      ->findAll();
    }

    public function getUserActivitySummary(int $userId, int $days = 7): array
    {
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        return $this->select('action_type, COUNT(*) as count')
                   ->where('action_by', $userId)
                   ->where('DATE(created_at) >=', $startDate)
                   ->groupBy('action_type')
                   ->findAll();
    }

    public function getProcessingTime(int $suratId): array
    {
        $workflow = $this->getWorkflowHistory($suratId);
        
        if (count($workflow) < 2) {
            return ['total_hours' => 0, 'stages' => []];
        }

        $stages = [];
        $totalSeconds = 0;
        
        for ($i = 1; $i < count($workflow); $i++) {
            $start = strtotime($workflow[$i-1]['created_at']);
            $end = strtotime($workflow[$i]['created_at']);
            $duration = $end - $start;
            
            $stages[] = [
                'from_status' => $workflow[$i-1]['to_status'],
                'to_status' => $workflow[$i]['to_status'],
                'duration_seconds' => $duration,
                'duration_hours' => round($duration / 3600, 2)
            ];
            
            $totalSeconds += $duration;
        }

        return [
            'total_hours' => round($totalSeconds / 3600, 2),
            'total_days' => round($totalSeconds / (3600 * 24), 1),
            'stages' => $stages
        ];
    }

    public function getAverageProcessingTime(string $status, int $days = 30): float
    {
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        $workflows = $this->select('surat_id, created_at')
                         ->where('to_status', $status)
                         ->where('DATE(created_at) >=', $startDate)
                         ->findAll();

        if (empty($workflows)) {
            return 0;
        }

        $totalTime = 0;
        $count = 0;

        foreach ($workflows as $workflow) {
            $firstWorkflow = $this->where('surat_id', $workflow['surat_id'])
                                 ->orderBy('created_at', 'ASC')
                                 ->first();
            
            if ($firstWorkflow) {
                $start = strtotime($firstWorkflow['created_at']);
                $end = strtotime($workflow['created_at']);
                $totalTime += ($end - $start);
                $count++;
            }
        }

        return $count > 0 ? round(($totalTime / $count) / 3600, 2) : 0;
    }
}