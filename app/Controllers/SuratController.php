<?php

namespace App\Controllers;

use App\Models\SuratModel;
use App\Models\ProdiModel;
use App\Models\LampiranModel;
use App\Models\SuratWorkflowModel;
use App\Models\ApprovalModel;
use CodeIgniter\Controller;

class SuratController extends Controller
{
    protected $suratModel;
    protected $prodiModel;
    protected $lampiranModel;
    protected $workflowModel;
    protected $approvalModel;
    protected $session;

    public function __construct()
    {
        $this->suratModel = new SuratModel();
        $this->prodiModel = new ProdiModel();
        $this->lampiranModel = new LampiranModel();
        $this->workflowModel = new SuratWorkflowModel();
        $this->approvalModel = new ApprovalModel();
        $this->session = \Config\Services::session();
        
        // Check authentication
        if (!$this->session->get('is_logged_in')) {
            return redirect()->to('/login');
        }
    }

    public function index()
    {
        $userRole = $this->session->get('user_role');
        $userId = $this->session->get('user_id');
        $prodiId = $this->session->get('user_prodi_id');

        // Get filter parameters
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $kategori = $this->request->getGet('kategori');
        $prioritas = $this->request->getGet('prioritas');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 15; // Items per page

        // Build filters for model method
        $filters = [];
        
        // Apply role-based filtering
        if ($userRole === 'admin_prodi') {
            $filters['created_by'] = $userId;
        } else {
            // For approval roles, show only submitted surat
            $filters['status_in'] = [
                SuratModel::STATUS_SUBMITTED,
                SuratModel::STATUS_UNDER_REVIEW,
                SuratModel::STATUS_APPROVED_L1,
                SuratModel::STATUS_APPROVED_L2,
                SuratModel::STATUS_READY_DISPOSISI,
                SuratModel::STATUS_IN_PROCESS,
                SuratModel::STATUS_COMPLETED,
                SuratModel::STATUS_REJECTED
            ];
        }

        // Apply search filter
        if ($search) {
            $filters['search'] = $search;
        }

        // Apply status filter
        if ($status) {
            $filters['status'] = $status;
        }

        // Apply kategori filter
        if ($kategori) {
            $filters['kategori'] = $kategori;
        }

        // Apply prioritas filter
        if ($prioritas) {
            $filters['prioritas'] = $prioritas;
        }

        // Get paginated results using model method
        $surat = $this->suratModel->getSuratPaginated($filters, $perPage);
        $pager = $this->suratModel->pager;

        // Get stats (without filters for overview)
        $stats = $this->suratModel->getSuratStats($prodiId);

        $data = [
            'title' => 'Daftar Surat - Sistem Surat Menyurat',
            'surat' => $surat,
            'pager' => $pager,
            'stats' => $stats,
            'user_role' => $userRole,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'kategori' => $kategori,
                'prioritas' => $prioritas,
                'page' => $page
            ],
            'total_results' => $pager ? $pager->getTotal('surat') : count($surat)
        ];

