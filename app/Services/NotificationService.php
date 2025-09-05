<?php

namespace App\Services;

use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Models\SuratModel;
use CodeIgniter\Email\Email;

class NotificationService
{
    protected $notificationModel;
    protected $userModel;
    protected $suratModel;
    protected $email;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        $this->suratModel = new SuratModel();
        $this->email = \Config\Services::email();
    }

    public function notifyWorkflowAction($suratId, $action, $fromStatus, $toStatus, $actionBy, $keterangan = null)
    {
        $surat = $this->suratModel->find($suratId);
        if (!$surat) return false;

        $actionUser = $this->userModel->find($actionBy);
        if (!$actionUser) return false;

        $recipients = $this->getWorkflowRecipients($surat, $toStatus);
        
        foreach ($recipients as $recipient) {
            $title = $this->generateWorkflowTitle($action, $surat['nomor_surat']);
            $message = $this->generateWorkflowMessage($action, $surat, $actionUser, $keterangan);
            $actionUrl = base_url("surat/{$suratId}");
            
            $priority = $this->getWorkflowPriority($action, $surat['prioritas']);
            
            $metadata = [
                'action' => $action,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'action_by' => $actionBy,
                'surat_nomor' => $surat['nomor_surat']
            ];

            $this->notificationModel->createWorkflowNotification(
                $recipient['id'],
                $suratId,
                $title,
                $message,
                $actionUrl,
                $priority,
                $metadata
            );

            if ($this->shouldSendEmail($recipient, $priority)) {
                $this->sendEmailNotification($recipient, $title, $message, $actionUrl, $surat);
            }
        }

        $this->notifyOriginalSender($surat, $action, $toStatus, $actionUser, $keterangan);

        return true;
    }

    protected function getWorkflowRecipients($surat, $status)
    {
        $recipients = [];

        switch ($status) {
            case 'UNDER_REVIEW':
                $recipients = $this->userModel->getUsersByRole('staff_umum', true);
                break;
                
            case 'APPROVED_L1':
                $recipients = $this->userModel->getUsersByRole('kabag_tu', true);
                break;
                
            case 'APPROVED_L2':
                $recipients = $this->userModel->getUsersByDivisi($surat['divisi_id'], ['dekan', 'wakil_dekan']);
                break;
                
            case 'READY_DISPOSISI':
                $recipients = $this->userModel->getUsersByRole('sekretaris', true);
                break;
                
            case 'IN_PROCESS':
                $recipients = $this->getProcessorsByCategory($surat);
                break;
        }

        return $recipients;
    }

    protected function getProcessorsByCategory($surat)
    {
        switch ($surat['kategori']) {
            case 'AKADEMIK':
                return $this->userModel->getUsersByRole('admin_akademik', true);
            case 'KEUANGAN':
                return $this->userModel->getUsersByRole('admin_keuangan', true);
            case 'UMUM':
            default:
                return $this->userModel->getUsersByRole('staff_umum', true);
        }
    }

    protected function generateWorkflowTitle($action, $nomorSurat)
    {
        $actionTexts = [
            'SUBMIT' => 'Surat Baru Diajukan',
            'APPROVE' => 'Surat Telah Disetujui',
            'REJECT' => 'Surat Ditolak',
            'REVISE' => 'Surat Perlu Direvisi',
            'DISPOSE' => 'Surat Didisposisikan',
            'COMPLETE' => 'Surat Selesai Diproses'
        ];

        return ($actionTexts[$action] ?? 'Update Surat') . " - {$nomorSurat}";
    }

    protected function generateWorkflowMessage($action, $surat, $actionUser, $keterangan = null)
    {
        $actionTexts = [
            'SUBMIT' => 'telah mengajukan surat',
            'APPROVE' => 'telah menyetujui surat',
            'REJECT' => 'telah menolak surat',
            'REVISE' => 'meminta revisi untuk surat',
            'DISPOSE' => 'telah mendisposisikan surat',
            'COMPLETE' => 'telah menyelesaikan pemrosesan surat'
        ];

        $message = "{$actionUser['nama']} {$actionTexts[$action]} \"{$surat['perihal']}\" ({$surat['nomor_surat']}).";
        
        if ($keterangan) {
            $message .= "\n\nKeterangan: {$keterangan}";
        }

        return $message;
    }

    protected function getWorkflowPriority($action, $suratPrioritas)
    {
        if (in_array($action, ['REJECT', 'REVISE'])) {
            return NotificationModel::PRIORITY_HIGH;
        }

        if ($suratPrioritas === 'SANGAT_PENTING') {
            return NotificationModel::PRIORITY_URGENT;
        } elseif ($suratPrioritas === 'PENTING') {
            return NotificationModel::PRIORITY_HIGH;
        }

        return NotificationModel::PRIORITY_NORMAL;
    }

    protected function notifyOriginalSender($surat, $action, $status, $actionUser, $keterangan = null)
    {
        $sender = $this->userModel->find($surat['created_by']);
        if (!$sender) return;

        $statusTexts = [
            'UNDER_REVIEW' => 'sedang ditinjau',
            'APPROVED_L1' => 'disetujui tahap 1',
            'APPROVED_L2' => 'disetujui tahap 2',
            'READY_DISPOSISI' => 'siap didisposisikan',
            'IN_PROCESS' => 'sedang diproses',
            'COMPLETED' => 'telah selesai',
            'REJECTED' => 'ditolak'
        ];

        $title = "Update Status Surat - {$surat['nomor_surat']}";
        $message = "Surat \"{$surat['perihal']}\" Anda {$statusTexts[$status]} oleh {$actionUser['nama']}.";
        
        if ($keterangan) {
            $message .= "\n\nKeterangan: {$keterangan}";
        }

        $actionUrl = base_url("surat/{$surat['id']}");
        $priority = $status === 'REJECTED' ? NotificationModel::PRIORITY_HIGH : NotificationModel::PRIORITY_NORMAL;

        $this->notificationModel->createWorkflowNotification(
            $sender['id'],
            $surat['id'],
            $title,
            $message,
            $actionUrl,
            $priority
        );

        if ($this->shouldSendEmail($sender, $priority)) {
            $this->sendEmailNotification($sender, $title, $message, $actionUrl, $surat);
        }
    }

    public function sendDeadlineReminders()
    {
        $suratModel = new SuratModel();
        $overdueLetters = $suratModel->getOverdueLetters();

        foreach ($overdueLetters as $surat) {
            $this->sendDeadlineNotification($surat);
        }

        $upcomingDeadlines = $suratModel->getUpcomingDeadlines(3);
        foreach ($upcomingDeadlines as $surat) {
            $this->sendUpcomingDeadlineNotification($surat);
        }
    }

    protected function sendDeadlineNotification($surat)
    {
        $recipients = $this->getCurrentProcessors($surat);
        
        foreach ($recipients as $recipient) {
            $title = "Surat Melewati Deadline - {$surat['nomor_surat']}";
            $message = "Surat \"{$surat['perihal']}\" telah melewati batas waktu pemrosesan. Harap segera ditindaklanjuti.";
            $actionUrl = base_url("surat/{$surat['id']}");

            $this->notificationModel->createNotification([
                'user_id' => $recipient['id'],
                'surat_id' => $surat['id'],
                'type' => NotificationModel::TYPE_DEADLINE,
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'priority' => NotificationModel::PRIORITY_URGENT
            ]);

            if ($this->shouldSendEmail($recipient, NotificationModel::PRIORITY_URGENT)) {
                $this->sendEmailNotification($recipient, $title, $message, $actionUrl, $surat);
            }
        }
    }

    protected function sendUpcomingDeadlineNotification($surat)
    {
        $recipients = $this->getCurrentProcessors($surat);
        $daysLeft = ceil((strtotime($surat['batas_waktu']) - time()) / (60 * 60 * 24));
        
        foreach ($recipients as $recipient) {
            $title = "Pengingat Deadline - {$surat['nomor_surat']}";
            $message = "Surat \"{$surat['perihal']}\" akan mencapai batas waktu dalam {$daysLeft} hari. Harap segera diproses.";
            $actionUrl = base_url("surat/{$surat['id']}");

            $this->notificationModel->createReminderNotification(
                $recipient['id'],
                $surat['id'],
                $title,
                $message,
                $actionUrl,
                ['days_left' => $daysLeft]
            );
        }
    }

    protected function getCurrentProcessors($surat)
    {
        switch ($surat['status']) {
            case 'UNDER_REVIEW':
                return $this->userModel->getUsersByRole('staff_umum', true);
            case 'APPROVED_L1':
                return $this->userModel->getUsersByRole('kabag_tu', true);
            case 'APPROVED_L2':
                return $this->userModel->getUsersByDivisi($surat['divisi_id'], ['dekan', 'wakil_dekan']);
            case 'READY_DISPOSISI':
                return $this->userModel->getUsersByRole('sekretaris', true);
            case 'IN_PROCESS':
                return $this->getProcessorsByCategory($surat);
            default:
                return [];
        }
    }

    protected function shouldSendEmail($user, $priority)
    {
        if (!isset($user['email']) || empty($user['email'])) {
            return false;
        }

        $emailSettings = $user['notification_settings'] ?? [];
        
        if (is_string($emailSettings)) {
            $emailSettings = json_decode($emailSettings, true) ?? [];
        }

        $enabledPriorities = $emailSettings['email_priorities'] ?? ['HIGH', 'URGENT'];
        
        return in_array($priority, $enabledPriorities);
    }

    protected function sendEmailNotification($recipient, $title, $message, $actionUrl, $surat = null)
    {
        try {
            $this->email->setFrom('no-reply@unjani.ac.id', 'Sistem Surat Menyurat UNJANI');
            $this->email->setTo($recipient['email']);
            $this->email->setSubject($title);
            
            $emailBody = $this->generateEmailTemplate($recipient, $title, $message, $actionUrl, $surat);
            $this->email->setMessage($emailBody);
            
            if ($this->email->send()) {
                log_message('info', "Email notification sent to: " . $recipient['email']);
                return true;
            } else {
                log_message('error', "Failed to send email to: " . $recipient['email'] . " - " . $this->email->printDebugger(['headers']));
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "Email notification error: " . $e->getMessage());
            return false;
        }
    }

    protected function generateEmailTemplate($recipient, $title, $message, $actionUrl, $surat = null)
    {
        $template = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
                .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                .surat-info { background: white; padding: 15px; border-left: 4px solid #667eea; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Sistem Surat Menyurat UNJANI</h2>
                </div>
                <div class="content">
                    <p>Yth. ' . htmlspecialchars($recipient['nama']) . ',</p>
                    
                    <h3>' . htmlspecialchars($title) . '</h3>
                    
                    <p>' . nl2br(htmlspecialchars($message)) . '</p>';
        
        if ($surat) {
            $template .= '
                    <div class="surat-info">
                        <h4>Informasi Surat:</h4>
                        <p><strong>Nomor:</strong> ' . htmlspecialchars($surat['nomor_surat'] ?? '-') . '</p>
                        <p><strong>Perihal:</strong> ' . htmlspecialchars($surat['perihal'] ?? '-') . '</p>
                        <p><strong>Prioritas:</strong> ' . htmlspecialchars($surat['prioritas'] ?? '-') . '</p>
                    </div>';
        }
        
        if ($actionUrl) {
            $template .= '
                    <div style="text-align: center; margin: 20px 0;">
                        <a href="' . htmlspecialchars($actionUrl) . '" class="btn">Lihat Detail Surat</a>
                    </div>';
        }
        
        $template .= '
                    <p>Terima kasih atas perhatian Anda.</p>
                </div>
                <div class="footer">
                    <p>Email ini dikirim otomatis oleh Sistem Surat Menyurat UNJANI.</p>
                    <p>Universitas Jenderal Ahmad Yani - Fakultas Management System</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $template;
    }

    public function createSystemNotification($userId, $title, $message, $actionUrl = null, $priority = 'NORMAL')
    {
        return $this->notificationModel->createSystemNotification($userId, $title, $message, $actionUrl, $priority);
    }

    public function notifyAllUsers($title, $message, $actionUrl = null, $priority = 'NORMAL', $excludeRoles = [])
    {
        $users = $this->userModel->getAllActiveUsers($excludeRoles);
        
        foreach ($users as $user) {
            $this->createSystemNotification($user['id'], $title, $message, $actionUrl, $priority);
            
            if ($this->shouldSendEmail($user, $priority)) {
                $this->sendEmailNotification($user, $title, $message, $actionUrl);
            }
        }
        
        return count($users);
    }
}