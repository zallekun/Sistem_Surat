<?php

namespace App\Controllers;

use App\Models\SuratModel;
use App\Models\UserModel;
use App\Models\ProdiModel;
use CodeIgniter\Controller;

class SearchController extends Controller
{
    protected $suratModel;
    protected $userModel;
    protected $prodiModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->suratModel = new SuratModel();
        $this->userModel = new UserModel();
        $this->prodiModel = new ProdiModel();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $userRole = $this->session->get('user_role');
        $userId = $this->session->get('user_id');
        $prodiId = $this->session->get('user_prodi_id');

        // Get search parameters
        $searchTerm = $this->request->getGet('q') ?? '';
        $status = $this->request->getGet('status') ?? '';
        $kategori = $this->request->getGet('kategori') ?? '';
        $prioritas = $this->request->getGet('prioritas') ?? '';
        $dateFrom = $this->request->getGet('date_from') ?? '';
        $dateTo = $this->request->getGet('date_to') ?? '';
        $createdBy = $this->request->getGet('created_by') ?? '';

        // Perform search
        $searchResults = $this->performAdvancedSearch([
            'q' => $searchTerm,
            'status' => $status,
            'kategori' => $kategori,
            'prioritas' => $prioritas,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'created_by' => $createdBy,
            'user_role' => $userRole,
            'user_id' => $userId,
            'prodi_id' => $prodiId
        ]);

        // Get filter options
        $filterOptions = $this->getFilterOptions($userRole, $prodiId);
        
        // Get saved searches for current user
        $savedSearches = $this->getSavedSearches($userId);

        // Track search if there's a search term
        if (!empty($searchTerm)) {
            $this->trackSearch($userId, $searchTerm, count($searchResults));
        }

        $data = [
            'title' => 'Advanced Search - Sistem Surat',
            'results' => $searchResults,
            'search_params' => [
                'q' => $searchTerm,
                'status' => $status,
                'kategori' => $kategori,
                'prioritas' => $prioritas,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'created_by' => $createdBy
            ],
            'filter_options' => $filterOptions,
            'saved_searches' => $savedSearches,
            'user_role' => $userRole,
            'result_count' => count($searchResults)
        ];

