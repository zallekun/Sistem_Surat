<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SuratModel;
use App\Models\SuratWorkflowModel;
use App\Models\ApprovalModel;

class DebugWorkflow extends BaseCommand
{
    protected $group = 'debug';
    protected $name = 'debug:workflow';
    protected $description = 'Debug workflow history for specific surat';

    public function run(array $params)
    {
        $suratModel = new SuratModel();
        $workflowModel = new SuratWorkflowModel();
        $approvalModel = new ApprovalModel();

        CLI::write('=== DEBUG WORKFLOW HISTORY ===', 'green');

        // Get all surat with status beyond SUBMITTED
        $advancedSurat = $suratModel->whereIn('status', ['APPROVED_L1', 'APPROVED_L2', 'READY_DISPOSISI', 'COMPLETED'])
                                   ->findAll();

        if (empty($advancedSurat)) {
            CLI::write('❌ No advanced surat found', 'red');
            return;
        }

        foreach ($advancedSurat as $surat) {
            CLI::write("\n📋 SURAT: {$surat['nomor_surat']} (ID: {$surat['id']})", 'yellow');
            CLI::write("   Status: {$surat['status']}", 'white');
            CLI::write("   Created by: {$surat['created_by']}, Current holder: {$surat['current_holder']}", 'white');

            // Get workflow history
            $workflows = $workflowModel->getWorkflowHistory($surat['id']);
            $workflowCount = $workflows ? count($workflows) : 0;
            CLI::write("   📈 Workflow History ({$workflowCount} records):", 'yellow');
            
            if (empty($workflows)) {
                CLI::write("     ❌ No workflow history found!", 'red');
            } else {
                foreach ($workflows as $w) {
                    $time = date('d M Y H:i', strtotime($w['created_at']));
                    CLI::write("     - {$w['action_type']}: {$w['from_status']} → {$w['to_status']} by {$w['action_by_name']} ({$time})", 'white');
                    if ($w['keterangan']) {
                        CLI::write("       Catatan: {$w['keterangan']}", 'white');
                    }
                }
            }

            // Get approval history
            $approvals = $approvalModel->getApprovalChain($surat['id']);
            $approvalCount = $approvals ? count($approvals) : 0;
            CLI::write("   ✅ Approval Chain ({$approvalCount} records):", 'yellow');
            
            if (empty($approvals)) {
                CLI::write("     ❌ No approval chain found!", 'red');
            } else {
                foreach ($approvals as $a) {
                    $status = isset($a['status_approval']) ? $a['status_approval'] : 'pending';
                    $time = isset($a['tanggal_approval']) && $a['tanggal_approval'] ? date('d M Y H:i', strtotime($a['tanggal_approval'])) : 'Pending';
                    CLI::write("     - Level {$a['level']}: {$a['approver_name']} ({$a['role']}) - Status: {$status} ({$time})", 'white');
                    if ($a['catatan']) {
                        CLI::write("       Catatan: {$a['catatan']}", 'white');
                    }
                    if ($a['alasan_reject']) {
                        CLI::write("       Alasan Reject: {$a['alasan_reject']}", 'red');
                    }
                }
            }
        }

        CLI::write("\n=== DEBUG COMPLETE ===", 'green');
    }
}