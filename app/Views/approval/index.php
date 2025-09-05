<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/mobile.css') ?>">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?= $this->include('partials/sidebar') ?>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 main-content px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="bi <?= $page_type === 'pending' ? 'bi-clock' : 'bi-check-circle' ?> me-2 text-primary"></i>
                        <?= $page_type === 'pending' ? 'Pending Approvals' : 'Completed Approvals' ?>
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="<?= base_url('approval/pending') ?>" 
                               class="btn btn-sm <?= $page_type === 'pending' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                <i class="bi bi-clock me-1"></i>Pending
                            </a>
                            <a href="<?= base_url('approval/completed') ?>" 
                               class="btn btn-sm <?= $page_type === 'completed' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                <i class="bi bi-check-circle me-1"></i>Completed
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card border-0 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-white text-center">
                                <i class="bi bi-clock" style="font-size: 2rem; opacity: 0.8;"></i>
                                <h3 class="mt-2 mb-0"><?= $page_type === 'pending' ? $stats['pending'] : $stats['completed'] ?></h3>
                                <small class="opacity-75"><?= $page_type === 'pending' ? 'Pending' : 'Completed' ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card border-0 h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="card-body text-white text-center">
                                <i class="bi bi-calendar-day" style="font-size: 2rem; opacity: 0.8;"></i>
                                <h3 class="mt-2 mb-0"><?= $stats['today'] ?></h3>
                                <small class="opacity-75">Today</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card border-0 h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="card-body text-white text-center">
                                <i class="bi bi-calendar-week" style="font-size: 2rem; opacity: 0.8;"></i>
                                <h3 class="mt-2 mb-0"><?= $stats['this_week'] ?></h3>
                                <small class="opacity-75">This Week</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card border-0 h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <div class="card-body text-white text-center">
                                <i class="bi bi-check-all" style="font-size: 2rem; opacity: 0.8;"></i>
                                <h3 class="mt-2 mb-0"><?= $stats['total_approved'] ?></h3>
                                <small class="opacity-75">Total Approved</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Surat List -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="bi <?= $page_type === 'pending' ? 'bi-list-ul' : 'bi-check2-all' ?> me-2"></i>
                            <?= $page_type === 'pending' ? 'Surat Requiring Your Action' : 'Completed Surat' ?>
                            <?php if (count($surat) > 0): ?>
                            <span class="badge bg-primary ms-2"><?= count($surat) ?></span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($surat)): ?>
                            <div class="text-center py-5">
                                <i class="bi <?= $page_type === 'pending' ? 'bi-inbox' : 'bi-check-circle' ?>" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="text-muted mt-3">
                                    <?= $page_type === 'pending' ? 'No pending approvals' : 'No completed surat' ?>
                                </h4>
                                <p class="text-muted">
                                    <?= $page_type === 'pending' ? 
                                        'All caught up! No surat requires your attention at the moment.' : 
                                        'No surat has been completed through your approval yet.' ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <!-- Desktop Table -->
                            <div class="table-responsive desktop-only">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nomor Surat</th>
                                            <th>Perihal</th>
                                            <th>Category</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($surat as $s): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($s['nomor_surat']) ?></strong>
                                                <?php if (isset($s['nama_prodi'])): ?>
                                                    <br><small class="text-muted"><?= esc($s['nama_prodi']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-medium"><?= esc(substr($s['perihal'], 0, 50)) ?><?= strlen($s['perihal']) > 50 ? '...' : '' ?></div>
                                                <?php if (isset($s['created_by_name'])): ?>
                                                    <small class="text-muted">by <?= esc($s['created_by_name']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= ucfirst($s['kategori']) ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $priorityClasses = [
                                                    'normal' => 'bg-secondary',
                                                    'urgent' => 'bg-warning text-dark',
                                                    'sangat_urgent' => 'bg-danger'
                                                ];
                                                ?>
                                                <span class="badge <?= $priorityClasses[$s['prioritas']] ?? 'bg-secondary' ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $s['prioritas'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                $statusClasses = [
                                                    'SUBMITTED' => 'bg-primary',
                                                    'UNDER_REVIEW' => 'bg-info',
                                                    'APPROVED_L1' => 'bg-success',
                                                    'APPROVED_L2' => 'bg-success',
                                                    'READY_DISPOSISI' => 'bg-success',
                                                    'IN_PROCESS' => 'bg-warning',
                                                    'COMPLETED' => 'bg-success'
                                                ];
                                                ?>
                                                <span class="badge <?= $statusClasses[$s['status']] ?? 'bg-secondary' ?>">
                                                    <?= str_replace('_', ' ', $s['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    <?= date('d/m/Y', strtotime($s['created_at'])) ?><br>
                                                    <?= date('H:i', strtotime($s['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('surat/' . $s['id']) ?>" 
                                                       class="btn btn-outline-primary" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <?php if ($page_type === 'pending'): ?>
                                                    <a href="<?= base_url('surat/' . $s['id']) ?>" 
                                                       class="btn btn-outline-success" title="Review">
                                                        <i class="bi bi-check2"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Cards -->
                            <div class="mobile-only">
                                <?php foreach ($surat as $s): ?>
                                <div class="mobile-table-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong><?= esc($s['nomor_surat']) ?></strong>
                                            <?php if (isset($s['nama_prodi'])): ?>
                                                <br><small class="text-muted"><?= esc($s['nama_prodi']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <span class="badge <?= $priorityClasses[$s['prioritas']] ?? 'bg-secondary' ?>">
                                            <?= ucfirst(str_replace('_', ' ', $s['prioritas'])) ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <div class="fw-medium"><?= esc(substr($s['perihal'], 0, 60)) ?><?= strlen($s['perihal']) > 60 ? '...' : '' ?></div>
                                        <?php if (isset($s['created_by_name'])): ?>
                                            <small class="text-muted">by <?= esc($s['created_by_name']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-info me-1"><?= ucfirst($s['kategori']) ?></span>
                                            <span class="badge <?= $statusClasses[$s['status']] ?? 'bg-secondary' ?>">
                                                <?= str_replace('_', ' ', $s['status']) ?>
                                            </span>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('surat/' . $s['id']) ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($page_type === 'pending'): ?>
                                            <a href="<?= base_url('surat/' . $s['id']) ?>" 
                                               class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-check2"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($s['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/pwa.js') ?>"></script>

    <style>
        .stats-card {
            transition: transform 0.2s ease-in-out;
            cursor: pointer;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }

        .mobile-table-card {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        @media (max-width: 768px) {
            .main-content {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }
        }
    </style>
</body>
</html>