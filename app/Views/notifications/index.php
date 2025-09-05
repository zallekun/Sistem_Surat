<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">Notifikasi</h3>
                    <p class="text-muted">Kelola semua notifikasi Anda</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('notifications/settings') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                    <?php if (in_array($user['role'], ['admin', 'dekan'])): ?>
                        <button class="btn btn-outline-primary" onclick="testNotification()">
                            <i class="bi bi-bell"></i> Test Notifikasi
                        </button>
                    <?php endif; ?>
                    <?php if ($unreadCount > 0): ?>
                        <button class="btn btn-primary" onclick="markAllAsRead()">
                            <i class="bi bi-check-all"></i> Tandai Semua Dibaca
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 text-center">
                            <div class="p-3">
                                <i class="bi bi-bell-fill text-primary display-6"></i>
                                <h4 class="mt-2"><?= $unreadCount ?></h4>
                                <p class="text-muted mb-0">Belum Dibaca</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="p-3">
                                <i class="bi bi-envelope-fill text-success display-6"></i>
                                <h4 class="mt-2"><?= count(array_filter($notifications, fn($n) => $n['type'] === 'WORKFLOW')) ?></h4>
                                <p class="text-muted mb-0">Workflow</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="p-3">
                                <i class="bi bi-exclamation-triangle-fill text-warning display-6"></i>
                                <h4 class="mt-2"><?= count(array_filter($notifications, fn($n) => $n['type'] === 'DEADLINE')) ?></h4>
                                <p class="text-muted mb-0">Deadline</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="p-3">
                                <i class="bi bi-info-circle-fill text-info display-6"></i>
                                <h4 class="mt-2"><?= count(array_filter($notifications, fn($n) => $n['type'] === 'SYSTEM')) ?></h4>
                                <p class="text-muted mb-0">Sistem</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="<?= base_url('notifications') ?>" 
                   class="btn <?= !$currentType ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Semua
                </a>
                <a href="<?= base_url('notifications?type=WORKFLOW') ?>" 
                   class="btn <?= $currentType === 'WORKFLOW' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Workflow
                </a>
                <a href="<?= base_url('notifications?type=DEADLINE') ?>" 
                   class="btn <?= $currentType === 'DEADLINE' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Deadline
                </a>
                <a href="<?= base_url('notifications?type=SYSTEM') ?>" 
                   class="btn <?= $currentType === 'SYSTEM' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Sistem
                </a>
                <a href="<?= base_url('notifications?type=REMINDER') ?>" 
                   class="btn <?= $currentType === 'REMINDER' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    Reminder
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?php if (empty($notifications)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Tidak ada notifikasi</h4>
                    <p class="text-muted">Anda akan menerima notifikasi di sini ketika ada aktivitas baru.</p>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="list-group-item <?= $notification['is_read'] ? '' : 'list-group-item-light border-start border-primary border-3' ?>" 
                             id="notification-<?= $notification['id'] ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3">
                                            <?php
                                            $iconClass = match($notification['type']) {
                                                'WORKFLOW' => 'bi-arrow-right-circle-fill text-primary',
                                                'DEADLINE' => 'bi-exclamation-triangle-fill text-danger',
                                                'REMINDER' => 'bi-clock-fill text-warning',
                                                'SYSTEM' => 'bi-info-circle-fill text-info',
                                                default => 'bi-bell-fill text-secondary'
                                            };
                                            ?>
                                            <i class="bi <?= $iconClass ?> fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 <?= $notification['is_read'] ? 'text-muted' : 'fw-bold' ?>">
                                                <?= esc($notification['title']) ?>
                                            </h6>
                                            <div class="d-flex align-items-center gap-3 small text-muted">
                                                <span>
                                                    <i class="bi bi-clock"></i>
                                                    <?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?>
                                                </span>
                                                <?php if ($notification['surat_id']): ?>
                                                    <span>
                                                        <i class="bi bi-file-earmark-text"></i>
                                                        <?= esc($notification['nomor_surat'] ?? 'Surat #' . $notification['surat_id']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="badge bg-<?= 
                                                    match($notification['priority']) {
                                                        'URGENT' => 'danger',
                                                        'HIGH' => 'warning text-dark',
                                                        'LOW' => 'secondary',
                                                        default => 'primary'
                                                    }
                                                ?>">
                                                    <?= ucfirst(strtolower($notification['priority'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-2 <?= $notification['is_read'] ? 'text-muted' : '' ?>">
                                        <?= nl2br(esc($notification['message'])) ?>
                                    </p>
                                    <?php if ($notification['action_url']): ?>
                                        <a href="<?= esc($notification['action_url']) ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           onclick="markAsRead(<?= $notification['id'] ?>)">
                                            <i class="bi bi-eye"></i> Lihat Detail
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-ghost" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php if (!$notification['is_read']): ?>
                                            <li>
                                                <button class="dropdown-item" onclick="markAsRead(<?= $notification['id'] ?>)">
                                                    <i class="bi bi-check"></i> Tandai Dibaca
                                                </button>
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <button class="dropdown-item text-danger" onclick="deleteNotification(<?= $notification['id'] ?>)">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`<?= base_url('notifications/mark-read') ?>/${notificationId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const notification = document.getElementById(`notification-${notificationId}`);
            notification.classList.remove('list-group-item-light', 'border-start', 'border-primary', 'border-3');
            updateUnreadCount();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Gagal memperbarui notifikasi', 'error');
    });
}

function markAllAsRead() {
    fetch('<?= base_url('notifications/mark-read') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Gagal memperbarui notifikasi', 'error');
    });
}

async function deleteNotification(notificationId) {
    const confirmed = await SuratNotification.confirmDelete(
        'Hapus Notifikasi?',
        'Notifikasi yang dihapus tidak dapat dikembalikan!'
    );
    
    if (confirmed) {
        fetch(`<?= base_url('notifications') ?>/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById(`notification-${notificationId}`).remove();
                showAlert(data.message, 'success');
            } else {
                showAlert(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Gagal menghapus notifikasi', 'error');
        });
    }
}

function testNotification() {
    fetch('<?= base_url('notifications/test') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Gagal mengirim test notifikasi', 'error');
    });
}

function updateUnreadCount() {
    fetch('<?= base_url('notifications/recent') ?>', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const badge = document.querySelector('.notification-count');
            if (badge) {
                if (data.data.unread_count > 0) {
                    badge.textContent = data.data.unread_count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
<?= $this->endSection() ?>