        return view('surat/index', $data);
    }

    public function create()
    {
        // Only admin_prodi can create surat
        if ($this->session->get('user_role') !== 'admin_prodi') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses untuk membuat surat');
        }

        $prodiId = $this->session->get('user_prodi_id');
        if (!$prodiId) {
            return redirect()->to('/dashboard')->with('error', 'Program studi tidak ditemukan');
        }

        $data = [
            'title' => 'Buat Surat Baru - Sistem Surat Menyurat',
            'prodi' => $this->prodiModel->find($prodiId),
            'nomor_surat' => $this->suratModel->generateNomorSurat($prodiId),
            'validation' => null
        ];

        return view('surat/create', $data);
    }

    public function store()
    {
        // Only admin_prodi can create surat
        if ($this->session->get('user_role') !== 'admin_prodi') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses');
        }

        $rules = [
            'nomor_surat' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nomor surat harus diisi',
                    'min_length' => 'Nomor surat minimal 3 karakter'
                ]
            ],
            'perihal' => [
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'Perihal harus diisi',
                    'min_length' => 'Perihal minimal 10 karakter'
                ]
            ],
            'tanggal_surat' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal surat harus diisi',
                    'valid_date' => 'Format tanggal tidak valid'
                ]
            ],
            'kategori' => [
                'rules' => 'required|in_list[akademik,kemahasiswaan,kepegawaian,keuangan,umum]',
                'errors' => [
                    'required' => 'Kategori harus dipilih'
                ]
            ],
            'prioritas' => [
                'rules' => 'required|in_list[normal,urgent,sangat_urgent]',
                'errors' => [
                    'required' => 'Prioritas harus dipilih'
                ]
            ],
            'tujuan' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Tujuan harus diisi'
                ]
            ],
            'file_surat' => [
                'rules' => 'uploaded[file_surat]|max_size[file_surat,5120]|ext_in[file_surat,pdf,jpg,jpeg,png]',
                'errors' => [
                    'uploaded' => 'File scan surat harus diupload',
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'ext_in' => 'Format file harus PDF, JPG, JPEG, atau PNG'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->create();
        }

        $prodiId = $this->session->get('user_prodi_id');
        $userId = $this->session->get('user_id');

        $data = [
            'nomor_surat' => $this->request->getPost('nomor_surat'),
            'perihal' => $this->request->getPost('perihal'),
            'tanggal_surat' => $this->request->getPost('tanggal_surat'),
            'kategori' => $this->request->getPost('kategori'),
            'prioritas' => $this->request->getPost('prioritas'),
            'tujuan' => $this->request->getPost('tujuan'),
            'keterangan' => $this->request->getPost('keterangan'),
            'deadline' => $this->request->getPost('deadline'),
            'prodi_id' => $prodiId,
            'created_by' => $userId,
            'current_holder' => $userId,
            'status' => SuratModel::STATUS_DRAFT
        ];

        // Debug logging
        log_message('info', "SuratController::store - Data to insert: " . json_encode($data));
        
        $suratId = $this->suratModel->insert($data);

        // Debug logging
        log_message('info', "SuratController::store - Insert result: " . ($suratId ? $suratId : 'FAILED'));
        if (!$suratId) {
            $errors = $this->suratModel->errors();
            log_message('error', "SuratController::store - Insert errors: " . json_encode($errors));
        }

        if ($suratId) {
            // Handle file upload
            $file = $this->request->getFile('file_surat');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Create upload directory if not exists
                $uploadPath = WRITEPATH . 'uploads/surat/' . date('Y/m');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Generate unique filename
                $fileName = $suratId . '_' . time() . '.' . $file->getExtension();
                $file->move($uploadPath, $fileName);
                
                // Save file info to lampiran table
                $fileData = [
                    'nama_file' => $fileName,
                    'nama_asli' => $file->getClientName(),
                    'path_file' => $uploadPath . '/' . $fileName,
                    'mime_type' => $file->getClientMimeType(),
                    'ukuran_file' => $file->getSize(),
                    'checksum' => hash_file('md5', $uploadPath . '/' . $fileName),
                    'versi' => 1,
                    'is_final' => true
                ];
                
                $this->lampiranModel->createNewVersion($suratId, $fileData, $userId);
            }
            // Log workflow
            $this->workflowModel->logWorkflow(
                $suratId, 
                '', 
                SuratModel::STATUS_DRAFT, 
                $userId, 
                SuratWorkflowModel::ACTION_SUBMIT, 
                'Surat dibuat'
            );

            // Check if auto submit is requested
            $autoSubmit = $this->request->getPost('auto_submit');
            
            if ($autoSubmit) {
                // Auto submit process - same as manual submit
                $userModel = new \App\Models\UserModel();
                $firstApprover = $userModel->getUsersByRole('staff_umum')[0] ?? null;
                
                if ($firstApprover) {
                    // Update status to SUBMITTED
                    $this->suratModel->updateStatus($suratId, SuratModel::STATUS_SUBMITTED, $firstApprover['id']);
                    
                    // Create approval chain
                    $this->approvalModel->createApprovalChain($suratId, $data['kategori']);
                    
                    // Log workflow
                    $this->workflowModel->logWorkflow(
                        $suratId, 
                        SuratModel::STATUS_DRAFT, 
                        SuratModel::STATUS_SUBMITTED, 
                        $userId, 
                        SuratWorkflowModel::ACTION_SUBMIT, 
                        'Surat dibuat dan langsung disubmit untuk review'
                    );
                    
                    // Update workload
                    $userModel->incrementWorkload($firstApprover['id']);
                    
                    $this->session->setFlashdata('success', 'Surat berhasil dibuat dan telah disubmit untuk review oleh Staff Umum');
                } else {
                    $this->session->setFlashdata('success', 'Surat berhasil dibuat sebagai DRAFT (Staff approver tidak ditemukan untuk auto-submit)');
                }
            } else {
                $this->session->setFlashdata('success', 'Surat berhasil dibuat dengan status DRAFT');
            }
            
            return redirect()->to('/surat/' . $suratId);
        } else {
            $this->session->setFlashdata('error', 'Gagal membuat surat. Error: ' . implode(', ', $this->suratModel->errors()));
            return $this->create();
        }
    }

    public function show($id)
    {
        $surat = $this->suratModel->getSuratWithDetails($id);
        
        if (!$surat) {
            return redirect()->to('/surat')->with('error', 'Surat tidak ditemukan');
        }

        // Check access permission
        $userRole = $this->session->get('user_role');
        $userId = $this->session->get('user_id');
        
        if (!$this->canViewSurat($surat, $userRole, $userId)) {
            return redirect()->to('/surat')->with('error', 'Anda tidak memiliki akses untuk melihat surat ini');
        }

        // Get related data
        $lampiran = $this->lampiranModel->getLampiranBySurat($id);
        $fileHistory = $this->lampiranModel->getFileHistory($id); // Get ALL file versions
        $workflow = $this->workflowModel->getWorkflowHistory($id);
        $approval = $this->approvalModel->getApprovalChain($id);

        $data = [
            'title' => 'Detail Surat - ' . $surat['nomor_surat'],
            'surat' => $surat,
            'lampiran' => $lampiran,
            'fileHistory' => $fileHistory,
            'workflow' => $workflow,
            'approval' => $approval,
            'user_role' => $userRole,
            'can_edit' => $this->canEditSurat($surat, $userRole, $userId),
            'can_approve' => $this->canApproveSurat($surat, $userRole),
        ];

        return view('surat/show', $data);
    }

    public function edit($id)
    {
        $surat = $this->suratModel->find($id);
        
        if (!$surat) {
            return redirect()->to('/surat')->with('error', 'Surat tidak ditemukan');
        }

        $userRole = $this->session->get('user_role');
        $userId = $this->session->get('user_id');

        if (!$this->canEditSurat($surat, $userRole, $userId)) {
            return redirect()->to('/surat/' . $id)->with('error', 'Surat tidak dapat diedit pada status ini');
        }

        $data = [
            'title' => 'Edit Surat - ' . $surat['nomor_surat'],
            'surat' => $surat,
            'validation' => null
        ];

        return view('surat/edit', $data);
    }

    public function update($id)
    {
        $surat = $this->suratModel->find($id);
        
        if (!$surat) {
            return redirect()->to('/surat')->with('error', 'Surat tidak ditemukan');
        }

        $userRole = $this->session->get('user_role');
        $userId = $this->session->get('user_id');

        if (!$this->canEditSurat($surat, $userRole, $userId)) {
            return redirect()->to('/surat/' . $id)->with('error', 'Surat tidak dapat diedit');
        }

        $rules = [
            'perihal' => 'required|min_length[10]',
            'tanggal_surat' => 'required|valid_date',
            'kategori' => 'required|in_list[akademik,kemahasiswaan,kepegawaian,keuangan,umum]',
            'prioritas' => 'required|in_list[normal,urgent,sangat_urgent]',
            'tujuan' => 'required|min_length[3]|max_length[100]',
        ];

        // Add file validation if file is uploaded
        $fileRevisi = $this->request->getFile('file_surat_revisi');
        if ($fileRevisi && $fileRevisi->isValid()) {
            $rules['file_surat_revisi'] = [
                'rules' => 'max_size[file_surat_revisi,5120]|ext_in[file_surat_revisi,pdf,jpg,jpeg,png]',
                'errors' => [
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'ext_in' => 'Format file harus PDF, JPG, JPEG, atau PNG'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return $this->edit($id);
        }

        $data = [
            'perihal' => $this->request->getPost('perihal'),
            'tanggal_surat' => $this->request->getPost('tanggal_surat'),
            'kategori' => $this->request->getPost('kategori'),
            'prioritas' => $this->request->getPost('prioritas'),
            'tujuan' => $this->request->getPost('tujuan'),
            'keterangan' => $this->request->getPost('keterangan'),
            'deadline' => $this->request->getPost('deadline'),
        ];

        if ($this->suratModel->update($id, $data)) {
            // Handle file upload for revision
            $fileRevisi = $this->request->getFile('file_surat_revisi');
            if ($fileRevisi && $fileRevisi->isValid() && !$fileRevisi->hasMoved()) {
                // Create upload directory if not exists
                $uploadPath = WRITEPATH . 'uploads/surat/' . date('Y/m');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Get latest version number
                $latestVersion = $this->lampiranModel->getLatestVersion($id);
                $newVersion = $latestVersion + 1;
                
                // Generate filename with version
                $fileName = $id . '_v' . $newVersion . '_' . time() . '.' . $fileRevisi->getExtension();
                $fileRevisi->move($uploadPath, $fileName);
                
                // Save new file version to lampiran table
                $fileData = [
                    'nama_file' => $fileName,
                    'nama_asli' => $fileRevisi->getClientName(),
                    'path_file' => $uploadPath . '/' . $fileName,
                    'mime_type' => $fileRevisi->getClientMimeType(),
                    'ukuran_file' => $fileRevisi->getSize(),
                    'checksum' => hash_file('md5', $uploadPath . '/' . $fileName),
                    'versi' => $newVersion,
                    'is_final' => true
                ];
                
                // Mark old version as not final
                $this->lampiranModel->where('surat_id', $id)->set('is_final', false)->update();
                
                // Create new version
                $this->lampiranModel->createNewVersion($id, $fileData, $userId);
                
                // Log file revision - if status is NEED_REVISION, this is a revision response
                $actionType = ($surat['status'] === 'NEED_REVISION') ? 'REVISE' : 'UPDATE';
                $message = ($surat['status'] === 'NEED_REVISION') ? 
                    "Surat direvisi dengan lampiran baru (versi {$newVersion})" : 
                    "Surat dan lampiran diperbarui (versi {$newVersion})";
                    
                $this->workflowModel->logWorkflow(
                    $id, 
                    $surat['status'], 
                    $surat['status'], 
                    $userId, 
                    $actionType, 
                    $message
                );

                $this->session->setFlashdata('success', "Surat berhasil diperbarui dengan lampiran versi {$newVersion}");
            } else {
                // Log workflow without file update
                $this->workflowModel->logWorkflow(
                    $id, 
                    $surat['status'], 
                    $surat['status'], 
                    $userId, 
                    'UPDATE', 
                    'Surat diperbarui (tanpa perubahan lampiran)'
                );

                $this->session->setFlashdata('success', 'Surat berhasil diperbarui');
            }

            return redirect()->to('/surat/' . $id);
        } else {
            $this->session->setFlashdata('error', 'Gagal memperbarui surat');
            return $this->edit($id);
        }
    }

    public function submit($id)
    {
        $surat = $this->suratModel->find($id);
        
        if (!$surat || !in_array($surat['status'], [SuratModel::STATUS_DRAFT, SuratModel::STATUS_NEED_REVISION])) {
            return redirect()->to('/surat')->with('error', 'Surat tidak dapat disubmit');
        }

        $userId = $this->session->get('user_id');
        
        if ($surat['created_by'] != $userId) {
            return redirect()->to('/surat')->with('error', 'Anda tidak memiliki akses');
        }

        // Get first approver (staff_umum)
        $userModel = new \App\Models\UserModel();
        $firstApprover = $userModel->getUsersByRole('staff_umum')[0] ?? null;
        
        if (!$firstApprover) {
            return redirect()->to('/surat/' . $id)->with('error', 'Staff approver tidak ditemukan');
        }

        // Update status
        if ($this->suratModel->updateStatus($id, SuratModel::STATUS_SUBMITTED, $firstApprover['id'])) {
            // Create approval chain if not exists
            $existingApproval = $this->approvalModel->where('surat_id', $id)->first();
            if (!$existingApproval) {
                $this->approvalModel->createApprovalChain($id, $surat['kategori']);
            }
            
            // Log workflow
            $this->workflowModel->logWorkflow(
                $id, 
                $surat['status'], 
                SuratModel::STATUS_SUBMITTED, 
                $userId, 
                SuratWorkflowModel::ACTION_SUBMIT, 
                'Surat disubmit untuk review'
            );

            // Update workload
            $userModel->incrementWorkload($firstApprover['id']);

            $this->session->setFlashdata('success', 'Surat berhasil disubmit untuk review');
        } else {
            $this->session->setFlashdata('error', 'Gagal submit surat');
        }

        return redirect()->to('/surat/' . $id);
    }

    public function bulkSubmit()
    {
        // Only admin_prodi can bulk submit
        if ($this->session->get('user_role') !== 'admin_prodi') {
            return redirect()->to('/surat')->with('error', 'Anda tidak memiliki akses');
        }

        $suratIds = $this->request->getPost('surat_ids');
        
        if (empty($suratIds)) {
            return redirect()->to('/surat')->with('error', 'Tidak ada surat yang dipilih');
        }

        $userId = $this->session->get('user_id');
        $userModel = new \App\Models\UserModel();
        $firstApprover = $userModel->getUsersByRole('staff_umum')[0] ?? null;
        
        if (!$firstApprover) {
            return redirect()->to('/surat')->with('error', 'Staff approver tidak ditemukan');
        }

        $successCount = 0;
        $errors = [];

        foreach ($suratIds as $suratId) {
            $surat = $this->suratModel->find($suratId);
            
            // Verify ownership and status
            if (!$surat || $surat['created_by'] != $userId || 
                !in_array($surat['status'], [SuratModel::STATUS_DRAFT, SuratModel::STATUS_NEED_REVISION])) {
                $errors[] = "Surat ID {$suratId} tidak dapat disubmit";
                continue;
            }

            // Update status to SUBMITTED
            if ($this->suratModel->updateStatus($suratId, SuratModel::STATUS_SUBMITTED, $firstApprover['id'])) {
                // Create approval chain if not exists
                $existingApproval = $this->approvalModel->where('surat_id', $suratId)->first();
                if (!$existingApproval) {
                    $this->approvalModel->createApprovalChain($suratId, $surat['kategori']);
                }
                
                // Log workflow
                $this->workflowModel->logWorkflow(
                    $suratId, 
                    $surat['status'], 
                    SuratModel::STATUS_SUBMITTED, 
                    $userId, 
                    SuratWorkflowModel::ACTION_SUBMIT, 
                    'Surat disubmit melalui bulk action'
                );

                $successCount++;
            } else {
                $errors[] = "Gagal submit surat {$surat['nomor_surat']}";
            }
        }

        // Update workload for staff
        if ($successCount > 0) {
            $userModel->incrementWorkload($firstApprover['id'], $successCount);
        }

        $message = "Berhasil submit {$successCount} surat untuk review";
        if (!empty($errors)) {
            $message .= ". Error: " . implode(', ', $errors);
        }

        return redirect()->to('/surat')->with('success', $message);
    }

    private function canViewSurat($surat, $userRole, $userId): bool
    {
        // Creator can always view
        if ($surat['created_by'] == $userId) {
            return true;
        }

        // Super admin and dekan can view all
        if (in_array($userRole, ['super_admin', 'dekan'])) {
            return true;
        }

        // Staff and management can view submitted surat
        if (in_array($userRole, ['staff_umum', 'kabag_tu', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum', 'kaur_keuangan'])) {
            return in_array($surat['status'], [
                SuratModel::STATUS_SUBMITTED,
                SuratModel::STATUS_UNDER_REVIEW,
                SuratModel::STATUS_APPROVED_L1,
                SuratModel::STATUS_APPROVED_L2,
                SuratModel::STATUS_READY_DISPOSISI,
                SuratModel::STATUS_IN_PROCESS,
                SuratModel::STATUS_COMPLETED,
                SuratModel::STATUS_REJECTED
            ]);
        }

        return false;
    }

    private function canEditSurat($surat, $userRole, $userId): bool
    {
        // Only creator can edit
        if ($surat['created_by'] != $userId) {
            return false;
        }

        // Only draft and need_revision can be edited
        return in_array($surat['status'], [
            SuratModel::STATUS_DRAFT,
            SuratModel::STATUS_NEED_REVISION
        ]);
    }

    private function canApproveSurat($surat, $userRole): bool
    {
        $statusRoleMapping = [
            SuratModel::STATUS_SUBMITTED => 'staff_umum',
            SuratModel::STATUS_APPROVED_L1 => 'kabag_tu',
            SuratModel::STATUS_APPROVED_L2 => 'dekan',
        ];

        return isset($statusRoleMapping[$surat['status']]) && 
               $statusRoleMapping[$surat['status']] === $userRole;
    }
}