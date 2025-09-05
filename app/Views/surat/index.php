<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/table-design.css') ?>">
    <?= $this->include('partials/notifications') ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?= $this->include('partials/sidebar') ?>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2"><i class="bi bi-envelope me-2"></i>Daftar Surat</h1>
                        <small class="text-muted">
                            Menampilkan <?= count($surat) ?> dari <?= $total_results ?> surat
                            <?php if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['kategori']) || !empty($filters['prioritas'])): ?>
                                (dengan filter)
                            <?php endif; ?>
                        </small>
                    </div>
                    <?php if ($user_role === 'admin_prodi'): ?>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" id="bulk-submit-btn" class="btn btn-success" style="display: none;">
                                <i class="bi bi-send me-2"></i>Submit Terpilih
                            </button>
                        </div>
                        <a href="<?= base_url('surat/create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Buat Surat Baru
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

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

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-file-text text-primary" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-primary"><?= array_sum($stats) ?></h3>
                                <small class="text-muted">Total Surat</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-warning">
                                    <?= ($stats['SUBMITTED'] ?? 0) + ($stats['UNDER_REVIEW'] ?? 0) + ($stats['APPROVED_L1'] ?? 0) + ($stats['NEED_REVISION'] ?? 0) ?>
                                </h3>
                                <small class="text-muted">Dalam Proses</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-success"><?= $stats['COMPLETED'] ?? 0 ?></h3>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0 text-danger"><?= $stats['REJECTED'] ?? 0 ?></h3>
                                <small class="text-muted">Ditolak</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter and Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="get" class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Cari</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Nomor surat / Perihal" value="<?= esc($filters['search']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="DRAFT" <?= $filters['status'] === 'DRAFT' ? 'selected' : '' ?>>Draft</option>
                                    <option value="SUBMITTED" <?= $filters['status'] === 'SUBMITTED' ? 'selected' : '' ?>>Submitted</option>
                                    <option value="UNDER_REVIEW" <?= $filters['status'] === 'UNDER_REVIEW' ? 'selected' : '' ?>>Under Review</option>
                                    <option value="APPROVED_L1" <?= $filters['status'] === 'APPROVED_L1' ? 'selected' : '' ?>>Approved L1</option>
                                    <option value="APPROVED_L2" <?= $filters['status'] === 'APPROVED_L2' ? 'selected' : '' ?>>Approved L2</option>
                                    <option value="COMPLETED" <?= $filters['status'] === 'COMPLETED' ? 'selected' : '' ?>>Completed</option>
                                    <option value="REJECTED" <?= $filters['status'] === 'REJECTED' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori" name="kategori">
                                    <option value="">Semua Kategori</option>
                                    <option value="akademik" <?= $filters['kategori'] === 'akademik' ? 'selected' : '' ?>>Akademik</option>
                                    <option value="kemahasiswaan" <?= $filters['kategori'] === 'kemahasiswaan' ? 'selected' : '' ?>>Kemahasiswaan</option>
                                    <option value="kepegawaian" <?= $filters['kategori'] === 'kepegawaian' ? 'selected' : '' ?>>Kepegawaian</option>
                                    <option value="keuangan" <?= $filters['kategori'] === 'keuangan' ? 'selected' : '' ?>>Keuangan</option>
                                    <option value="umum" <?= $filters['kategori'] === 'umum' ? 'selected' : '' ?>>Umum</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="prioritas" class="form-label">Prioritas</label>
                                <select class="form-select" id="prioritas" name="prioritas">
                                    <option value="">Semua Prioritas</option>
                                    <option value="normal" <?= $filters['prioritas'] === 'normal' ? 'selected' : '' ?>>Normal</option>
                                    <option value="urgent" <?= $filters['prioritas'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                    <option value="sangat_urgent" <?= $filters['prioritas'] === 'sangat_urgent' ? 'selected' : '' ?>>Sangat Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                    <a href="<?= base_url('surat') ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-circle me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Surat Table -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($surat)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="text-muted mt-3">Belum ada surat</h4>
                                <p class="text-muted">
                                    <?php if ($user_role === 'admin_prodi'): ?>
                                        Silakan buat surat baru untuk memulai.
                                    <?php else: ?>
                                        Tidak ada surat yang perlu direview saat ini.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <?php if ($user_role === 'admin_prodi'): ?>
                                            <th>
                                                <input type="checkbox" id="select-all" class="form-check-input">
                                            </th>
                                            <?php endif; ?>
                                            <th>No</th>
                                            <th>Nomor Surat</th>
                                            <th>Perihal</th>
                                            <th>Kategori</th>
                                            <th>Prioritas</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($surat as $index => $s): ?>
                                        <tr>
                                            <?php if ($user_role === 'admin_prodi' && in_array($s['status'], ['DRAFT', 'NEED_REVISION'])): ?>
                                            <td>
                                                <input type="checkbox" class="form-check-input surat-checkbox" 
                                                       value="<?= $s['id'] ?>" data-status="<?= $s['status'] ?>">
                                            </td>
                                            <?php elseif ($user_role === 'admin_prodi'): ?>
                                            <td></td>
                                            <?php endif; ?>
                                            <td><?= $pager ? (($pager->getCurrentPage('surat') - 1) * 15 + $index + 1) : ($index + 1) ?></td>
                                            <td>
                                                <strong><?= esc($s['nomor_surat']) ?></strong>
                                                <?php if (isset($s['nama_prodi'])): ?>
                                                    <br><small class="text-muted"><?= esc($s['nama_prodi']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= esc(substr($s['perihal'], 0, 60)) ?>
                                                <?= strlen($s['perihal']) > 60 ? '...' : '' ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= ucfirst($s['kategori']) ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $priorityClass = [
                                                    'normal' => 'bg-secondary',
                                                    'urgent' => 'bg-warning',
                                                    'sangat_urgent' => 'bg-danger'
                                                ];
                                                ?>
                                                <span class="badge <?= $priorityClass[$s['prioritas']] ?? 'bg-secondary' ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $s['prioritas'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                $statusClass = [
                                                    'DRAFT' => 'bg-secondary',
                                                    'SUBMITTED' => 'bg-primary',
                                                    'UNDER_REVIEW' => 'bg-info',
                                                    'NEED_REVISION' => 'bg-warning',
                                                    'APPROVED_L1' => 'bg-success',
                                                    'APPROVED_L2' => 'bg-success',
                                                    'READY_DISPOSISI' => 'bg-success',
                                                    'IN_PROCESS' => 'bg-warning',
                                                    'COMPLETED' => 'bg-success',
                                                    'REJECTED' => 'bg-danger',
                                                    'CANCELLED' => 'bg-dark'
                                                ];
                                                ?>
                                                <span class="badge <?= $statusClass[$s['status']] ?? 'bg-secondary' ?>">
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
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?= base_url('surat/' . $s['id']) ?>" 
                                                       class="btn btn-outline-primary" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <?php if ($user_role === 'admin_prodi' && 
                                                              in_array($s['status'], ['DRAFT', 'NEED_REVISION'])): ?>
                                                    <a href="<?= base_url('surat/' . $s['id'] . '/edit') ?>" 
                                                       class="btn btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($pager && $total_results > 15): ?>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Halaman <?= $pager->getCurrentPage('surat') ?> dari <?= $pager->getPageCount('surat') ?>
                                    (<?= $pager->getFirstItem('surat') ?>-<?= $pager->getLastItem('surat') ?> dari <?= $total_results ?> surat)
                                </div>
                                <div>
                                    <?= $pager->links('surat', 'bootstrap_pagination') ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if ($user_role === 'admin_prodi'): ?>
    <script>
    // Bulk submit functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.surat-checkbox');
        const bulkSubmitBtn = document.getElementById('bulk-submit-btn');
        
        // Select all functionality
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const isChecked = this.checked;
                checkboxes.forEach(checkbox => {
                    if (checkbox.dataset.status === 'DRAFT' || checkbox.dataset.status === 'NEED_REVISION') {
                        checkbox.checked = isChecked;
                        // Add/remove selected class for row highlighting
                        const row = checkbox.closest('tr');
                        if (isChecked) {
                            row.classList.add('selected');
                        } else {
                            row.classList.remove('selected');
                        }
                    }
                });
                toggleBulkSubmitBtn();
            });
        }
        
        // Individual checkbox functionality
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                toggleBulkSubmitBtn();
                // Add/remove selected class for row highlighting
                const row = this.closest('tr');
                if (this.checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            });
        });
        
        function toggleBulkSubmitBtn() {
            const checkedBoxes = document.querySelectorAll('.surat-checkbox:checked');
            if (checkedBoxes.length > 0) {
                bulkSubmitBtn.style.display = 'block';
                bulkSubmitBtn.textContent = `Submit ${checkedBoxes.length} Surat Terpilih`;
            } else {
                bulkSubmitBtn.style.display = 'none';
            }
        }
        
        // Bulk submit action
        bulkSubmitBtn.addEventListener('click', async function() {
            const checkedBoxes = document.querySelectorAll('.surat-checkbox:checked');
            const suratIds = Array.from(checkedBoxes).map(cb => cb.value);
            
            // Use modern confirmation dialog
            const confirmed = await SuratNotification.confirm(
                `Submit ${suratIds.length} Surat?`,
                `${suratIds.length} surat akan dikirim untuk proses review. Apakah Anda yakin?`,
                'Ya, Submit!',
                'Batal'
            );
            
            if (!confirmed) return;
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('surat/bulk-submit') ?>';
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '<?= csrf_token() ?>';
            csrfInput.value = '<?= csrf_hash() ?>';
            form.appendChild(csrfInput);
            
            // Add surat IDs
            suratIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'surat_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            
            // Show loading notification
            SuratNotification.loading('Memproses Submit...', `Mengirim ${suratIds.length} surat untuk review`);
            
            form.submit();
        });
    });
    </script>
    <?php endif; ?>
</body>
</html>