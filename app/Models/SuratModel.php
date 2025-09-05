<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratModel extends Model
{
    protected $table = 'surat';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'nomor_surat', 'perihal', 'tanggal_surat', 'kategori', 'prioritas',
        'tujuan', 'prodi_id', 'created_by', 'current_holder', 'status',
        'keterangan', 'deadline', 'completed_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'prodi_id' => 'integer',
        'created_by' => 'integer',
        'current_holder' => '?integer',
        'completed_at' => '?datetime',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nomor_surat' => 'required|min_length[3]|max_length[100]|is_unique[surat.nomor_surat,id,{id}]',
        'perihal' => 'required|min_length[10]',
        'tanggal_surat' => 'required|valid_date',
        'kategori' => 'required|in_list[akademik,kemahasiswaan,kepegawaian,keuangan,umum]',
        'prioritas' => 'required|in_list[normal,urgent,sangat_urgent]',
        'tujuan' => 'required|min_length[3]|max_length[100]',
        'prodi_id' => 'required|integer|is_not_unique[prodi.id]',
        'created_by' => 'required|integer|is_not_unique[users.id]',
        'deadline' => 'permit_empty|valid_date',
    ];

    protected $validationMessages = [
        'nomor_surat' => [
            'is_unique' => 'Nomor surat sudah digunakan'
        ],
        'prodi_id' => [
            'is_not_unique' => 'Program studi tidak ditemukan'
        ],
        'created_by' => [
            'is_not_unique' => 'User tidak ditemukan'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Status constants
    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SUBMITTED = 'SUBMITTED';
    public const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';
    public const STATUS_NEED_REVISION = 'NEED_REVISION';
    public const STATUS_APPROVED_L1 = 'APPROVED_L1';
    public const STATUS_APPROVED_L2 = 'APPROVED_L2';
    public const STATUS_READY_DISPOSISI = 'READY_DISPOSISI';
    public const STATUS_IN_PROCESS = 'IN_PROCESS';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public function getSuratWithDetails(int $suratId): ?array
    {
        return $this->select('surat.*, 
                            prodi.nama_prodi, prodi.kode_prodi,
                            fakultas.nama_fakultas,
                            creator.nama as creator_name,
                            holder.nama as holder_name')
                   ->join('prodi', 'prodi.id = surat.prodi_id')
                   ->join('fakultas', 'fakultas.id = prodi.fakultas_id')
                   ->join('users creator', 'creator.id = surat.created_by')
                   ->join('users holder', 'holder.id = surat.current_holder', 'left')
                   ->where('surat.id', $suratId)
                   ->first();
    }

    public function getSuratByUser(int $userId, ?string $status = null): array
    {
        $builder = $this->select('surat.*, prodi.nama_prodi, fakultas.nama_fakultas')
                       ->join('prodi', 'prodi.id = surat.prodi_id')
                       ->join('fakultas', 'fakultas.id = prodi.fakultas_id')
                       ->where('surat.created_by', $userId);

        if ($status) {
            $builder->where('surat.status', $status);
        }

        return $builder->orderBy('surat.created_at', 'DESC')->findAll();
    }

    public function getSuratForApproval(string $userRole, ?int $divisiId = null): array
    {
        $builder = $this->select('surat.*, prodi.nama_prodi, fakultas.nama_fakultas, creator.nama as creator_name')
                       ->join('prodi', 'prodi.id = surat.prodi_id')
                       ->join('fakultas', 'fakultas.id = prodi.fakultas_id')
                       ->join('users creator', 'creator.id = surat.created_by');

        switch ($userRole) {
            case 'staff_umum':
                $builder->where('surat.status', self::STATUS_SUBMITTED);
                break;
            case 'kabag_tu':
                $builder->where('surat.status', self::STATUS_APPROVED_L1);
                break;
            case 'dekan':
                $builder->where('surat.status', self::STATUS_APPROVED_L2);
                break;
            case 'wd_akademik':
                $builder->where('surat.status', self::STATUS_READY_DISPOSISI)
                       ->where('surat.kategori', 'akademik');
                break;
            case 'wd_kemahasiswa':
                $builder->where('surat.status', self::STATUS_READY_DISPOSISI)
                       ->where('surat.kategori', 'kemahasiswaan');
                break;
            case 'wd_umum':
                $builder->where('surat.status', self::STATUS_READY_DISPOSISI)
                       ->whereIn('surat.kategori', ['kepegawaian', 'umum']);
                break;
            case 'kaur_keuangan':
                $builder->where('surat.status', self::STATUS_READY_DISPOSISI)
                       ->where('surat.kategori', 'keuangan');
                break;
        }

        return $builder->orderBy('surat.created_at', 'ASC')->findAll();
    }

    public function updateStatus(int $suratId, string $newStatus, ?int $currentHolderId = null): bool
    {
        $updateData = ['status' => $newStatus];
        
        if ($currentHolderId) {
            $updateData['current_holder'] = $currentHolderId;
        }

        if ($newStatus === self::STATUS_COMPLETED) {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($suratId, $updateData);
    }

    public function getSuratStats(?int $prodiId = null): array
    {
        $builder = $this->select('status, COUNT(*) as count')
                       ->groupBy('status');

        if ($prodiId) {
            $builder->where('prodi_id', $prodiId);
        }

        $results = $builder->findAll();
        
        // Initialize all status with 0
        $stats = [
            self::STATUS_DRAFT => 0,
            self::STATUS_SUBMITTED => 0,
            self::STATUS_UNDER_REVIEW => 0,
            self::STATUS_NEED_REVISION => 0,
            self::STATUS_APPROVED_L1 => 0,
            self::STATUS_APPROVED_L2 => 0,
            self::STATUS_READY_DISPOSISI => 0,
            self::STATUS_IN_PROCESS => 0,
            self::STATUS_COMPLETED => 0,
            self::STATUS_REJECTED => 0,
            self::STATUS_CANCELLED => 0
        ];
        
        // Override with actual counts
        foreach ($results as $result) {
            $stats[$result['status']] = (int)$result['count'];
        }

        return $stats;
    }

    public function generateNomorSurat(int $prodiId): string
    {
        $prodi = model('ProdiModel')->find($prodiId);
        $year = date('Y');
        $month = date('m');
        
        // Get last number for this month
        $lastSurat = $this->select('nomor_surat')
                         ->where('prodi_id', $prodiId)
                         ->like('nomor_surat', "/$month/$year")
                         ->orderBy('id', 'DESC')
                         ->first();

        $lastNumber = 1;
        if ($lastSurat) {
            // Extract number from format: 001/TI/03/2025
            $parts = explode('/', $lastSurat['nomor_surat']);
            if (count($parts) >= 1) {
                $lastNumber = intval($parts[0]) + 1;
            }
        }

        $number = str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        return "$number/{$prodi['kode_prodi']}/$month/$year";
    }

    public function getOverdueLetters()
    {
        return $this->select('surat.*, prodi.nama_prodi, users.nama as dibuat_oleh_nama')
                    ->join('prodi', 'prodi.id = surat.prodi_id')
                    ->join('users', 'users.id = surat.created_by')
                    ->where('surat.batas_waktu <', date('Y-m-d H:i:s'))
                    ->whereNotIn('surat.status', [self::STATUS_COMPLETED, self::STATUS_REJECTED])
                    ->findAll();
    }

    public function getUpcomingDeadlines($days = 3)
    {
        $futureDate = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        
        return $this->select('surat.*, prodi.nama_prodi, users.nama as dibuat_oleh_nama')
                    ->join('prodi', 'prodi.id = surat.prodi_id')
                    ->join('users', 'users.id = surat.created_by')
                    ->where('surat.batas_waktu <=', $futureDate)
                    ->where('surat.batas_waktu >', date('Y-m-d H:i:s'))
                    ->whereNotIn('surat.status', [self::STATUS_COMPLETED, self::STATUS_REJECTED])
                    ->findAll();
    }

    /**
     * Get paginated surat with filters
     */
    public function getSuratPaginated($filters = [], $perPage = 15)
    {
        $builder = $this->select('surat.*, prodi.nama_prodi, users.nama as creator_name')
                        ->join('prodi', 'prodi.id = surat.prodi_id', 'left')
                        ->join('users', 'users.id = surat.created_by', 'left');

        // Apply filters
        if (isset($filters['created_by'])) {
            $builder->where('surat.created_by', $filters['created_by']);
        }

        if (isset($filters['status_in']) && is_array($filters['status_in'])) {
            $builder->whereIn('surat.status', $filters['status_in']);
        }

        if (isset($filters['status'])) {
            $builder->where('surat.status', $filters['status']);
        }

        if (isset($filters['kategori'])) {
            $builder->where('surat.kategori', $filters['kategori']);
        }

        if (isset($filters['prioritas'])) {
            $builder->where('surat.prioritas', $filters['prioritas']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                    ->like('surat.nomor_surat', $search)
                    ->orLike('surat.perihal', $search)
                    ->orLike('surat.tujuan', $search)
                    ->groupEnd();
        }

        // Order by created_at desc for latest first
        $builder->orderBy('surat.created_at', 'DESC');

        // Return paginated results
        return $builder->paginate($perPage, 'surat');
    }
}