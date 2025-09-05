<?php

namespace App\Models;

use CodeIgniter\Model;

class DisposisiModel extends Model
{
    protected $table = 'disposisi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'surat_id', 'nomor_disposisi', 'tanggal_masuk', 'jumlah_lampiran',
        'dari', 'kepada', 'instruksi', 'sifat', 'batas_waktu', 'created_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'surat_id' => 'integer',
        'jumlah_lampiran' => 'integer',
        'tanggal_masuk' => 'datetime',
        'batas_waktu' => '?datetime',
        'created_by' => 'integer',
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'surat_id' => 'required|integer|is_not_unique[surat.id]',
        'nomor_disposisi' => 'required|min_length[3]|max_length[100]|is_unique[disposisi.nomor_disposisi,id,{id}]',
        'tanggal_masuk' => 'required|valid_date',
        'jumlah_lampiran' => 'required|integer|greater_equal_to[0]',
        'dari' => 'required|min_length[3]|max_length[100]',
        'kepada' => 'required|min_length[3]|max_length[100]',
        'sifat' => 'required|in_list[biasa,segera,sangat_segera,rahasia]',
        'batas_waktu' => 'permit_empty|valid_date',
        'created_by' => 'required|integer|is_not_unique[users.id]',
    ];

    protected $validationMessages = [
        'nomor_disposisi' => [
            'is_unique' => 'Nomor disposisi sudah digunakan'
        ],
        'surat_id' => [
            'is_not_unique' => 'Surat tidak ditemukan'
        ],
        'created_by' => [
            'is_not_unique' => 'User tidak ditemukan'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getDisposisiWithDetails(int $disposisiId): ?array
    {
        return $this->select('disposisi.*, 
                            surat.nomor_surat, surat.perihal, surat.kategori,
                            users.nama as created_by_name')
                   ->join('surat', 'surat.id = disposisi.surat_id')
                   ->join('users', 'users.id = disposisi.created_by')
                   ->where('disposisi.id', $disposisiId)
                   ->first();
    }

    public function getDisposisiBySurat(int $suratId): array
    {
        return $this->select('disposisi.*, users.nama as created_by_name')
                   ->join('users', 'users.id = disposisi.created_by')
                   ->where('disposisi.surat_id', $suratId)
                   ->orderBy('disposisi.created_at', 'DESC')
                   ->findAll();
    }

    public function generateNomorDisposisi(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Get last number for this month
        $lastDisposisi = $this->select('nomor_disposisi')
                             ->like('nomor_disposisi', "DISP/$month/$year")
                             ->orderBy('id', 'DESC')
                             ->first();

        $lastNumber = 1;
        if ($lastDisposisi) {
            // Extract number from format: DISP/001/03/2025
            $parts = explode('/', $lastDisposisi['nomor_disposisi']);
            if (count($parts) >= 2) {
                $lastNumber = intval($parts[1]) + 1;
            }
        }

        $number = str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        return "DISP/$number/$month/$year";
    }

    public function createAutoDisposisi(int $suratId, string $kategori, int $createdBy): ?int
    {
        // Get surat details
        $suratModel = new SuratModel();
        $surat = $suratModel->find($suratId);
        
        if (!$surat) {
            return null;
        }

        // Determine target based on category
        $kepada = $this->getDisposisiTarget($kategori);
        
        $data = [
            'surat_id' => $suratId,
            'nomor_disposisi' => $this->generateNomorDisposisi(),
            'tanggal_masuk' => date('Y-m-d'),
            'jumlah_lampiran' => $this->countLampiran($suratId),
            'dari' => 'Dekan',
            'kepada' => $kepada,
            'instruksi' => $this->getDefaultInstruksi($kategori),
            'sifat' => $this->mapPrioritasToSifat($surat['prioritas']),
            'batas_waktu' => $this->calculateDeadline($surat['prioritas']),
            'created_by' => $createdBy
        ];

        $result = $this->insert($data);
        return $result ? $this->getInsertID() : null;
    }

    private function getDisposisiTarget(string $kategori): string
    {
        $targets = [
            'akademik' => 'Wakil Dekan Bidang Akademik',
            'kemahasiswaan' => 'Wakil Dekan Bidang Kemahasiswaan',
            'kepegawaian' => 'Wakil Dekan Bidang Umum',
            'keuangan' => 'Kepala Urusan Keuangan',
            'umum' => 'Kepala Bagian Tata Usaha'
        ];

        return $targets[$kategori] ?? 'Kepala Bagian Tata Usaha';
    }

    private function getDefaultInstruksi(string $kategori): string
    {
        $instruksi = [
            'akademik' => 'Mohon ditindaklanjuti sesuai dengan ketentuan akademik yang berlaku',
            'kemahasiswaan' => 'Mohon ditindaklanjuti sesuai dengan kebijakan kemahasiswaan',
            'kepegawaian' => 'Mohon ditindaklanjuti sesuai dengan peraturan kepegawaian',
            'keuangan' => 'Mohon ditindaklanjuti sesuai dengan ketentuan keuangan',
            'umum' => 'Mohon ditindaklanjuti sebagaimana mestinya'
        ];

        return $instruksi[$kategori] ?? 'Mohon ditindaklanjuti sebagaimana mestinya';
    }

    private function mapPrioritasToSifat(string $prioritas): string
    {
        $mapping = [
            'normal' => 'biasa',
            'urgent' => 'segera',
            'sangat_urgent' => 'sangat_segera'
        ];

        return $mapping[$prioritas] ?? 'biasa';
    }

    private function calculateDeadline(string $prioritas): ?string
    {
        $days = [
            'normal' => 7,
            'urgent' => 3,
            'sangat_urgent' => 1
        ];

        $addDays = $days[$prioritas] ?? 7;
        return date('Y-m-d', strtotime("+$addDays days"));
    }

    private function countLampiran(int $suratId): int
    {
        $lampiranModel = new LampiranModel();
        return count($lampiranModel->getLampiranBySurat($suratId));
    }
}