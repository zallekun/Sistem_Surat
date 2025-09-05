<nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <i class="bi bi-envelope-paper-fill text-white" style="font-size: 2rem;"></i>
            <h5 class="text-white mt-2 mb-0">Sistem Surat</h5>
            <small class="text-muted">Fakultas Universitas</small>
        </div>
        
        <div class="mb-3 text-center">
            <div class="badge bg-primary">
                <?= ucwords(str_replace('_', ' ', session()->get('user_role'))) ?>
            </div>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?= uri_string() === 'dashboard' ? 'active bg-primary' : '' ?>" 
                   href="<?= base_url('dashboard') ?>">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?= uri_string() === 'notifications' ? 'active bg-primary' : '' ?>" 
                   href="<?= base_url('notifications') ?>">
                    <i class="bi bi-bell me-2"></i>
                    Notifikasi
                    <span class="badge bg-danger ms-auto notification-count" id="notificationCount" style="display: none;">0</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?= uri_string() === 'search' ? 'active bg-primary' : '' ?>" 
                   href="<?= base_url('search') ?>">
                    <i class="bi bi-search me-2"></i>
                    Advanced Search
                </a>
            </li>
            
            <?php $userRole = session()->get('user_role'); ?>
            
            <?php if (in_array($userRole, ['admin_prodi'])): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-bs-toggle="collapse" data-bs-target="#suratMenu">
                    <i class="bi bi-envelope me-2"></i>
                    Manajemen Surat
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse <?= strpos(uri_string(), 'surat') !== false ? 'show' : '' ?>" id="suratMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link text-light <?= uri_string() === 'surat/create' ? 'active' : '' ?>" 
                               href="<?= base_url('surat/create') ?>">
                                <i class="bi bi-plus-circle me-2"></i>Buat Surat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light <?= uri_string() === 'surat' ? 'active' : '' ?>" 
                               href="<?= base_url('surat') ?>">
                                <i class="bi bi-list-ul me-2"></i>Daftar Surat
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <?php if (in_array($userRole, ['staff_umum', 'kabag_tu', 'dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum', 'kaur_keuangan'])): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-bs-toggle="collapse" data-bs-target="#approvalMenu">
                    <i class="bi bi-check2-square me-2"></i>
                    Approval
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="approvalMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link text-light" href="<?= base_url('approval/pending') ?>">
                                <i class="bi bi-clock me-2"></i>Menunggu Review
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="<?= base_url('approval/completed') ?>">
                                <i class="bi bi-check-circle me-2"></i>Sudah Disetujui
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <?php if (in_array($userRole, ['dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum', 'kabag_tu', 'admin_prodi'])): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-bs-toggle="collapse" data-bs-target="#analyticsMenu">
                    <i class="bi bi-graph-up me-2"></i>
                    Analytics
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse <?= strpos(uri_string(), 'analytics') !== false ? 'show' : '' ?>" id="analyticsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link text-light <?= uri_string() === 'analytics' ? 'active' : '' ?>" 
                               href="<?= base_url('analytics') ?>">
                                <i class="bi bi-speedometer me-2"></i>Executive Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light <?= uri_string() === 'analytics/reports' ? 'active' : '' ?>" 
                               href="<?= base_url('analytics/reports') ?>">
                                <i class="bi bi-file-text me-2"></i>Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link text-white <?= uri_string() === 'profile' ? 'active bg-primary' : '' ?>" 
                   href="<?= base_url('profile') ?>">
                    <i class="bi bi-person me-2"></i>
                    Profile
                </a>
            </li>
            
            <li class="nav-item mt-3">
                <a class="nav-link text-warning" href="#" onclick="confirmLogout(event)">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    min-height: 100vh;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.sidebar .nav-link {
    color: rgba(255,255,255,0.8) !important;
    border-radius: 8px;
    margin: 2px 0;
    transition: all 0.3s;
}

.sidebar .nav-link:hover {
    color: white !important;
    background-color: rgba(255,255,255,0.1) !important;
}

.sidebar .nav-link.active {
    color: white !important;
    background-color: rgba(255,255,255,0.2) !important;
}
</style>