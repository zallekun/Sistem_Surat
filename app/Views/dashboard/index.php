<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/table-design.css') ?>">
    <?= $this->include('partials/notifications') ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Use proper sidebar with analytics menu -->
            <?= $this->include('partials/sidebar') ?>

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
                                <h3 class="mt-2 mb-0 text-primary"><?= $stats['total'] ?? 0 ?></h3>
                                <small class="text-muted">Total Surat</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-warning"><?= count($pending_approvals) ?></h3>
                                <small class="text-muted">Menunggu Approval</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-success"><?= $stats['approved'] ?? 0 ?></h3>
                                <small class="text-muted">Sudah Disetujui</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-danger"><?= $stats['rejected'] ?? 0 ?></h3>
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
                                <?php if (empty($user_activity)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-2 mb-0">Belum ada aktivitas</p>
                                    <small>Aktivitas surat akan muncul di sini</small>
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-sm">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Aktivitas</th>
                                                <th>Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($user_activity as $activity): ?>
                                            <tr>
                                                <td>
                                                    <i class="bi bi-check-circle text-success me-2"></i>
                                                    <?= ucfirst(str_replace('_', ' ', $activity['action_type'])) ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary"><?= $activity['count'] ?></span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
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
                                    <a href="<?= base_url('surat/create') ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-2"></i>Buat Surat Baru
                                    </a>
                                    <a href="<?= base_url('surat') ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-search me-2"></i>Cari Surat
                                    </a>
                                </div>
                                <?php else: ?>
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url('surat?status=SUBMITTED,UNDER_REVIEW,APPROVED_L1,APPROVED_L2') ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-clock me-2"></i>Review Surat
                                    </a>
                                    <a href="<?= base_url('surat') ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-list me-2"></i>Lihat Semua Surat
                                    </a>
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