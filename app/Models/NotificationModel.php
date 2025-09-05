<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'surat_id', 'type', 'title', 'message', 
        'action_url', 'is_read', 'is_email_sent', 'priority', 
        'metadata', 'read_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'type' => 'required|in_list[WORKFLOW,SYSTEM,REMINDER,DEADLINE]',
        'title' => 'required|max_length[255]',
        'message' => 'required',
        'priority' => 'in_list[LOW,NORMAL,HIGH,URGENT]'
    ];

    const TYPE_WORKFLOW = 'WORKFLOW';
    const TYPE_SYSTEM = 'SYSTEM';
    const TYPE_REMINDER = 'REMINDER';
    const TYPE_DEADLINE = 'DEADLINE';

    const PRIORITY_LOW = 'LOW';
    const PRIORITY_NORMAL = 'NORMAL';
    const PRIORITY_HIGH = 'HIGH';
    const PRIORITY_URGENT = 'URGENT';

    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', false)
                    ->countAllResults();
    }

    public function getRecentNotifications($userId, $limit = 10)
    {
        return $this->select('notifications.*, surat.nomor_surat, surat.perihal')
                    ->join('surat', 'surat.id = notifications.surat_id', 'left')
                    ->where('notifications.user_id', $userId)
                    ->orderBy('notifications.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function markAsRead($notificationId, $userId = null)
    {
        $data = [
            'is_read' => true,
            'read_at' => date('Y-m-d H:i:s')
        ];

        $builder = $this->where('id', $notificationId);
        
        if ($userId) {
            $builder->where('user_id', $userId);
        }

        return $builder->update($data);
    }

    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => date('Y-m-d H:i:s')
                    ]);
    }

    public function createNotification($data)
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data);
    }

    public function createWorkflowNotification($userId, $suratId, $title, $message, $actionUrl = null, $priority = 'NORMAL', $metadata = null)
    {
        return $this->createNotification([
            'user_id' => $userId,
            'surat_id' => $suratId,
            'type' => self::TYPE_WORKFLOW,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'priority' => $priority,
            'metadata' => $metadata ? json_encode($metadata) : null
        ]);
    }

    public function createSystemNotification($userId, $title, $message, $actionUrl = null, $priority = 'NORMAL')
    {
        return $this->createNotification([
            'user_id' => $userId,
            'type' => self::TYPE_SYSTEM,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'priority' => $priority
        ]);
    }

    public function createReminderNotification($userId, $suratId, $title, $message, $actionUrl = null, $metadata = null)
    {
        return $this->createNotification([
            'user_id' => $userId,
            'surat_id' => $suratId,
            'type' => self::TYPE_REMINDER,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'priority' => self::PRIORITY_HIGH,
            'metadata' => $metadata ? json_encode($metadata) : null
        ]);
    }

    public function getNotificationsByType($userId, $type, $limit = 50)
    {
        return $this->select('notifications.*, surat.nomor_surat, surat.perihal')
                    ->join('surat', 'surat.id = notifications.surat_id', 'left')
                    ->where('notifications.user_id', $userId)
                    ->where('notifications.type', $type)
                    ->orderBy('notifications.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function deleteOldNotifications($days = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('created_at <', $cutoffDate)
                    ->where('is_read', true)
                    ->delete();
    }
}