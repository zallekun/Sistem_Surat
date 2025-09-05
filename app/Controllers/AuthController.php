<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    public function login()
    {
        // Redirect if already logged in
        if ($this->session->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login - Sistem Surat Menyurat',
            'validation' => null
        ];

        return view('auth/login', $data);
    }

    public function authenticate()
    {
        $rules = [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Format email tidak valid'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password harus diisi'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return view('auth/login', [
                'title' => 'Login - Sistem Surat Menyurat',
                'validation' => $this->validator,
                'old_input' => $this->request->getPost()
            ]);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            // Set session data
            $sessionData = [
                'user_id' => $user['id'],
                'user_name' => $user['nama'],
                'user_email' => $user['email'],
                'user_role' => $user['role'],
                'user_prodi_id' => $user['prodi_id'],
                'user_divisi_id' => $user['divisi_id'],
                'is_logged_in' => true
            ];

            $this->session->set($sessionData);

            // Set success message
            $this->session->setFlashdata('success', 'Login berhasil! Selamat datang, ' . $user['nama']);

            // Redirect based on role
            return redirect()->to('/dashboard');
        } else {
            return view('auth/login', [
                'title' => 'Login - Sistem Surat Menyurat',
                'error' => 'Email atau password salah',
                'old_input' => $this->request->getPost()
            ]);
        }
    }

    public function logout()
    {
        // Clear all session data
        $this->session->destroy();

        // Set logout message
        $this->session->setFlashdata('info', 'Anda telah berhasil logout');

        return redirect()->to('/login');
    }

    public function profile()
    {
        if (!$this->session->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $user = $this->userModel->getUserWithRelations($userId);

        if (!$user) {
            $this->session->setFlashdata('error', 'User tidak ditemukan');
            return redirect()->to('/dashboard');
        }

        // Get user statistics
        $suratModel = new \App\Models\SuratModel();
        $workflowModel = new \App\Models\SuratWorkflowModel();
        
        $stats = [
            'total_created' => $suratModel->where('created_by', $userId)->countAllResults(),
            'total_approved' => $workflowModel->where('action_by', $userId)
                                              ->whereIn('action_type', ['APPROVE', 'COMPLETE'])
                                              ->countAllResults()
        ];

        // Ensure proper field names for the view
        $user['fakultas_nama'] = $user['nama_fakultas'] ?? null;
        $user['prodi_nama'] = $user['nama_prodi'] ?? null;

        $data = [
            'title' => 'Profile - Sistem Surat Menyurat',
            'user' => $user,
            'stats' => $stats
        ];

        return view('auth/profile', $data);
    }

    public function updateProfile()
    {
        if (!$this->session->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');

        $rules = [
            'nama' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama harus diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'telepon' => [
                'rules' => 'permit_empty|max_length[20]',
                'errors' => [
                    'max_length' => 'Telepon maksimal 20 karakter'
                ]
            ]
        ];

        // Add password validation if password is being updated
        $newPassword = $this->request->getPost('new_password');
        if (!empty($newPassword)) {
            $rules['current_password'] = [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password lama harus diisi untuk mengubah password'
                ]
            ];
            $rules['new_password'] = [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password baru harus diisi',
                    'min_length' => 'Password baru minimal 6 karakter'
                ]
            ];
            $rules['confirm_password'] = [
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Konfirmasi password harus diisi',
                    'matches' => 'Konfirmasi password tidak sama dengan password baru'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return $this->profile();
        }

        // Verify current password if changing password
        if (!empty($newPassword)) {
            $currentUser = $this->userModel->find($userId);
            $currentPassword = $this->request->getPost('current_password');
            
            if (!password_verify($currentPassword, $currentUser['password'])) {
                return view('auth/profile', [
                    'title' => 'Profile - Sistem Surat Menyurat',
                    'user' => $this->userModel->getUserWithRelations($userId),
                    'error' => 'Password lama tidak sesuai'
                ]);
            }
        }

        // Prepare update data
        $updateData = [
            'nama' => $this->request->getPost('nama'),
            'telepon' => $this->request->getPost('telepon')
        ];

        if (!empty($newPassword)) {
            $updateData['password'] = $newPassword; // Will be hashed by model callback
        }

        if ($this->userModel->update($userId, $updateData)) {
            // Update session data
            $this->session->set('user_name', $updateData['nama']);
            
            $this->session->setFlashdata('success', 'Profile berhasil diperbarui');
        } else {
            $this->session->setFlashdata('error', 'Gagal memperbarui profile');
        }

        return redirect()->to('/profile');
    }
}