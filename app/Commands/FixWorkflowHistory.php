<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SuratModel;
use App\Models\SuratWorkflowModel;
use App\Models\ApprovalModel;
use App\Models\UserModel;

class FixWorkflowHistory extends BaseCommand
{
    protected $group = 'debug';
    protected $name = 'fix:workflow';
    protected $description = 'Fix missing workflow history for advanced surat';

    public function run(array $params)
    {
        $suratModel = new SuratModel();
        $workflowModel = new SuratWorkflowModel();
        $approvalModel = new ApprovalModel();
        $userModel = new UserModel();

        CLI::write('=== FIXING WORKFLOW HISTORY ===', 'green');

        // Get surat that have advanced status but missing workflow history
        $advancedSurat = $suratModel->whereIn('status', ['APPROVED_L1', 'APPROVED_L2', 'READY_DISPOSISI', 'COMPLETED'])
                                   ->findAll();

        if (empty($advancedSurat)) {
            CLI::write('❌ No advanced surat found', 'red');
            return;
        }

        foreach ($advancedSurat as $surat) {
            CLI::write("\n📋 Fixing SURAT: {$surat['nomor_surat']} (ID: {$surat['id']}) - Status: {$surat['status']}", 'yellow');
            
            // Check if workflow history exists
            $workflows = $workflowModel->getWorkflowHistory($surat['id']);
            
            if (empty($workflows)) {
                CLI::write("   ⚠️  Missing workflow history - Reconstructing...", 'yellow');
                
                // Get users for reconstruction
                $adminProdi = $userModel->find($surat['created_by']);
                $staffUmum = $userModel->getUsersByRole('staff_umum');
                $kabagTu = $userModel->getUsersByRole('kabag_tu'); 
                $dekan = $userModel->getUsersByRole('dekan');
                
                $staffUmumUser = !empty($staffUmum) ? $staffUmum[0] : null;
                $kabagTuUser = !empty($kabagTu) ? $kabagTu[0] : null;
                $dekanUser = !empty($dekan) ? $dekan[0] : null;
                
                if (!$staffUmumUser || !$kabagTuUser || !$dekanUser) {
                    CLI::write("   ❌ Cannot find required users for reconstruction", 'red');
                    continue;
                }
                
                // Reconstruct workflow based on current status
                $baseTime = strtotime($surat['created_at']);
                
                // 1. SUBMIT (Admin Prodi)
                $workflowModel->insert([
                    'surat_id' => $surat['id'],
                    'from_status' => 'DRAFT',
                    'to_status' => 'SUBMITTED',
                    'action_by' => $adminProdi['id'],
                    'action_type' => 'SUBMIT',
                    'keterangan' => 'Surat disubmit untuk review (rekonstruksi)',
                    'created_at' => date('Y-m-d H:i:s', $baseTime)
                ]);
                CLI::write("   ✅ Added SUBMIT workflow", 'green');
                
                if (in_array($surat['status'], ['APPROVED_L1', 'APPROVED_L2', 'READY_DISPOSISI', 'COMPLETED'])) {
                    // 2. APPROVE L1 (Staff Umum)
                    $workflowModel->insert([
                        'surat_id' => $surat['id'],
                        'from_status' => 'SUBMITTED',
                        'to_status' => 'APPROVED_L1',
                        'action_by' => $staffUmumUser['id'],
                        'action_type' => 'APPROVE',
                        'keterangan' => 'Disetujui oleh Staff Umum (rekonstruksi)',
                        'created_at' => date('Y-m-d H:i:s', $baseTime + 3600) // +1 hour
                    ]);
                    CLI::write("   ✅ Added APPROVE L1 workflow", 'green');
                    
                    // Update approval record
                    $approval1 = $approvalModel->where('surat_id', $surat['id'])->where('level', 1)->first();
                    if ($approval1) {
                        $approvalModel->update($approval1['id'], [
                            'status_approval' => 'approved',
                            'tanggal_approval' => date('Y-m-d H:i:s', $baseTime + 3600),
                            'catatan' => 'Disetujui (rekonstruksi)'
                        ]);
                    }
                }
                
                if (in_array($surat['status'], ['APPROVED_L2', 'READY_DISPOSISI', 'COMPLETED'])) {
                    // 3. APPROVE L2 (Kabag TU)
                    $workflowModel->insert([
                        'surat_id' => $surat['id'],
                        'from_status' => 'APPROVED_L1',
                        'to_status' => 'APPROVED_L2',
                        'action_by' => $kabagTuUser['id'],
                        'action_type' => 'APPROVE',
                        'keterangan' => 'Disetujui oleh Kabag TU (rekonstruksi)',
                        'created_at' => date('Y-m-d H:i:s', $baseTime + 7200) // +2 hours
                    ]);
                    CLI::write("   ✅ Added APPROVE L2 workflow", 'green');
                    
                    // Update approval record
                    $approval2 = $approvalModel->where('surat_id', $surat['id'])->where('level', 2)->first();
                    if ($approval2) {
                        $approvalModel->update($approval2['id'], [
                            'status_approval' => 'approved',
                            'tanggal_approval' => date('Y-m-d H:i:s', $baseTime + 7200),
                            'catatan' => 'Disetujui (rekonstruksi)'
                        ]);
                    }
                }
                
                CLI::write("   🎉 Workflow history reconstructed successfully!", 'green');
            } else {
                $count = $workflows ? count($workflows) : 0;
                CLI::write("   ✅ Workflow history already exists ({$count} records)", 'green');
            }
        }

        CLI::write("\n=== FIX COMPLETE ===", 'green');
    }
}