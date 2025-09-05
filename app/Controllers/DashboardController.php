<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DashboardController extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->session->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = $this->session->get('user_role');
        $userId = $this->session->get('user_id');
        $prodiId = $this->session->get('user_prodi_id');

        // Get stats based on user role
        $suratModel = new \App\Models\SuratModel();
        $workflowModel = new \App\Models\SuratWorkflowModel();
        
        $rawStats = $suratModel->getSuratStats($prodiId);
        
        // Transform stats for dashboard view
        $stats = [
            'total' => array_sum($rawStats),
            'approved' => ($rawStats[\App\Models\SuratModel::STATUS_COMPLETED] ?? 0),
            'rejected' => ($rawStats[\App\Models\SuratModel::STATUS_REJECTED] ?? 0)
        ];
        
        // Get pending approvals for current user
        $pendingApprovals = [];
        if (in_array($userRole, ['staff_umum', 'kabag_tu', 'dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum', 'kaur_keuangan'])) {
            $pendingApprovals = $suratModel->getSuratForApproval($userRole);
        }

        // Get user activity
        $userActivity = $workflowModel->getUserActivitySummary($userId, 7);

        $data = [
            'title' => 'Dashboard - Sistem Surat Menyurat',
            'user_name' => $this->session->get('user_name'),
            'user_role' => $userRole,
            'stats' => $stats,
            'pending_approvals' => $pendingApprovals,
            'user_activity' => $userActivity,
            'debug_role' => $userRole // Debug: show current role
        ];

        return view('dashboard/index', $data);
    }
}