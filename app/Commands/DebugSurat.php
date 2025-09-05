<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SuratModel;
use App\Models\UserModel;

class DebugSurat extends BaseCommand
{
    protected $group = 'debug';
    protected $name = 'debug:surat';
    protected $description = 'Debug surat database operations';

    public function run(array $params)
    {
        $suratModel = new SuratModel();
        $userModel = new UserModel();

        CLI::write('=== DEBUG SURAT DATABASE ===', 'green');

        // Test database connection
        $db = \Config\Database::connect();
        if ($db->connect()) {
            CLI::write('✓ Database connection: OK', 'green');
        } else {
            CLI::write('✗ Database connection: FAILED', 'red');
            return;
        }

        // Check if surat table exists
        if ($db->tableExists('surat')) {
            CLI::write('✓ Surat table: EXISTS', 'green');
        } else {
            CLI::write('✗ Surat table: NOT FOUND', 'red');
            return;
        }

        // Get total surat count
        $totalSurat = $suratModel->countAll();
        CLI::write("📊 Total surat in database: {$totalSurat}", 'yellow');

        // Get all surat
        $allSurat = $suratModel->findAll();
        CLI::write('📝 Recent surat:', 'yellow');
        foreach ($allSurat as $surat) {
            CLI::write("  - ID: {$surat['id']}, Nomor: {$surat['nomor_surat']}, Status: {$surat['status']}, Created by: {$surat['created_by']}", 'white');
        }

        // Test with admin_prodi user
        $adminProdi = $userModel->where('role', 'admin_prodi')->first();
        if ($adminProdi) {
            CLI::write("👤 Found admin_prodi user: {$adminProdi['nama']} (ID: {$adminProdi['id']})", 'yellow');
            
            $suratByUser = $suratModel->getSuratByUser($adminProdi['id']);
            CLI::write("📋 Surat by user {$adminProdi['id']}: " . count($suratByUser), 'yellow');
            
            foreach ($suratByUser as $surat) {
                CLI::write("  - {$surat['nomor_surat']} - {$surat['status']}", 'white');
            }
        } else {
            CLI::write('👤 No admin_prodi user found', 'red');
        }

        // Test manual insert
        CLI::write('🔨 Testing manual insert...', 'yellow');
        
        if ($adminProdi) {
            $testData = [
                'nomor_surat' => 'TEST-001/TI/09/2025',
                'perihal' => 'Test Insert Surat dari Command',
                'tanggal_surat' => date('Y-m-d'),
                'kategori' => 'umum',
                'prioritas' => 'normal',
                'tujuan' => 'Test Tujuan',
                'prodi_id' => $adminProdi['prodi_id'],
                'created_by' => $adminProdi['id'],
                'current_holder' => $adminProdi['id'],
                'status' => 'DRAFT',
                'keterangan' => 'Test keterangan',
                'deadline' => date('Y-m-d', strtotime('+7 days'))
            ];
            
            CLI::write('Data to insert: ' . json_encode($testData), 'white');
            
            $insertId = $suratModel->insert($testData);
            if ($insertId) {
                CLI::write("✓ Manual insert: SUCCESS (ID: {$insertId})", 'green');
            } else {
                CLI::write("✗ Manual insert: FAILED", 'red');
                $errors = $suratModel->errors();
                if ($errors) {
                    CLI::write('Errors: ' . json_encode($errors), 'red');
                }
            }
        }

        // Test submit process
        CLI::write('🚀 Testing submit process...', 'yellow');
        
        $draftSurat = $suratModel->where('status', 'DRAFT')->first();
        if ($draftSurat) {
            CLI::write("Found DRAFT surat: {$draftSurat['nomor_surat']}", 'white');
            
            // Get staff_umum user
            $staffUmum = $userModel->getUsersByRole('staff_umum', true);
            if (!empty($staffUmum)) {
                $firstStaff = $staffUmum[0];
                CLI::write("Found staff_umum: {$firstStaff['nama']} (ID: {$firstStaff['id']})", 'white');
                
                // Update to SUBMITTED
                $updated = $suratModel->updateStatus($draftSurat['id'], 'SUBMITTED', $firstStaff['id']);
                if ($updated) {
                    CLI::write("✅ Status updated: DRAFT → SUBMITTED", 'green');
                    
                    // Check if staff can see it now
                    $suratForStaff = $suratModel->getSuratForApproval('staff_umum');
                    CLI::write("📋 Staff umum can now see: " . count($suratForStaff) . " surat", 'green');
                } else {
                    CLI::write("❌ Failed to update status", 'red');
                }
            } else {
                CLI::write("❌ No staff_umum found", 'red');
            }
        } else {
            CLI::write("No DRAFT surat found", 'white');
        }

        // Debug Staff Umum access
        CLI::write('🔍 Testing Staff Umum access...', 'yellow');
        
        $staffUmum = $userModel->getUsersByRole('staff_umum', true);
        if (!empty($staffUmum)) {
            $staff = $staffUmum[0];
            CLI::write("Staff found: {$staff['nama']} (ID: {$staff['id']}, Email: {$staff['email']})", 'white');
            
            // Test what surat staff can see
            $suratForStaff = $suratModel->getSuratForApproval('staff_umum');
            CLI::write("📋 Surat visible to staff_umum: " . count($suratForStaff), 'white');
            
            foreach ($suratForStaff as $surat) {
                CLI::write("  - ID: {$surat['id']}, Nomor: {$surat['nomor_surat']}, Status: {$surat['status']}", 'white');
            }
            
            // Check all SUBMITTED surat
            $submittedSurat = $suratModel->where('status', 'SUBMITTED')->findAll();
            CLI::write("📤 Total SUBMITTED surat in DB: " . count($submittedSurat), 'white');
            
            foreach ($submittedSurat as $surat) {
                CLI::write("  - ID: {$surat['id']}, Nomor: {$surat['nomor_surat']}, Status: {$surat['status']}, Current Holder: {$surat['current_holder']}, Prodi ID: {$surat['prodi_id']}", 'white');
            }
            
            // Check prodi data
            $prodiModel = new \App\Models\ProdiModel();
            $prodi = $prodiModel->find($submittedSurat[0]['prodi_id']);
            if ($prodi) {
                CLI::write("🏫 Prodi data found: {$prodi['nama_prodi']} (Fakultas ID: {$prodi['fakultas_id']})", 'white');
                
                $fakultasModel = new \App\Models\FakultasModel();
                $fakultas = $fakultasModel->find($prodi['fakultas_id']);
                if ($fakultas) {
                    CLI::write("🏛️ Fakultas data found: {$fakultas['nama_fakultas']}", 'white');
                } else {
                    CLI::write("❌ Fakultas NOT found for ID: {$prodi['fakultas_id']}", 'red');
                }
            } else {
                CLI::write("❌ Prodi NOT found for ID: {$submittedSurat[0]['prodi_id']}", 'red');
            }
        }

        // Test manual query
        CLI::write('🔬 Testing manual query...', 'yellow');
        
        $db = \Config\Database::connect();
        $manualQuery = $db->query("
            SELECT surat.*, prodi.nama_prodi, fakultas.nama_fakultas, creator.nama as creator_name
            FROM surat 
            JOIN prodi ON prodi.id = surat.prodi_id
            JOIN fakultas ON fakultas.id = prodi.fakultas_id  
            JOIN users creator ON creator.id = surat.created_by
            WHERE surat.status = 'SUBMITTED'
        ");
        
        $manualResults = $manualQuery->getResultArray();
        CLI::write('📊 Manual query results: ' . count($manualResults), 'white');
        
        foreach ($manualResults as $result) {
            CLI::write("  - ID: {$result['id']}, Nomor: {$result['nomor_surat']}, Status: {$result['status']}, Creator: {$result['creator_name']}", 'white');
        }
        
        // Test getSuratForApproval with debug
        CLI::write('🔍 Testing getSuratForApproval method...', 'yellow');
        
        try {
            $testResult = $suratModel->getSuratForApproval('staff_umum');
            CLI::write('✅ getSuratForApproval success: ' . count($testResult), 'green');
            
            foreach ($testResult as $surat) {
                CLI::write("  - ID: {$surat['id']}, Nomor: {$surat['nomor_surat']}, Status: {$surat['status']}", 'white');
            }
        } catch (\Exception $e) {
            CLI::write('❌ getSuratForApproval error: ' . $e->getMessage(), 'red');
        }

        CLI::write('=== DEBUG COMPLETE ===', 'green');
    }
}