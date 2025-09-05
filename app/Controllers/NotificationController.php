<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Services\NotificationService;

class NotificationController extends BaseController
{
    protected $notificationModel;
    protected $notificationService;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->notificationService = new NotificationService();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $page = (int) ($this->request->getVar('page') ?? 1);
        $type = $this->request->getVar('type');
        $limit = 20;

        if ($type && in_array($type, ['WORKFLOW', 'SYSTEM', 'REMINDER', 'DEADLINE'])) {
            $notifications = $this->notificationModel->getNotificationsByType($userId, $type, $limit);
        } else {
            $notifications = $this->notificationModel->getRecentNotifications($userId, $limit);
        }

        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        $data = [
            'title' => 'Notifikasi',
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'currentType' => $type,
            'user' => session()->get()
        ];

        return view('notifications/index', $data);
    }

    public function getRecent()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $userId = session()->get('user_id');
        $limit = (int) ($this->request->getVar('limit') ?? 5);

        $notifications = $this->notificationModel->getRecentNotifications($userId, $limit);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]
        ]);
    }

    public function markAsRead($id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $userId = session()->get('user_id');

        if ($id) {
            $result = $this->notificationModel->markAsRead($id, $userId);
            $message = 'Notifikasi berhasil ditandai sebagai telah dibaca';
        } else {
            $result = $this->notificationModel->markAllAsRead($userId);
            $message = 'Semua notifikasi berhasil ditandai sebagai telah dibaca';
        }

        if ($result) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $message,
                    'unread_count' => $this->notificationModel->getUnreadCount($userId)
                ]);
            } else {
                session()->setFlashdata('success', $message);
                return redirect()->to('/notifications');
            }
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Gagal memperbarui notifikasi'])->setStatusCode(500);
            } else {
                session()->setFlashdata('error', 'Gagal memperbarui notifikasi');
                return redirect()->to('/notifications');
            }
        }
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $userId = session()->get('user_id');
        
        $notification = $this->notificationModel->where('id', $id)
                                                ->where('user_id', $userId)
                                                ->first();

        if (!$notification) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Notifikasi tidak ditemukan'])->setStatusCode(404);
            } else {
                session()->setFlashdata('error', 'Notifikasi tidak ditemukan');
                return redirect()->to('/notifications');
            }
        }

        if ($this->notificationModel->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Notifikasi berhasil dihapus'
                ]);
            } else {
                session()->setFlashdata('success', 'Notifikasi berhasil dihapus');
                return redirect()->to('/notifications');
            }
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Gagal menghapus notifikasi'])->setStatusCode(500);
            } else {
                session()->setFlashdata('error', 'Gagal menghapus notifikasi');
                return redirect()->to('/notifications');
            }
        }
    }

    public function settings()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        $notificationSettings = [];
        if (isset($user['notification_settings'])) {
            $notificationSettings = is_string($user['notification_settings']) 
                ? json_decode($user['notification_settings'], true) 
                : $user['notification_settings'];
        }

        $defaultSettings = [
            'email_enabled' => true,
            'email_priorities' => ['HIGH', 'URGENT'],
            'workflow_notifications' => true,
            'system_notifications' => true,
            'reminder_notifications' => true,
            'deadline_notifications' => true
        ];

        $settings = array_merge($defaultSettings, $notificationSettings);

        $data = [
            'title' => 'Pengaturan Notifikasi',
            'user' => session()->get(),
            'settings' => $settings
        ];

        return view('notifications/settings', $data);
    }

    public function updateSettings()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $userModel = new \App\Models\UserModel();

        $settings = [
            'email_enabled' => (bool) $this->request->getPost('email_enabled'),
            'email_priorities' => $this->request->getPost('email_priorities') ?? [],
            'workflow_notifications' => (bool) $this->request->getPost('workflow_notifications'),
            'system_notifications' => (bool) $this->request->getPost('system_notifications'),
            'reminder_notifications' => (bool) $this->request->getPost('reminder_notifications'),
            'deadline_notifications' => (bool) $this->request->getPost('deadline_notifications')
        ];

        if ($userModel->update($userId, ['notification_settings' => json_encode($settings)])) {
            session()->setFlashdata('success', 'Pengaturan notifikasi berhasil disimpan');
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan pengaturan notifikasi');
        }

        return redirect()->to('/notifications/settings');
    }

    public function testNotification()
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'dekan'])) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $userId = session()->get('user_id');
        
        $this->notificationService->createSystemNotification(
            $userId,
            'Test Notifikasi',
            'Ini adalah test notifikasi sistem. Jika Anda melihat pesan ini, sistem notifikasi berfungsi dengan baik.',
            base_url('notifications'),
            'NORMAL'
        );

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Test notifikasi berhasil dikirim'
            ]);
        } else {
            session()->setFlashdata('success', 'Test notifikasi berhasil dikirim');
            return redirect()->to('/notifications');
        }
    }
}