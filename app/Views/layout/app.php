<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Basic Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="Sistem Surat Menyurat UNJANI - Electronic Letter Management System">
    <meta name="keywords" content="UNJANI, surat, management, universitas">
    <meta name="author" content="Universitas Jenderal Ahmad Yani">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#667eea">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="UNJANI Surat">
    <meta name="msapplication-TileColor" content="#667eea">
    <meta name="msapplication-navbutton-color" content="#667eea">
    
    <!-- iOS Safari Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192x192.png') ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= base_url('assets/icons/icon-152x152.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/icons/icon-192x192.png') ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/icons/icon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/icons/icon-16x16.png') ?>">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    
    <title><?= $title ?? 'UNJANI Surat' ?></title>
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/mobile.css') ?>">
    
    <!-- Additional CSS -->
    <?= $this->renderSection('styles') ?>
    
    <!-- Prevent zoom on input focus (iOS) -->
    <style>
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            select, textarea, input[type="text"], input[type="password"], 
            input[type="datetime"], input[type="datetime-local"], 
            input[type="date"], input[type="month"], input[type="time"], 
            input[type="week"], input[type="number"], input[type="email"], 
            input[type="url"], input[type="search"], input[type="tel"] {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body class="safe-area-top safe-area-bottom">
    <!-- Mobile Header (only on mobile) -->
    <div class="mobile-header mobile-only">
        <button class="mobile-menu-btn" onclick="toggleMobileSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <h6 class="mb-0">UNJANI Surat</h6>
        <div class="d-flex">
            <button class="btn btn-sm text-white" onclick="showConnectionStatus()">
                <i class="bi bi-wifi" id="connectionIcon"></i>
            </button>
        </div>
    </div>

    <!-- Main App Container -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?= $this->include('partials/sidebar') ?>

            <!-- Sidebar Overlay (mobile) -->
            <div class="sidebar-overlay" onclick="closeMobileSidebar()"></div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content px-md-4">
                <!-- Content will be rendered here -->
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- Bottom Navigation (mobile only) -->
    <div class="bottom-nav mobile-only safe-bottom">
        <?php $currentPath = uri_string(); ?>
        <a href="<?= base_url('dashboard') ?>" class="bottom-nav-item <?= $currentPath === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Home</span>
        </a>
        <a href="<?= base_url('search') ?>" class="bottom-nav-item <?= strpos($currentPath, 'search') !== false ? 'active' : '' ?>">
            <i class="bi bi-search"></i>
            <span>Search</span>
        </a>
        <a href="<?= base_url('surat') ?>" class="bottom-nav-item <?= strpos($currentPath, 'surat') !== false ? 'active' : '' ?>">
            <i class="bi bi-envelope"></i>
            <span>Surat</span>
        </a>
        <?php if (in_array(session()->get('user_role'), ['dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum', 'kabag_tu', 'admin_prodi'])): ?>
        <a href="<?= base_url('analytics') ?>" class="bottom-nav-item <?= strpos($currentPath, 'analytics') !== false ? 'active' : '' ?>">
            <i class="bi bi-graph-up"></i>
            <span>Analytics</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- Floating Action Button -->
    <?php if (session()->get('user_role') === 'admin_prodi'): ?>
    <button class="fab mobile-only" onclick="window.location.href='<?= base_url('surat/create') ?>'" title="Create New Surat">
        <i class="bi bi-plus"></i>
    </button>
    <?php endif; ?>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Additional Scripts -->
    <?= $this->renderSection('scripts') ?>
    
    <!-- PWA Script -->
    <script src="<?= base_url('assets/js/pwa.js') ?>"></script>
    
    <script>
        // Mobile Navigation Functions
        function toggleMobileSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (sidebar && overlay) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
            }
        }

        function closeMobileSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (sidebar && overlay) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        // Connection Status
        function showConnectionStatus() {
            const isOnline = navigator.onLine;
            const message = isOnline ? '✅ Connected to internet' : '📡 Offline mode';
            const type = isOnline ? 'success' : 'warning';
            
            if (window.pwaManager) {
                window.pwaManager.showToast(message, type);
            }
        }

        function updateConnectionIcon() {
            const icon = document.getElementById('connectionIcon');
            if (icon) {
                icon.className = navigator.onLine ? 'bi bi-wifi' : 'bi bi-wifi-off';
            }
        }

        // Update connection icon on network changes
        window.addEventListener('online', updateConnectionIcon);
        window.addEventListener('offline', updateConnectionIcon);
        
        // Initial connection icon update
        document.addEventListener('DOMContentLoaded', updateConnectionIcon);

        // Prevent zoom on double tap (iOS Safari)
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // Handle back button on mobile
        window.addEventListener('popstate', function(event) {
            closeMobileSidebar();
        });

        // Close sidebar when clicking on main content (mobile)
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuBtn.contains(e.target) && 
                sidebar.classList.contains('show')) {
                closeMobileSidebar();
            }
        });

        // Pull to refresh (mobile)
        let startY = 0;
        let currentY = 0;
        let pulling = false;

        if ('ontouchstart' in window) {
            const mainContent = document.querySelector('.main-content');
            
            mainContent.addEventListener('touchstart', function(e) {
                if (mainContent.scrollTop === 0) {
                    startY = e.touches[0].pageY;
                    currentY = startY;
                }
            });

            mainContent.addEventListener('touchmove', function(e) {
                if (mainContent.scrollTop === 0) {
                    currentY = e.touches[0].pageY;
                    if (currentY > startY + 50 && !pulling) {
                        pulling = true;
                        // Add visual feedback here
                    }
                }
            });

            mainContent.addEventListener('touchend', function(e) {
                if (pulling && currentY > startY + 100) {
                    // Trigger refresh
                    window.location.reload();
                }
                pulling = false;
                startY = 0;
                currentY = 0;
            });
        }

        // Keyboard handling for mobile
        if (window.innerWidth <= 768) {
            const viewport = document.querySelector('meta[name=viewport]');
            
            window.addEventListener('focusin', function() {
                viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, user-scalable=no');
            });
            
            window.addEventListener('focusout', function() {
                viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, user-scalable=no');
            });
        }
    </script>
</body>
</html>