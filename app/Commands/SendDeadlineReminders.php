<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\NotificationService;
use App\Models\NotificationModel;

class SendDeadlineReminders extends BaseCommand
{
    protected $group = 'notification';
    protected $name = 'notification:reminders';
    protected $description = 'Send deadline reminder notifications for overdue and upcoming deadline letters';

    public function run(array $params)
    {
        CLI::write('Starting deadline reminder process...', 'green');

        try {
            $notificationService = new NotificationService();
            
            CLI::write('Sending deadline reminders...', 'yellow');
            $notificationService->sendDeadlineReminders();
            
            CLI::write('Deadline reminders sent successfully!', 'green');
            
            // Optional: Clean up old notifications
            if (CLI::getOption('cleanup')) {
                CLI::write('Cleaning up old notifications...', 'yellow');
                
                $notificationModel = new NotificationModel();
                $days = (int) (CLI::getOption('days') ?? 30);
                $deleted = $notificationModel->deleteOldNotifications($days);
                
                CLI::write("Cleaned up {$deleted} old notifications (older than {$days} days)", 'green');
            }
            
        } catch (\Exception $e) {
            CLI::error('Error sending deadline reminders: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }
}