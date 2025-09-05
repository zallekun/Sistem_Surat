<?php

namespace App\Controllers;

use App\Models\SuratModel;
use App\Models\UserModel;
use App\Models\SuratWorkflowModel;
use App\Models\ApprovalModel;
use App\Models\DisposisiModel;
use App\Models\UserDelegationsModel;
use App\Services\NotificationService;
use CodeIgniter\Controller;

class WorkflowController extends Controller
{
    protected $suratModel;
    protected $userModel;
    protected $workflowModel;
    protected $approvalModel;
    protected $disposisiModel;
    protected $delegationModel;
    protected $notificationService;
    protected $session;

    public function __construct()
    {
        $this->suratModel = new SuratModel();
        $this->userModel = new UserModel();
        $this->workflowModel = new SuratWorkflowModel();
        $this->approvalModel = new ApprovalModel();
        $this->disposisiModel = new DisposisiModel();
        $this->delegationModel = new UserDelegationsModel();
        $this->notificationService = new NotificationService();
        $this->session = \Config\Services::session();
        
        // Check authentication
        if (!$this->session->get('is_logged_in')) {
            return redirect()->to('/login');
        }
    }

    public function approve($suratId)
    {
        $surat = $this->suratModel->find($suratId);
        if (!$surat) {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat tidak ditemukan']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        // Check if user can approve this surat
        if (!$this->canApprove($surat, $userRole)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses untuk menyetujui surat ini']);
        }

        $keterangan = $this->request->getPost('keterangan') ?? '';
        $result = $this->processApproval($surat, $userId, $userRole, 'APPROVE', $keterangan);

        return $this->response->setJSON($result);
    }

    public function reject($suratId)
    {
        $surat = $this->suratModel->find($suratId);
        if (!$surat) {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat tidak ditemukan']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$this->canApprove($surat, $userRole)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses untuk menolak surat ini']);
        }

        $keterangan = $this->request->getPost('keterangan') ?? 'Surat ditolak';
        $result = $this->processApproval($surat, $userId, $userRole, 'REJECT', $keterangan);

