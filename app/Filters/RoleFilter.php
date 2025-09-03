<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        
        // Check if user is logged in
        if (!$session->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userRole = $session->get('user_role');
        $allowedRoles = $arguments ?? [];

        // If no specific roles required, just check if logged in
        if (empty($allowedRoles)) {
            return;
        }

        // Check if user's role is in allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}