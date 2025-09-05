<?php

namespace App\Controllers;

use App\Models\SuratModel;
use App\Models\SuratWorkflowModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class AnalyticsController extends Controller
{
    protected $suratModel;
    protected $workflowModel;
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->suratModel = new SuratModel();
        $this->workflowModel = new SuratWorkflowModel();
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        // Check if user has analytics access
        if (!$this->hasAnalyticsAccess()) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses untuk melihat analytics');
        }

        $userRole = $this->session->get('user_role');
        $prodiId = $this->session->get('user_prodi_id');

        // Get Executive KPIs
        $kpis = $this->getExecutiveKPIs($prodiId);
        
        // Get workflow performance data
        $workflowStats = $this->getWorkflowPerformance($prodiId);
        
        // Get monthly trends (last 6 months)
        $monthlyTrends = $this->getMonthlyTrends($prodiId);
        
        // Get top performers
        $topPerformers = $this->getTopPerformers();

        $data = [
            'title' => 'Analytics Dashboard - Sistem Surat',
            'user_role' => $userRole,
            'kpis' => $kpis,
            'workflow_stats' => $workflowStats,
            'monthly_trends' => $monthlyTrends,
            'top_performers' => $topPerformers
        ];

        return view('analytics/index', $data);
    }

    public function reports()
    {
        if (!$this->hasAnalyticsAccess()) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses untuk melihat reports');
        }

        $data = [
            'title' => 'Reports - Analytics Dashboard',
            'user_role' => $this->session->get('user_role')
        ];

        return view('analytics/reports', $data);
    }

    public function exportPDF()
    {
        if (!$this->hasAnalyticsAccess()) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        // Implementation for PDF export will be added later
        return $this->response->setJSON(['message' => 'PDF export feature coming soon']);
    }

    public function getChartData($type)
    {
        if (!$this->hasAnalyticsAccess()) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $prodiId = $this->session->get('user_prodi_id');

        switch ($type) {
            case 'monthly':
                return $this->response->setJSON($this->getMonthlyTrends($prodiId));
            case 'workflow':
                return $this->response->setJSON($this->getWorkflowPerformance($prodiId));
            case 'performance':
                return $this->response->setJSON($this->getTopPerformers());
            default:
                return $this->response->setJSON(['error' => 'Invalid chart type']);
        }
    }

    private function hasAnalyticsAccess()
    {
        $userRole = $this->session->get('user_role');
        $allowedRoles = ['dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum', 'kabag_tu', 'admin_prodi'];
        
        return in_array($userRole, $allowedRoles);
    }

    private function getExecutiveKPIs($prodiId = null)
    {
        $builder = $this->suratModel->builder();
        
        if ($prodiId) {
            $builder->where('prodi_id', $prodiId);
        }

        // Total surat
        $totalSurat = $builder->countAllResults(false);

        // Surat completed this month
        $completedThisMonth = $builder->where('status', SuratModel::STATUS_COMPLETED)
                                    ->where('MONTH(updated_at)', date('m'))
                                    ->where('YEAR(updated_at)', date('Y'))
                                    ->countAllResults(false);

        // Average processing time (in days)
        $avgProcessingTime = $this->getAverageProcessingTime($prodiId);

        // Completion rate
        $completed = $builder->where('status', SuratModel::STATUS_COMPLETED)->countAllResults(false);
        $completionRate = $totalSurat > 0 ? round(($completed / $totalSurat) * 100, 1) : 0;

        // Pending approvals
        $pendingApprovals = $builder->whereIn('status', [
            SuratModel::STATUS_SUBMITTED,
            SuratModel::STATUS_UNDER_REVIEW,
            SuratModel::STATUS_APPROVED_L1,
            SuratModel::STATUS_APPROVED_L2
        ])->countAllResults();

        return [
            'total_surat' => $totalSurat,
            'completed_this_month' => $completedThisMonth,
            'avg_processing_days' => $avgProcessingTime,
            'completion_rate' => $completionRate,
            'pending_approvals' => $pendingApprovals
        ];
    }

    private function getWorkflowPerformance($prodiId = null)
    {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count,
                    AVG(DATEDIFF(updated_at, created_at)) as avg_days
                FROM surat 
                WHERE 1=1";
        
        $params = [];
        if ($prodiId) {
            $sql .= " AND prodi_id = ?";
            $params[] = $prodiId;
        }
        
        $sql .= " GROUP BY status ORDER BY FIELD(status, 'DRAFT', 'SUBMITTED', 'UNDER_REVIEW', 'APPROVED_L1', 'APPROVED_L2', 'READY_DISPOSISI', 'IN_PROCESS', 'COMPLETED')";

        $query = $this->suratModel->db->query($sql, $params);
        return $query->getResultArray();
    }

    private function getMonthlyTrends($prodiId = null)
    {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as total_created,
                    SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed
                FROM surat 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
        
        $params = [];
        if ($prodiId) {
            $sql .= " AND prodi_id = ?";
            $params[] = $prodiId;
        }
        
        $sql .= " GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                  ORDER BY month ASC";

        $query = $this->suratModel->db->query($sql, $params);
        return $query->getResultArray();
    }

    private function getTopPerformers()
    {
        $sql = "SELECT 
                    u.nama,
                    u.role,
                    COUNT(sw.id) as total_actions,
                    AVG(CASE 
                        WHEN sw.action_type = 'APPROVE' THEN 1
                        WHEN sw.action_type = 'COMPLETE' THEN 1  
                        ELSE 0 
                    END) as approval_rate
                FROM users u
                LEFT JOIN surat_workflow sw ON u.id = sw.action_by
                WHERE sw.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY u.id, u.nama, u.role
                HAVING total_actions > 0
                ORDER BY total_actions DESC, approval_rate DESC
                LIMIT 10";

        $query = $this->suratModel->db->query($sql);
        return $query->getResultArray();
    }

    private function getAverageProcessingTime($prodiId = null)
    {
        $sql = "SELECT 
                    AVG(DATEDIFF(
                        COALESCE(completed_at, NOW()),
                        created_at
                    )) as avg_days
                FROM surat 
                WHERE status IN ('COMPLETED', 'IN_PROCESS')";
        
        $params = [];
        if ($prodiId) {
            $sql .= " AND prodi_id = ?";
            $params[] = $prodiId;
        }

        $query = $this->suratModel->db->query($sql, $params);
        $result = $query->getRow();
        
        return $result ? round($result->avg_days, 1) : 0;
    }
}