        return $this->response->setJSON($result);
    }

    public function revise($suratId)
    {
        $surat = $this->suratModel->find($suratId);
        if (!$surat) {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat tidak ditemukan']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$this->canApprove($surat, $userRole)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses']);
        }

        $keterangan = $this->request->getPost('keterangan') ?? 'Perlu revisi';
        $result = $this->processApproval($surat, $userId, $userRole, 'REVISE', $keterangan);

        return $this->response->setJSON($result);
    }

    public function dispose($suratId)
    {
        $surat = $this->suratModel->find($suratId);
        if (!$surat || $surat['status'] !== SuratModel::STATUS_APPROVED_L2) {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat tidak dapat didisposisi']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if ($userRole !== 'dekan') {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya Dekan yang dapat mendisposisi surat']);
        }

        // Create disposisi
        $disposisiId = $this->disposisiModel->createAutoDisposisi($suratId, $surat['kategori'], $userId);
        
        if ($disposisiId) {
            // Update surat status
            $targetUser = $this->getDisposisiTargetUser($surat['kategori']);
            $this->suratModel->updateStatus($suratId, SuratModel::STATUS_IN_PROCESS, $targetUser);
            
            // Log workflow
            $this->workflowModel->logWorkflow(
                $suratId,
                SuratModel::STATUS_APPROVED_L2,
                SuratModel::STATUS_IN_PROCESS,
                $userId,
                SuratWorkflowModel::ACTION_DISPOSE,
                'Surat didisposisi ke ' . $this->getDisposisiTargetRole($surat['kategori'])
            );

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Surat berhasil didisposisi',
                'disposisi_id' => $disposisiId
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mendisposisi surat']);
    }

    public function complete($suratId)
    {
        $surat = $this->suratModel->find($suratId);
        if (!$surat || $surat['status'] !== SuratModel::STATUS_IN_PROCESS) {
            return $this->response->setJSON(['success' => false, 'message' => 'Surat tidak dapat diselesaikan']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$this->canComplete($surat, $userRole)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses untuk menyelesaikan surat ini']);
        }

        $keterangan = $this->request->getPost('keterangan') ?? 'Surat telah diselesaikan';
        
        // Update status
        $this->suratModel->updateStatus($suratId, SuratModel::STATUS_COMPLETED);
        
        // Log workflow
        $this->workflowModel->logWorkflow(
            $suratId,
            SuratModel::STATUS_IN_PROCESS,
            SuratModel::STATUS_COMPLETED,
            $userId,
            SuratWorkflowModel::ACTION_COMPLETE,
            $keterangan
        );

        // Decrement workload
        $this->userModel->decrementWorkload($userId);

        return $this->response->setJSON([
            'success' => true, 
            'message' => 'Surat berhasil diselesaikan'
        ]);
    }

    private function processApproval($surat, $userId, $userRole, $action, $keterangan)
    {
        $currentStatus = $surat['status'];
        $newStatus = $this->getNextStatus($currentStatus, $action, $userRole);
        
        if (!$newStatus) {
            return ['success' => false, 'message' => 'Status transition tidak valid'];
        }

        // Get next approver if approved
        $nextApprover = null;
        if ($action === 'APPROVE') {
            $nextApprover = $this->getNextApprover($newStatus);
        } elseif ($action === 'REJECT' || $action === 'REVISE') {
            // Return to creator
            $nextApprover = $surat['created_by'];
        }

        // Update surat status
        $this->suratModel->updateStatus($surat['id'], $newStatus, $nextApprover);

        // Update approval record
        $this->updateApprovalRecord($surat['id'], $userId, $action, $keterangan);

        // Log workflow
        $this->workflowModel->logWorkflow(
            $surat['id'],
            $currentStatus,
            $newStatus,
            $userId,
            $action,
            $keterangan
        );

        // Send notifications
        $this->notificationService->notifyWorkflowAction(
            $surat['id'],
            $action,
            $currentStatus,
            $newStatus,
            $userId,
            $keterangan
        );

        // Update workload
        if ($nextApprover && $nextApprover != $userId) {
            $this->userModel->incrementWorkload($nextApprover);
        }
        $this->userModel->decrementWorkload($userId);

        return [
            'success' => true, 
            'message' => $this->getActionMessage($action),
            'new_status' => $newStatus
        ];
    }

    private function getNextStatus($currentStatus, $action, $userRole)
    {
        if ($action === 'REJECT') {
            return SuratModel::STATUS_REJECTED;
        }
        
        if ($action === 'REVISE') {
            return SuratModel::STATUS_NEED_REVISION;
        }

        if ($action === 'APPROVE') {
            $transitions = [
                SuratModel::STATUS_SUBMITTED => SuratModel::STATUS_APPROVED_L1,
                SuratModel::STATUS_APPROVED_L1 => SuratModel::STATUS_APPROVED_L2,
                SuratModel::STATUS_APPROVED_L2 => SuratModel::STATUS_READY_DISPOSISI
            ];

            return $transitions[$currentStatus] ?? null;
        }

        return null;
    }

    private function getNextApprover($status)
    {
        $approvers = [
            SuratModel::STATUS_APPROVED_L1 => 'kabag_tu',
            SuratModel::STATUS_APPROVED_L2 => 'dekan',
            SuratModel::STATUS_READY_DISPOSISI => 'dekan'
        ];

        $role = $approvers[$status] ?? null;
        if (!$role) return null;

        $users = $this->userModel->getUsersByRole($role);
        return !empty($users) ? $users[0]['id'] : null;
    }

    private function getDisposisiTargetUser($kategori)
    {
        $targetRoles = [
            'akademik' => 'wd_akademik',
            'kemahasiswaan' => 'wd_kemahasiswa',
            'kepegawaian' => 'wd_umum',
            'keuangan' => 'kaur_keuangan',
            'umum' => 'kabag_tu'
        ];

        $role = $targetRoles[$kategori] ?? 'kabag_tu';
        $users = $this->userModel->getUsersByRole($role);
        return !empty($users) ? $users[0]['id'] : null;
    }

    private function getDisposisiTargetRole($kategori)
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

    private function updateApprovalRecord($suratId, $approverId, $action, $keterangan)
    {
        $status = ($action === 'APPROVE') ? ApprovalModel::STATUS_APPROVED : ApprovalModel::STATUS_REJECTED;
        
        // Find the approval record for this user
        $approval = $this->approvalModel->where('surat_id', $suratId)
                                      ->where('user_id', $approverId)
                                      ->where('status_approval', ApprovalModel::STATUS_PENDING)
                                      ->first();

        if ($approval) {
            $updateData = [
                'status_approval' => $status,
                'tanggal_approval' => date('Y-m-d H:i:s'),
                'ip_address' => $this->request->getIPAddress()
            ];
            
            if ($action === 'APPROVE') {
                $updateData['catatan'] = $keterangan;
            } else {
                $updateData['alasan_reject'] = $keterangan;
            }
            
            $this->approvalModel->update($approval['id'], $updateData);
        }
    }

    private function canApprove($surat, $userRole)
    {
        // Define which roles can act on which statuses
        $statusRoleMapping = [
            SuratModel::STATUS_SUBMITTED => ['staff_umum'],
            SuratModel::STATUS_UNDER_REVIEW => ['staff_umum', 'kabag_tu'], 
            SuratModel::STATUS_APPROVED_L1 => ['kabag_tu', 'dekan'],
            SuratModel::STATUS_APPROVED_L2 => ['dekan'],
        ];

        return isset($statusRoleMapping[$surat['status']]) && 
               in_array($userRole, $statusRoleMapping[$surat['status']]);
    }

    private function canComplete($surat, $userRole)
    {
        $categoryRoleMapping = [
            'akademik' => 'wd_akademik',
            'kemahasiswaan' => 'wd_kemahasiswa',
            'kepegawaian' => 'wd_umum',
            'keuangan' => 'kaur_keuangan',
            'umum' => 'kabag_tu'
        ];

        return isset($categoryRoleMapping[$surat['kategori']]) && 
               $categoryRoleMapping[$surat['kategori']] === $userRole;
    }

    private function getActionMessage($action)
    {
        $messages = [
            'APPROVE' => 'Surat berhasil disetujui',
            'REJECT' => 'Surat berhasil ditolak',
            'REVISE' => 'Surat dikembalikan untuk revisi',
            'DISPOSE' => 'Surat berhasil didisposisi',
            'COMPLETE' => 'Surat berhasil diselesaikan'
        ];

        return $messages[$action] ?? 'Aksi berhasil dilakukan';
    }

    public function history($suratId)
    {
        $workflow = $this->workflowModel->getWorkflowHistory($suratId);
        $processing = $this->workflowModel->getProcessingTime($suratId);
        
        $data = [
            'workflow' => $workflow,
            'processing_time' => $processing
        ];

        return $this->response->setJSON($data);
    }

    public function timeline($suratId)
    {
        // Get surat details with related info
        $surat = $this->suratModel->select('surat.*, prodi.nama_prodi as prodi_name, users.nama as creator_name')
                                  ->join('prodi', 'prodi.id = surat.prodi_id', 'left')
                                  ->join('users', 'users.id = surat.created_by', 'left')
                                  ->find($suratId);
        
        if (!$surat) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Surat tidak ditemukan');
        }

        // Get comprehensive workflow history
        $workflow = $this->workflowModel->getWorkflowHistory($suratId);
        
        // Get processing time analytics
        $processing_time = $this->workflowModel->getProcessingTime($suratId);
        
        // Get approval chain with detailed timestamps
        $approvals = $this->approvalModel->getApprovalChain($suratId);
        
        // Get next steps prediction
        $nextSteps = $this->predictNextSteps($surat);
        
        $data = [
            'title' => 'Workflow Timeline - ' . $surat['nomor_surat'],
            'surat' => $surat,
            'workflow' => $workflow,
            'processing_time' => $processing_time,
            'approvals' => $approvals,
            'next_steps' => $nextSteps,
            'user_role' => $this->session->get('user_role')
        ];

        return view('workflow/timeline', $data);
    }

    private function predictNextSteps($surat)
    {
        $status = $surat['status'];
        
        $predictions = [
            'SUBMITTED' => [
                'next_action' => 'Verifikasi administratif',
                'next_actor' => 'Staff Umum',
                'estimated_hours' => 4,
                'description' => 'Dokumen akan diverifikasi kelengkapan dan kesesuaian format'
            ],
            'UNDER_REVIEW' => [
                'next_action' => 'Review dokumen',
                'next_actor' => 'Staff Umum',
                'estimated_hours' => 8,
                'description' => 'Dokumen sedang direview untuk persetujuan level 1'
            ],
            'APPROVED_L1' => [
                'next_action' => 'Persetujuan Kepala Bagian',
                'next_actor' => 'Kabag TU',
                'estimated_hours' => 12,
                'description' => 'Menunggu persetujuan dari Kepala Bagian Tata Usaha'
            ],
            'APPROVED_L2' => [
                'next_action' => 'Persetujuan Dekan',
                'next_actor' => 'Dekan',
                'estimated_hours' => 24,
                'description' => 'Menunggu persetujuan final dari Dekan'
            ],
            'READY_DISPOSISI' => [
                'next_action' => 'Disposisi ke Unit',
                'next_actor' => 'Dekan',
                'estimated_hours' => 6,
                'description' => 'Surat siap didisposisi ke unit terkait untuk pelaksanaan'
            ],
            'IN_PROCESS' => [
                'next_action' => 'Pelaksanaan tugas',
                'next_actor' => 'Unit Terkait',
                'estimated_hours' => 48,
                'description' => 'Surat sedang diproses oleh unit yang bertanggung jawab'
            ]
        ];

        return $predictions[$status] ?? null;
    }
}