<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;

class TestLogin extends BaseCommand
{
    protected $group = 'debug';
    protected $name = 'debug:login';
    protected $description = 'Test login process for different users';

    public function run(array $params)
    {
        $userModel = new UserModel();

        CLI::write('=== LOGIN TEST ===', 'green');

        // Test admin prodi
        $adminProdi = $userModel->where('email', 'admin.ti@universitas.ac.id')->first();
        if ($adminProdi) {
            CLI::write('👤 Admin Prodi found:', 'yellow');
            CLI::write("  - ID: {$adminProdi['id']}", 'white');
            CLI::write("  - Name: {$adminProdi['nama']}", 'white');
            CLI::write("  - Email: {$adminProdi['email']}", 'white');
            CLI::write("  - Role: {$adminProdi['role']}", 'white');
            CLI::write("  - Prodi ID: {$adminProdi['prodi_id']}", 'white');
            CLI::write("  - Active: " . ($adminProdi['is_active'] ? 'YES' : 'NO'), 'white');
        } else {
            CLI::write('❌ Admin Prodi NOT found', 'red');
        }

        // Test staff umum
        $staffUmum = $userModel->where('email', 'staff.umum@universitas.ac.id')->first();
        if ($staffUmum) {
            CLI::write('👤 Staff Umum found:', 'yellow');
            CLI::write("  - ID: {$staffUmum['id']}", 'white');
            CLI::write("  - Name: {$staffUmum['nama']}", 'white');
            CLI::write("  - Email: {$staffUmum['email']}", 'white');
            CLI::write("  - Role: {$staffUmum['role']}", 'white');
            CLI::write("  - Prodi ID: " . ($staffUmum['prodi_id'] ?? 'NULL'), 'white');
            CLI::write("  - Active: " . ($staffUmum['is_active'] ? 'YES' : 'NO'), 'white');
        } else {
            CLI::write('❌ Staff Umum NOT found', 'red');
        }

        // Test password verification
        if ($staffUmum) {
            $passwordCorrect = password_verify('staffumum123', $staffUmum['password']);
            CLI::write('🔐 Password verification: ' . ($passwordCorrect ? 'CORRECT' : 'WRONG'), $passwordCorrect ? 'green' : 'red');
        }

        CLI::write('=== LOGIN TEST COMPLETE ===', 'green');
    }
}