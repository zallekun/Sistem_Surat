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

        $data = [
            'title' => 'Dashboard - Sistem Surat Menyurat',
            'user_name' => $this->session->get('user_name'),
            'user_role' => $this->session->get('user_role'),
        ];

        return view('dashboard/index', $data);
    }
}