<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.2);
        }
        .main-content {
            padding: 20px;
        }
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            border: none;
        }
        .stats-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .role-badge {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar px-0">
                <div class="p-3">
                    <div class="text-center mb-4">
                        <i class="bi bi-envelope-paper-fill" style="font-size: 2rem; color: white;"></i>
                        <h5 class="text-white mt-2 mb-0">Sistem Surat</h5>
                        <small class="text-white-50">Fakultas Universitas</small>
                    </div>
                    
                    <div class="mb-3 text-center">
                        <div class="role-badge">
                            <?= ucwords(str_replace('_', ' ', $user_role)) ?>
                        </div>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('dashboard') ?>">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <?php if (in_array($user_role, ['admin_prodi'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#suratMenu">
                                <i class="bi bi-envelope me-2"></i>
                                Manajemen Surat
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="suratMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <i class="bi bi-plus-circle me-2"></i>Buat Surat
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <i class="bi bi-list-ul me-2"></i>Daftar Surat
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <?php endif; ?>

                        <?php if (in_array($user_role, ['staff_umum', 'kabag_tu', 'dekan', 'wd_akademik', 'wd_kemahasiswa', 'wd_umum'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#approvalMenu">
                                <i class="bi bi-check2-square me-2"></i>
                                Approval
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="approvalMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <i class="bi bi-clock me-2"></i>Menunggu Review
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <i class="bi bi-check-circle me-2"></i>Sudah Disetujui
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('profile') ?>">
                                <i class="bi bi-person me-2"></i>
                                Profile
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3">
                            <a class="nav-link text-warning" href="<?= base_url('logout') ?>">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 main-content">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Welcome Card -->
                <div class="card welcome-card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-1">Selamat Datang, <?= $user_name ?>!</h4>
                                <p class="mb-0">Role: <?= ucwords(str_replace('_', ' ', $user_role)) ?></p>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-person-circle" style="font-size: 3rem; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-envelope text-primary" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-primary">0</h3>
                                <small class="text-muted">Total Surat</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-warning">0</h3>
                                <small class="text-muted">Menunggu Approval</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-success">0</h3>
                                <small class="text-muted">Sudah Disetujui</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-danger">0</h3>
                                <small class="text-muted">Ditolak</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-list me-2"></i>Aktivitas Terkini</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-2 mb-0">Belum ada aktivitas</p>
                                    <small>Aktivitas surat akan muncul di sini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Aksi Cepat</h6>
                            </div>
                            <div class="card-body">
                                <?php if ($user_role === 'admin_prodi'): ?>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-2"></i>Buat Surat Baru
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-search me-2"></i>Cari Surat
                                    </button>
                                </div>
                                <?php else: ?>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-warning btn-sm">
                                        <i class="bi bi-clock me-2"></i>Review Surat
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-graph-up me-2"></i>Lihat Laporan
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>