        return view('search/index', $data);
    }

    public function suggestions()
    {
        $query = $this->request->getGet('q') ?? '';
        $userRole = $this->session->get('user_role');
        $prodiId = $this->session->get('user_prodi_id');

        if (strlen($query) < 2) {
            return $this->response->setJSON([]);
        }

        $suggestions = $this->generateSearchSuggestions($query, $userRole, $prodiId);
        
        return $this->response->setJSON($suggestions);
    }

    public function saveSearch()
    {
        $userId = $this->session->get('user_id');
        $searchName = $this->request->getPost('name');
        $searchParams = $this->request->getPost('params');

        if (empty($searchName) || empty($searchParams)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid parameters']);
        }

        $searchId = $this->saveSavedSearch($userId, $searchName, $searchParams);

        if ($searchId) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Search saved successfully',
                'search_id' => $searchId
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to save search']);
    }

    public function deleteSavedSearch($searchId)
    {
        $userId = $this->session->get('user_id');
        
        $deleted = $this->deleteSavedSearchRecord($userId, $searchId);

        if ($deleted) {
            return $this->response->setJSON(['success' => true, 'message' => 'Search deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete search']);
    }

    public function analytics()
    {
        $userRole = $this->session->get('user_role');
        
        // Only admin/management can view search analytics
        if (!in_array($userRole, ['dekan', 'kabag_tu', 'admin_prodi'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $analytics = $this->getSearchAnalytics();

        $data = [
            'title' => 'Search Analytics - Sistem Surat',
            'analytics' => $analytics,
            'user_role' => $userRole
        ];

        return view('search/analytics', $data);
    }

    private function performAdvancedSearch($params)
    {
        $builder = $this->suratModel->select('
            surat.*, 
            prodi.nama_prodi,
            users.nama as created_by_name,
            surat.created_at as surat_created_at
        ')
        ->join('prodi', 'prodi.id = surat.prodi_id', 'left')
        ->join('users', 'users.id = surat.created_by', 'left');

        // Apply user role filters
        if ($params['user_role'] === 'admin_prodi') {
            $builder->where('surat.created_by', $params['user_id']);
        } elseif (in_array($params['user_role'], ['staff_umum', 'kabag_tu', 'dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum', 'kaur_keuangan'])) {
            // Show surat that user can access based on workflow
            $accessibleStatuses = $this->getAccessibleStatuses($params['user_role']);
            if (!empty($accessibleStatuses)) {
                $builder->whereIn('surat.status', $accessibleStatuses);
            }
        }

        // Apply search term - search across multiple fields
        if (!empty($params['q'])) {
            $searchTerm = '%' . $params['q'] . '%';
            $builder->groupStart()
                ->like('surat.nomor_surat', $searchTerm)
                ->orLike('surat.perihal', $searchTerm)
                ->orLike('surat.isi_ringkas', $searchTerm)
                ->orLike('users.nama', $searchTerm)
                ->orLike('prodi.nama_prodi', $searchTerm)
                ->groupEnd();
        }

        // Apply filters
        if (!empty($params['status'])) {
            if (strpos($params['status'], ',') !== false) {
                $statuses = explode(',', $params['status']);
                $builder->whereIn('surat.status', $statuses);
            } else {
                $builder->where('surat.status', $params['status']);
            }
        }

        if (!empty($params['kategori'])) {
            $builder->where('surat.kategori', $params['kategori']);
        }

        if (!empty($params['prioritas'])) {
            $builder->where('surat.prioritas', $params['prioritas']);
        }

        if (!empty($params['date_from'])) {
            $builder->where('DATE(surat.created_at) >=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $builder->where('DATE(surat.created_at) <=', $params['date_to']);
        }

        if (!empty($params['created_by'])) {
            $builder->where('surat.created_by', $params['created_by']);
        }

        // Apply prodi filter if applicable
        if ($params['prodi_id'] && $params['user_role'] === 'admin_prodi') {
            $builder->where('surat.prodi_id', $params['prodi_id']);
        }

        // Order by relevance and date
        $builder->orderBy('surat.updated_at', 'DESC');

        return $builder->findAll();
    }

    private function generateSearchSuggestions($query, $userRole, $prodiId)
    {
        $suggestions = [];

        // Search in nomor surat
        $suratNumbers = $this->db->table('surat')
            ->select('nomor_surat')
            ->like('nomor_surat', $query)
            ->distinct()
            ->limit(5)
            ->get()
            ->getResultArray();

        foreach ($suratNumbers as $surat) {
            $suggestions[] = [
                'type' => 'nomor_surat',
                'value' => $surat['nomor_surat'],
                'label' => $surat['nomor_surat'],
                'icon' => 'bi-file-text'
            ];
        }

        // Search in perihal
        $perihals = $this->db->table('surat')
            ->select('perihal')
            ->like('perihal', $query)
            ->distinct()
            ->limit(3)
            ->get()
            ->getResultArray();

        foreach ($perihals as $perihal) {
            $suggestions[] = [
                'type' => 'perihal',
                'value' => $perihal['perihal'],
                'label' => substr($perihal['perihal'], 0, 50) . (strlen($perihal['perihal']) > 50 ? '...' : ''),
                'icon' => 'bi-chat-text'
            ];
        }

        // Search in user names (if allowed)
        if (in_array($userRole, ['dekan', 'kabag_tu', 'admin_prodi'])) {
            $users = $this->db->table('users')
                ->select('nama')
                ->like('nama', $query)
                ->distinct()
                ->limit(3)
                ->get()
                ->getResultArray();

            foreach ($users as $user) {
                $suggestions[] = [
                    'type' => 'user',
                    'value' => $user['nama'],
                    'label' => $user['nama'],
                    'icon' => 'bi-person'
                ];
            }
        }

        return array_slice($suggestions, 0, 10);
    }

    private function getFilterOptions($userRole, $prodiId)
    {
        $options = [
            'status' => [
                'DRAFT' => 'Draft',
                'SUBMITTED' => 'Submitted',
                'UNDER_REVIEW' => 'Under Review',
                'NEED_REVISION' => 'Need Revision',
                'APPROVED_L1' => 'Approved Level 1',
                'APPROVED_L2' => 'Approved Level 2',
                'READY_DISPOSISI' => 'Ready Disposisi',
                'IN_PROCESS' => 'In Process',
                'COMPLETED' => 'Completed',
                'REJECTED' => 'Rejected'
            ],
            'kategori' => [
                'akademik' => 'Akademik',
                'kemahasiswaan' => 'Kemahasiswaan',
                'kepegawaian' => 'Kepegawaian',
                'keuangan' => 'Keuangan',
                'umum' => 'Umum'
            ],
            'prioritas' => [
                'normal' => 'Normal',
                'urgent' => 'Urgent',
                'sangat_urgent' => 'Sangat Urgent'
            ]
        ];

        // Get users for created_by filter
        if (in_array($userRole, ['dekan', 'kabag_tu', 'admin_prodi'])) {
            $users = $this->userModel->findAll();
            $options['users'] = [];
            foreach ($users as $user) {
                $options['users'][$user['id']] = $user['nama'];
            }
        }

        return $options;
    }

    private function getAccessibleStatuses($userRole)
    {
        $statusMap = [
            'staff_umum' => ['SUBMITTED', 'UNDER_REVIEW', 'APPROVED_L1', 'APPROVED_L2', 'COMPLETED'],
            'kabag_tu' => ['UNDER_REVIEW', 'APPROVED_L1', 'APPROVED_L2', 'COMPLETED'],
            'dekan' => ['APPROVED_L1', 'APPROVED_L2', 'READY_DISPOSISI', 'IN_PROCESS', 'COMPLETED'],
            'wd_akademik' => ['IN_PROCESS', 'COMPLETED'],
            'wd_kemahasiswaan' => ['IN_PROCESS', 'COMPLETED'],
            'wd_umum' => ['IN_PROCESS', 'COMPLETED'],
            'kaur_keuangan' => ['IN_PROCESS', 'COMPLETED']
        ];

        return $statusMap[$userRole] ?? [];
    }

    private function getSavedSearches($userId)
    {
        return $this->db->table('saved_searches')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function saveSavedSearch($userId, $name, $params)
    {
        $data = [
            'user_id' => $userId,
            'name' => $name,
            'search_params' => json_encode($params),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->table('saved_searches')->insert($data) ? $this->db->insertID() : false;
    }

    private function deleteSavedSearchRecord($userId, $searchId)
    {
        return $this->db->table('saved_searches')
            ->where('id', $searchId)
            ->where('user_id', $userId)
            ->delete();
    }

    private function trackSearch($userId, $searchTerm, $resultCount)
    {
        $data = [
            'user_id' => $userId,
            'search_term' => $searchTerm,
            'result_count' => $resultCount,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('search_logs')->insert($data);
    }

    private function getSearchAnalytics()
    {
        $topSearches = $this->db->table('search_logs')
            ->select('search_term, COUNT(*) as search_count')
            ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
            ->groupBy('search_term')
            ->orderBy('search_count', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $searchTrends = $this->db->table('search_logs')
            ->select('DATE(created_at) as search_date, COUNT(*) as daily_searches')
            ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
            ->groupBy('DATE(created_at)')
            ->orderBy('search_date', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'top_searches' => $topSearches,
            'search_trends' => $searchTrends
        ];
    }
}