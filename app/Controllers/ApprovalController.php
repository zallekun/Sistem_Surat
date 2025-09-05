<?php

namespace App\Controllers;

use App\Models\SuratModel;
use CodeIgniter\Controller;

class ApprovalController extends Controller
{
    protected $suratModel;
    protected $session;

    public function __construct()
    {
        $this->suratModel = new SuratModel();
        $this->session = \Config\Services::session();
    }

    public function pending()
    {
        if (!$this->session->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = $this->session->get('user_role');
        $userId = $this->session->get('user_id');

        // Check if user has approval access
        if (!in_array($userRole, ['staff_umum', 'kabag_tu', 'dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum', 'kaur_keuangan'])) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses approval');
        }

        // Get pending approvals for current user role
        $pendingSurat = $this->suratModel->getSuratForApproval($userRole);
        
        // Get statistics
        $stats = [
            'pending' => count($pendingSurat),
            'today' => $this->getTodayApprovals($userId),
            'this_week' => $this->getWeekApprovals($userId),
            'total_approved' => $this->getTotalApproved($userId)
        ];

        $data = [
            'title' => 'Pending Approvals - Sistem Surat',
            'surat' => $pendingSurat,
            'stats' => $stats,
            'user_role' => $userRole,
            'page_type' => 'pending'
        ];

        return view('approval/index', $data);
    }

    public function completed()
    {
        if (!$this->session->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = $this->session->get('user_role');
        $userId = $this->session->get('user_id');

        // Check if user has approval access
        if (!in_array($userRole, ['staff_umum', 'kabag_tu', 'dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum', 'kaur_keuangan'])) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses approval');
        }

        // Get completed surat based on user role and what they can see
        $completedSurat = $this->getCompletedSurat($userRole, $userId);
        
        // Get statistics
        $stats = [
            'completed' => count($completedSurat),
            'today' => $this->getTodayApprovals($userId),
            'this_week' => $this->getWeekApprovals($userId),
            'total_approved' => $this->getTotalApproved($userId)
        ];

        $data = [
            'title' => 'Completed Approvals - Sistem Surat',
            'surat' => $completedSurat,
            'stats' => $stats,
            'user_role' => $userRole,
            'page_type' => 'completed'
        ];

        return view('approval/index', $data);
    }

    private function getCompletedSurat($userRole, $userId)
    {
        $builder = $this->suratModel->select('
            surat.*, 
            prodi.nama_prodi,
            creator.nama as created_by_name
        ')
        ->join('prodi', 'prodi.id = surat.prodi_id', 'left')
        ->join('users creator', 'creator.id = surat.created_by', 'left');

        // Filter based on user role - show surat they have involvement in
        if ($userRole === 'staff_umum' || $userRole === 'kabag_tu' || $userRole === 'dekan') {
            // Show completed surat that went through their approval stage
            $builder->where('surat.status', 'COMPLETED');
        } else {
            // For final approvers (WD/Kaur), show surat they completed
            $builder->where('surat.status', 'COMPLETED');
            
            // Additional filter based on category responsibility
            $categoryMapping = [
                'wd_akademik' => 'akademik',
                'wd_kemahasiswaan' => 'kemahasiswaan',
                'wd_umum' => ['kepegawaian', 'umum'],
                'kaur_keuangan' => 'keuangan'
            ];
            
            if (isset($categoryMapping[$userRole])) {
                if (is_array($categoryMapping[$userRole])) {
                    $builder->whereIn('surat.kategori', $categoryMapping[$userRole]);
                } else {
                    $builder->where('surat.kategori', $categoryMapping[$userRole]);
                }
            }
        }

        return $builder->orderBy('surat.updated_at', 'DESC')->findAll();
    }

    private function getTodayApprovals($userId)
    {
        return $this->suratModel->db->table('surat_workflow')
            ->where('action_by', $userId)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->whereIn('action_type', ['APPROVE', 'COMPLETE'])
            ->countAllResults();
    }

    private function getWeekApprovals($userId)
    {
        return $this->suratModel->db->table('surat_workflow')
            ->where('action_by', $userId)
            ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
            ->whereIn('action_type', ['APPROVE', 'COMPLETE'])
            ->countAllResults();
    }

    private function getTotalApproved($userId)
    {
        return $this->suratModel->db->table('surat_workflow')
            ->where('action_by', $userId)
            ->whereIn('action_type', ['APPROVE', 'COMPLETE'])
            ->countAllResults();
    }
}