<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title', true) ?> - Sistem Surat Menyurat UNJANI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
    <?= $this->renderSection('head') ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar-custom position-fixed h-100">
                <?= view('partials/sidebar') ?>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="pt-3 pb-2 mb-3">
                    <?= $this->renderSection('content') ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Notification checking script -->
    <script>
    function checkNotifications() {
        fetch('<?= base_url('notifications/recent') ?>', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const badge = document.getElementById('notificationCount');
                if (badge) {
                    if (data.data.unread_count > 0) {
                        badge.textContent = data.data.unread_count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error checking notifications:', error);
        });
    }

    // Check notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkNotifications();
        
        // Check every 30 seconds
        setInterval(checkNotifications, 30000);
    });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>