<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Vertical Timeline Styles */
        .vertical-timeline {
            position: relative;
            padding-left: 0;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 3rem;
            padding-bottom: 2rem;
            border-left: 3px solid #e9ecef;
        }
        
        .timeline-item:last-child {
            border-left-color: transparent;
            padding-bottom: 0;
        }
        
        .timeline-marker {
            position: absolute;
            left: -15px;
            top: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .timeline-content {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-left: 1rem;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .timeline-content::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 15px;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 8px 8px 8px 0;
            border-color: transparent #f8f9fa transparent transparent;
        }
        
        .timeline-item.success .timeline-marker {
            background: #28a745;
        }
        
        .timeline-item.danger .timeline-marker {
            background: #dc3545;
        }
        
        .timeline-item.warning .timeline-marker {
            background: #ffc107;
        }
        
        .timeline-item.primary .timeline-marker {
            background: #007bff;
        }
        
        .timeline-item.info .timeline-marker {
            background: #17a2b8;
        }
        
        .timeline-item.current {
            border-left-color: #007bff;
        }
        
        .timeline-item.current .timeline-content {
            background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
            border: 1px solid #007bff;
        }
        
        /* File History Styles */
        .file-history-item {
            position: relative;
            padding-left: 2rem;
            padding-bottom: 1rem;
            border-left: 2px solid #e9ecef;
        }
        .file-history-item:last-child {
            border-left-color: transparent;
        }
        .file-history-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 0.5rem;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #6c757d;
        }
        .file-history-item.active::before {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?= $this->include('partials/sidebar') ?>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="bi bi-file-text me-2"></i><?= esc($surat['nomor_surat']) ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2" role="group">
                            <?php if ($can_edit): ?>
                            <a href="<?= base_url('surat/' . $surat['id'] . '/edit') ?>" class="btn btn-outline-warning">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                            <?php endif; ?>
                            
                            <?php if (in_array($surat['status'], ['DRAFT', 'NEED_REVISION']) && $surat['created_by'] == session()->get('user_id')): ?>
                            <button type="button" class="btn btn-primary" onclick="submitSurat(<?= $surat['id'] ?>)">
                                <i class="bi bi-send me-2"></i>Submit untuk Review
                            </button>
                            <?php endif; ?>
                        </div>
                        <a href="<?= base_url('surat') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
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

                <div class="row">
                    <!-- Enhanced Header Card -->
                    <div class="col-12 mb-4">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <div class="card-body py-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                                <i class="bi bi-file-text" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h3 class="mb-1 fw-bold"><?= esc($surat['nomor_surat']) ?></h3>
                                                <small class="opacity-75">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?= date('d F Y', strtotime($surat['tanggal_surat'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                        <p class="mb-2 opacity-90" style="font-size: 1.1rem; line-height: 1.4;">
                                            <?= esc(substr($surat['perihal'], 0, 80)) ?><?= strlen($surat['perihal']) > 80 ? '...' : '' ?>
                                        </p>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <span class="badge bg-white text-primary px-2 py-1">
                                                <i class="bi bi-building me-1"></i><?= esc($surat['nama_prodi']) ?>
                                            </span>
                                            <span class="badge bg-white text-info px-2 py-1">
                                                <i class="bi bi-tag me-1"></i><?= ucfirst($surat['kategori']) ?>
                                            </span>
                                            <?php 
                                            $priorityConfig = [
                                                'normal' => ['class' => 'text-secondary', 'icon' => 'bi-circle'],
                                                'urgent' => ['class' => 'text-warning', 'icon' => 'bi-exclamation-triangle'],
                                                'sangat_urgent' => ['class' => 'text-danger', 'icon' => 'bi-exclamation-triangle-fill']
                                            ];
                                            $pConfig = $priorityConfig[$surat['prioritas']] ?? $priorityConfig['normal'];
                                            ?>
                                            <span class="badge bg-white <?= $pConfig['class'] ?> px-2 py-1">
                                                <i class="bi <?= $pConfig['icon'] ?> me-1"></i><?= ucfirst(str_replace('_', ' ', $surat['prioritas'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="text-center p-3" style="background: rgba(255,255,255,0.15); border-radius: 12px; backdrop-filter: blur(10px);">
                                                    <?php 
                                                    $statusConfig = [
                                                        'DRAFT' => ['icon' => 'bi-pencil-square', 'text' => 'Draft', 'color' => '#6c757d'],
                                                        'SUBMITTED' => ['icon' => 'bi-arrow-up-circle', 'text' => 'Submitted', 'color' => '#007bff'],
                                                        'UNDER_REVIEW' => ['icon' => 'bi-eye', 'text' => 'Under Review', 'color' => '#17a2b8'],
                                                        'NEED_REVISION' => ['icon' => 'bi-exclamation-circle', 'text' => 'Need Revision', 'color' => '#ffc107'],
                                                        'APPROVED_L1' => ['icon' => 'bi-check-circle', 'text' => 'Approved L1', 'color' => '#28a745'],
                                                        'APPROVED_L2' => ['icon' => 'bi-check-circle-fill', 'text' => 'Approved L2', 'color' => '#28a745'],
                                                        'READY_DISPOSISI' => ['icon' => 'bi-check2-all', 'text' => 'Ready Disposisi', 'color' => '#20c997'],
                                                        'IN_PROCESS' => ['icon' => 'bi-arrow-repeat', 'text' => 'In Process', 'color' => '#fd7e14'],
                                                        'COMPLETED' => ['icon' => 'bi-check-all', 'text' => 'Completed', 'color' => '#28a745'],
                                                        'REJECTED' => ['icon' => 'bi-x-circle', 'text' => 'Rejected', 'color' => '#dc3545']
                                                    ];
                                                    $sConfig = $statusConfig[$surat['status']] ?? ['icon' => 'bi-circle', 'text' => 'Unknown', 'color' => '#6c757d'];
                                                    ?>
                                                    <i class="bi <?= $sConfig['icon'] ?>" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block;"></i>
                                                    <div class="fw-bold" style="font-size: 0.9rem;"><?= $sConfig['text'] ?></div>
                                                    <small class="opacity-75">Current Status</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center p-3" style="background: rgba(255,255,255,0.15); border-radius: 12px; backdrop-filter: blur(10px);">
                                                    <i class="bi bi-person-circle" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block;"></i>
                                                    <div class="fw-bold" style="font-size: 0.9rem;"><?= esc($surat['creator_name']) ?></div>
                                                    <small class="opacity-75">Created By</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="<?= base_url('workflow/timeline/' . $surat['id']) ?>" 
                                               class="btn btn-light btn-sm px-3">
                                                <i class="bi bi-clock-history me-2"></i>View Full Timeline
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Surat Details -->
                    <div class="col-lg-8">
                        <!-- Info Dasar Card -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-info-circle me-2 text-primary"></i>
                                    Informasi Dasar
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Primary Information Grid -->
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="bi bi-hash text-primary me-2"></i>
                                                <span class="fw-bold text-primary">Nomor Surat</span>
                                            </div>
                                            <div class="info-value h5 mb-0 mt-1"><?= esc($surat['nomor_surat']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="bi bi-calendar3 text-success me-2"></i>
                                                <span class="fw-bold text-success">Tanggal Surat</span>
                                            </div>
                                            <div class="info-value h5 mb-0 mt-1"><?= date('d F Y', strtotime($surat['tanggal_surat'])) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Perihal Section -->
                                <div class="perihal-section mb-4">
                                    <div class="info-label mb-3">
                                        <i class="bi bi-file-text text-info me-2"></i>
                                        <span class="fw-bold text-info">Perihal</span>
                                    </div>
                                    <div class="perihal-content" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #17a2b8; padding: 1.5rem; border-radius: 10px; font-size: 1.1rem; line-height: 1.6;">
                                        <?= nl2br(esc($surat['perihal'])) ?>
                                    </div>
                                </div>

                                <!-- Classification Grid -->
                                <div class="classification-grid mb-4">
                                    <div class="info-label mb-3">
                                        <i class="bi bi-tags text-warning me-2"></i>
                                        <span class="fw-bold text-warning">Klasifikasi</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="classification-item p-3" style="background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%); border-radius: 10px; border: 1px solid #e3f2fd;">
                                                <div class="d-flex align-items-center justify-content-center mb-2">
                                                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="bi bi-tag text-info"></i>
                                                    </div>
                                                    <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                                                        <?= ucfirst($surat['kategori']) ?>
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block text-center">Kategori</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="classification-item p-3" style="background: linear-gradient(135deg, #fff3cd 0%, #f8f9fa 100%); border-radius: 10px; border: 1px solid #fff3cd;">
                                                <div class="d-flex align-items-center justify-content-center mb-2">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="bi <?= $pConfig['icon'] ?> text-warning"></i>
                                                    </div>
                                                    <span class="badge" style="background: <?= $pConfig['class'] === 'text-danger' ? 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)' : ($pConfig['class'] === 'text-warning' ? 'linear-gradient(135deg, #ffc107 0%, #d39e00 100%)' : 'linear-gradient(135deg, #6c757d 0%, #5a6268 100%)') ?>; font-size: 0.9rem; padding: 0.5rem 0.75rem; color: white;">
                                                        <?= ucfirst(str_replace('_', ' ', $surat['prioritas'])) ?>
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block text-center">Prioritas</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="classification-item p-3" style="background: linear-gradient(135deg, #d1ecf1 0%, #f8f9fa 100%); border-radius: 10px; border: 1px solid #d1ecf1;">
                                                <div class="text-center mb-2">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 d-inline-flex">
                                                        <i class="bi bi-bullseye text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="fw-medium text-center text-truncate" title="<?= esc($surat['tujuan']) ?>">
                                                    <?= esc($surat['tujuan']) ?>
                                                </div>
                                                <small class="text-muted d-block text-center">Tujuan</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($surat['keterangan']): ?>
                                <!-- Keterangan Section -->
                                <div class="keterangan-section mb-4">
                                    <div class="info-label mb-3">
                                        <i class="bi bi-chat-left-text text-secondary me-2"></i>
                                        <span class="fw-bold text-secondary">Keterangan Tambahan</span>
                                    </div>
                                    <div class="keterangan-content" style="background: linear-gradient(135deg, #f1f3f4 0%, #e9ecef 100%); border-left: 4px solid #6c757d; padding: 1.5rem; border-radius: 10px; font-style: italic;">
                                        <?= nl2br(esc($surat['keterangan'])) ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Metadata Section -->
                                <div class="metadata-section">
                                    <div class="info-label mb-3">
                                        <i class="bi bi-info-circle text-muted me-2"></i>
                                        <span class="fw-bold text-muted">Informasi Meta</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="meta-item p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 3px solid #28a745;">
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="bi bi-mortarboard text-success me-2"></i>
                                                    <small class="text-success fw-bold">PROGRAM STUDI</small>
                                                </div>
                                                <div class="fw-medium"><?= esc($surat['nama_prodi']) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="meta-item p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 3px solid #007bff;">
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="bi bi-person-badge text-primary me-2"></i>
                                                    <small class="text-primary fw-bold">PEMBUAT</small>
                                                </div>
                                                <div class="fw-medium"><?= esc($surat['creator_name']) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="meta-item p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 3px solid #17a2b8;">
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="bi bi-clock text-info me-2"></i>
                                                    <small class="text-info fw-bold">DIBUAT</small>
                                                </div>
                                                <div class="fw-medium"><?= date('d M Y', strtotime($surat['created_at'])) ?></div>
                                                <small class="text-muted"><?= date('H:i', strtotime($surat['created_at'])) ?> WIB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Info Widgets -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="quick-info-widget p-3" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 12px; text-align: center;">
                                    <i class="bi bi-speedometer text-primary" style="font-size: 1.5rem;"></i>
                                    <h6 class="mt-2 mb-1 text-primary">Processing Time</h6>
                                    <div class="fw-bold">
                                        <?php 
                                        $createdTime = strtotime($surat['created_at']);
                                        $currentTime = time();
                                        $processingDays = ceil(($currentTime - $createdTime) / (24 * 60 * 60));
                                        echo $processingDays;
                                        ?> hari
                                    </div>
                                    <small class="text-muted">Sejak dibuat</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="quick-info-widget p-3" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); border-radius: 12px; text-align: center;">
                                    <i class="bi bi-diagram-3 text-success" style="font-size: 1.5rem;"></i>
                                    <h6 class="mt-2 mb-1 text-success">Workflow Stage</h6>
                                    <div class="fw-bold">
                                        <?php 
                                        $stageOrder = [
                                            'DRAFT' => 1, 'SUBMITTED' => 2, 'UNDER_REVIEW' => 3, 
                                            'APPROVED_L1' => 4, 'APPROVED_L2' => 5, 'READY_DISPOSISI' => 6,
                                            'IN_PROCESS' => 7, 'COMPLETED' => 8, 'REJECTED' => 0
                                        ];
                                        $currentStage = $stageOrder[$surat['status']] ?? 0;
                                        echo ($currentStage > 0) ? "$currentStage / 8" : 'Rejected';
                                        ?>
                                    </div>
                                    <small class="text-muted">Progress</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="quick-info-widget p-3" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%); border-radius: 12px; text-align: center;">
                                    <i class="bi bi-people text-warning" style="font-size: 1.5rem;"></i>
                                    <h6 class="mt-2 mb-1 text-warning">Approval Count</h6>
                                    <div class="fw-bold"><?= count($workflow ?? []) ?></div>
                                    <small class="text-muted">Total actions</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Workflow Actions for Approvers -->
                        <?php if ($can_approve): ?>
                        <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px; background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-check2-square me-2 text-warning"></i>
                                    <span style="color: #f57f17;">Aksi Approval</span>
                                </h5>
                                <small class="text-muted" style="color: #8d6e00 !important;">Pilih tindakan yang sesuai untuk surat ini</small>
                            </div>
                            <div class="card-body" style="background: white; border-radius: 0 0 15px 15px;">
                                <div class="mb-4">
                                    <label for="approvalKeterangan" class="form-label fw-bold">
                                        <i class="bi bi-chat-left-text text-info me-2"></i>
                                        Keterangan Approval
                                    </label>
                                    <textarea class="form-control" id="approvalKeterangan" rows="4" 
                                              style="border-radius: 10px; resize: vertical; border: 2px solid #e9ecef;"
                                              placeholder="Berikan catatan, feedback, atau keterangan untuk keputusan Anda...">
                                    </textarea>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Keterangan akan disimpan dalam timeline workflow
                                    </small>
                                </div>
                                
                                <!-- Action Buttons dengan Enhanced Style -->
                                <div class="action-buttons">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-warning w-100 py-3" 
                                                    style="border-radius: 12px; font-weight: 600; transition: all 0.3s ease;"
                                                    onclick="workflowAction('revise', <?= $surat['id'] ?>)"
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(255,193,7,0.3)'"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                <i class="bi bi-arrow-counterclockwise me-2"></i>
                                                <div>Minta Revisi</div>
                                                <small class="d-block opacity-75">Return for changes</small>
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-danger w-100 py-3" 
                                                    style="border-radius: 12px; font-weight: 600; transition: all 0.3s ease;"
                                                    onclick="workflowAction('reject', <?= $surat['id'] ?>)"
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(220,53,69,0.3)'"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                <i class="bi bi-x-circle me-2"></i>
                                                <div>Tolak</div>
                                                <small class="d-block opacity-75">Reject permanently</small>
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-success w-100 py-3" 
                                                    style="border-radius: 12px; font-weight: 600; transition: all 0.3s ease; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none;"
                                                    onclick="workflowAction('approve', <?= $surat['id'] ?>)"
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(40,167,69,0.4)'"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                <i class="bi bi-check-circle me-2"></i>
                                                <div>Setujui</div>
                                                <small class="d-block opacity-75">Approve & continue</small>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Disposisi Action for Dekan -->
                        <?php if ($user_role === 'dekan' && $surat['status'] === 'APPROVED_L2'): ?>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-share me-2"></i>Disposisi Surat</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Surat akan didisposisi otomatis berdasarkan kategori ke:</p>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <?php 
                                    $targets = [
                                        'akademik' => 'Wakil Dekan Bidang Akademik',
                                        'kemahasiswaan' => 'Wakil Dekan Bidang Kemahasiswaan',
                                        'kepegawaian' => 'Wakil Dekan Bidang Umum',
                                        'keuangan' => 'Kepala Urusan Keuangan',
                                        'umum' => 'Kepala Bagian Tata Usaha'
                                    ];
                                    echo $targets[$surat['kategori']] ?? 'Kepala Bagian Tata Usaha';
                                    ?>
                                </div>
                                <div class="d-grid">
                                    <button type="button" class="btn btn-primary" 
                                            onclick="workflowAction('dispose', <?= $surat['id'] ?>)">
                                        <i class="bi bi-share me-2"></i>Disposisi Surat
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Complete Action -->
                        <?php 
                        $canComplete = false;
                        if ($surat['status'] === 'IN_PROCESS') {
                            $categoryRoleMapping = [
                                'akademik' => 'wd_akademik',
                                'kemahasiswaan' => 'wd_kemahasiswa', 
                                'kepegawaian' => 'wd_umum',
                                'keuangan' => 'kaur_keuangan',
                                'umum' => 'kabag_tu'
                            ];
                            $canComplete = isset($categoryRoleMapping[$surat['kategori']]) && 
                                          $categoryRoleMapping[$surat['kategori']] === $user_role;
                        }
                        ?>
                        <?php if ($canComplete): ?>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-check2-all me-2"></i>Selesaikan Surat</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="completeKeterangan" class="form-label">Keterangan Penyelesaian</label>
                                    <textarea class="form-control" id="completeKeterangan" rows="3" 
                                              placeholder="Masukkan keterangan penyelesaian..."></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="button" class="btn btn-success" 
                                            onclick="workflowAction('complete', <?= $surat['id'] ?>)">
                                        <i class="bi bi-check2-all me-2"></i>Tandai Selesai
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Workflow History & Lampiran -->
                    <div class="col-lg-4">
                        <!-- Enhanced Workflow History -->
                        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                            <div class="card-header bg-transparent border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-clock-history me-2 text-primary"></i>
                                            Timeline Workflow
                                        </h6>
                                        <small class="text-muted">Real-time progress tracking</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="refreshTimeline()" title="Refresh">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                        <a href="<?= base_url('workflow/timeline/' . $surat['id']) ?>" 
                                           class="btn btn-primary" title="Full Timeline">
                                            <i class="bi bi-fullscreen"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (empty($workflow)): ?>
                                    <div class="text-center text-muted">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p class="mt-2 mb-0">Belum ada aktivitas</p>
                                    </div>
                                <?php else: ?>
                                    <div class="vertical-timeline">
                                        <?php foreach ($workflow as $index => $w): ?>
                                        <?php 
                                        // Define action styling and details
                                        $actionConfig = [
                                            'SUBMIT' => ['class' => 'primary', 'icon' => 'bi-send', 'text' => 'Disubmit untuk review'],
                                            'APPROVE' => ['class' => 'success', 'icon' => 'bi-check-circle', 'text' => 'Disetujui'],
                                            'REJECT' => ['class' => 'danger', 'icon' => 'bi-x-circle', 'text' => 'Ditolak - Perlu Revisi'],
                                            'REVISE' => ['class' => 'warning', 'icon' => 'bi-arrow-repeat', 'text' => 'Direvisi dan disubmit ulang'],
                                            'UPDATE' => ['class' => 'info', 'icon' => 'bi-pencil', 'text' => 'Diperbarui'],
                                            'COMPLETE' => ['class' => 'success', 'icon' => 'bi-check-all', 'text' => 'Diselesaikan'],
                                        ];
                                        
                                        $config = $actionConfig[$w['action_type']] ?? ['class' => 'secondary', 'icon' => 'bi-circle', 'text' => $w['action_type']];
                                        $isLatest = ($index === count($workflow) - 1);
                                        ?>
                                        
                                        <div class="timeline-item <?= $config['class'] ?> <?= $isLatest ? 'current' : '' ?>">
                                            <div class="timeline-marker">
                                                <i class="bi <?= $config['icon'] ?>"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0 text-<?= $config['class'] ?>">
                                                        <?= $config['text'] ?>
                                                        <?= $isLatest ? '<span class="badge bg-primary ms-2">Terbaru</span>' : '' ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <?= date('d M Y', strtotime($w['created_at'])) ?>
                                                    </small>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="bi bi-person-circle me-2 text-muted"></i>
                                                        <strong><?= esc($w['action_by_name']) ?></strong>
                                                        <span class="badge bg-secondary ms-2"><?= ucwords(str_replace('_', ' ', $w['role'])) ?></span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-clock me-2 text-muted"></i>
                                                        <small class="text-muted"><?= date('H:i', strtotime($w['created_at'])) ?> WIB</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <span class="badge bg-light text-dark me-1"><?= str_replace('_', ' ', $w['from_status']) ?></span>
                                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                                    <span class="badge bg-<?= $config['class'] ?>"><?= str_replace('_', ' ', $w['to_status']) ?></span>
                                                </div>
                                                
                                                <?php if ($w['keterangan']): ?>
                                                <div class="alert alert-<?= $w['action_type'] === 'REJECT' ? 'danger' : 'info' ?> alert-sm py-2 px-3 mb-0">
                                                    <div class="d-flex align-items-start">
                                                        <i class="bi bi-<?= $w['action_type'] === 'REJECT' ? 'exclamation-triangle' : 'info-circle' ?> me-2 mt-1"></i>
                                                        <div>
                                                            <strong><?= $w['action_type'] === 'REJECT' ? 'Alasan penolakan:' : 'Catatan:' ?></strong><br>
                                                            <span><?= esc($w['keterangan']) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Enhanced Lampiran Section -->
                        <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px;">
                            <div class="card-header bg-transparent border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-paperclip me-2 text-success"></i>
                                            Lampiran Dokumen
                                        </h6>
                                        <small class="text-muted">
                                            <?php if (!empty($lampiran)): ?>
                                                <?= count($lampiran) ?> file attached • 
                                                <?php 
                                                $totalSize = 0;
                                                foreach ($lampiran as $l) {
                                                    $totalSize += $l['ukuran_file'];
                                                }
                                                echo number_format($totalSize / 1024, 1) . ' KB total';
                                                ?>
                                            <?php else: ?>
                                                No files attached
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <?php if (in_array($surat['status'], ['DRAFT', 'NEED_REVISION']) && 
                                              $surat['created_by'] == session()->get('user_id')): ?>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-success" onclick="showUploadModal()">
                                            <i class="bi bi-plus me-1"></i>Upload
                                        </button>
                                        <a href="<?= base_url('file/upload/' . $surat['id']) ?>" 
                                           class="btn btn-success">
                                            <i class="bi bi-folder-plus me-1"></i>Manage
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (empty($lampiran)): ?>
                                    <div class="text-center text-muted">
                                        <i class="bi bi-file-earmark" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p class="mt-2 mb-0">Belum ada lampiran</p>
                                        <?php if (in_array($surat['status'], ['DRAFT', 'NEED_REVISION']) && 
                                                  $surat['created_by'] == session()->get('user_id')): ?>
                                        <small>
                                            <a href="<?= base_url('file/upload/' . $surat['id']) ?>" class="text-decoration-none">
                                                Klik di sini untuk upload file
                                            </a>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Enhanced File Display -->
                                    <div class="files-grid">
                                        <?php foreach ($lampiran as $l): ?>
                                        <div class="file-item mb-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; padding: 1rem; border: 1px solid #e9ecef; transition: all 0.3s ease;" 
                                             onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)'" 
                                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                            <div class="d-flex align-items-start">
                                                <div class="file-icon me-3">
                                                    <?php 
                                                    $ext = strtolower(pathinfo($l['nama_asli'], PATHINFO_EXTENSION));
                                                    $iconConfig = [
                                                        'pdf' => ['icon' => 'bi-file-pdf', 'color' => 'text-danger'],
                                                        'doc' => ['icon' => 'bi-file-word', 'color' => 'text-primary'],
                                                        'docx' => ['icon' => 'bi-file-word', 'color' => 'text-primary'],
                                                        'xls' => ['icon' => 'bi-file-excel', 'color' => 'text-success'],
                                                        'xlsx' => ['icon' => 'bi-file-excel', 'color' => 'text-success'],
                                                        'jpg' => ['icon' => 'bi-file-image', 'color' => 'text-warning'],
                                                        'jpeg' => ['icon' => 'bi-file-image', 'color' => 'text-warning'],
                                                        'png' => ['icon' => 'bi-file-image', 'color' => 'text-warning'],
                                                        'default' => ['icon' => 'bi-file-earmark-text', 'color' => 'text-info']
                                                    ];
                                                    $config = $iconConfig[$ext] ?? $iconConfig['default'];
                                                    ?>
                                                    <div class="bg-white rounded-circle p-2 d-inline-flex" style="box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                        <i class="bi <?= $config['icon'] ?> <?= $config['color'] ?>" style="font-size: 1.2rem;"></i>
                                                    </div>
                                                </div>
                                                <div class="file-info flex-grow-1">
                                                    <h6 class="mb-1 fw-bold" style="color: #495057;"><?= esc($l['nama_asli']) ?></h6>
                                                    <div class="file-meta mb-2">
                                                        <span class="badge bg-primary bg-opacity-25 text-primary me-1">v<?= $l['versi'] ?></span>
                                                        <span class="badge bg-info bg-opacity-25 text-info me-1"><?= number_format($l['ukuran_file'] / 1024, 1) ?> KB</span>
                                                        <span class="badge bg-secondary bg-opacity-25 text-secondary"><?= strtoupper($ext) ?></span>
                                                    </div>
                                                    <div class="file-timestamp d-flex align-items-center text-muted" style="font-size: 0.85rem;">
                                                        <i class="bi bi-clock me-1"></i>
                                                        <?= date('d M Y', strtotime($l['created_at'])) ?> at <?= date('H:i', strtotime($l['created_at'])) ?>
                                                    </div>
                                                    <?php if ($l['keterangan']): ?>
                                                    <div class="file-description mt-2 p-2" style="background: rgba(255,255,255,0.7); border-radius: 6px; font-style: italic; color: #6c757d;">
                                                        <i class="bi bi-chat-left-text me-1"></i>
                                                        <?= esc($l['keterangan']) ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <!-- File Actions -->
                                            <div class="file-actions mt-3 d-flex gap-2">
                                                <button class="btn btn-sm btn-info" 
                                                        style="border-radius: 8px; font-weight: 500;"
                                                        onclick="previewFile(<?= $l['id'] ?>, '<?= esc($l['nama_asli']) ?>')" 
                                                        title="Preview File">
                                                    <i class="bi bi-eye me-1"></i>Preview
                                                </button>
                                                <a href="<?= base_url('file/download/' . $l['id']) ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   style="border-radius: 8px; font-weight: 500;"
                                                   title="Download File">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                                <?php if (in_array($surat['status'], ['DRAFT', 'NEED_REVISION']) && 
                                                          $surat['created_by'] == session()->get('user_id')): ?>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        style="border-radius: 8px; font-weight: 500;"
                                                        onclick="deleteFile(<?= $l['id'] ?>)" 
                                                        title="Hapus File">
                                                    <i class="bi bi-trash me-1"></i>Delete
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- File History -->
                                    <?php if (count($fileHistory) > 1): ?>
                                    <div class="mt-4">
                                        <h6 class="border-bottom pb-2">
                                            <i class="bi bi-clock-history me-2"></i>Riwayat File 
                                            <span class="badge bg-info"><?= count($fileHistory) ?> versi</span>
                                        </h6>
                                        <div class="file-history">
                                            <?php foreach ($fileHistory as $h): ?>
                                            <div class="file-history-item <?= $h['is_final'] ? 'active' : '' ?>">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold small mb-1">
                                                            <?= esc($h['nama_asli']) ?>
                                                            <?php if ($h['is_final']): ?>
                                                                <span class="badge bg-success ms-2">AKTIF</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="text-muted small">
                                                            Versi <?= $h['versi'] ?> • <?= number_format($h['ukuran_file'] / 1024, 1) ?> KB<br>
                                                            <i class="bi bi-person me-1"></i><?= esc($h['uploaded_by_name']) ?> • 
                                                            <?= date('d M Y H:i', strtotime($h['created_at'])) ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <a href="<?= base_url('file/download/' . $h['id']) ?>" 
                                                           class="btn btn-sm <?= $h['is_final'] ? 'btn-success' : 'btn-outline-secondary' ?>" 
                                                           title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced Timeline Refresh
        function refreshTimeline() {
            const timelineSection = document.querySelector('.vertical-timeline');
            if (timelineSection) {
                // Show loading state
                timelineSection.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Refreshing timeline...</p>
                    </div>
                `;
                
                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        }
        
        // Show Upload Modal
        function showUploadModal() {
            // This would show a modal for quick file upload
            window.location.href = `<?= base_url('file/upload/' . $surat['id']) ?>`;
        }
        
        async function submitSurat(suratId) {
            const confirmed = await SuratNotification.confirm(
                'Submit Surat untuk Review?',
                'Surat akan dikirim ke tahap review selanjutnya',
                'Ya, Submit',
                'Batal'
            );
            
            if (confirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `<?= base_url('surat/') ?>${suratId}/submit`;
                
                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '<?= csrf_token() ?>';
                token.value = '<?= csrf_hash() ?>';
                form.appendChild(token);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        async function workflowAction(action, suratId) {
            let keteranganId = action === 'complete' ? 'completeKeterangan' : 'approvalKeterangan';
            let keterangan = document.getElementById(keteranganId)?.value || '';
            
            let confirmMessage = '';
            switch(action) {
                case 'approve': confirmMessage = 'Apakah Anda yakin ingin menyetujui surat ini?'; break;
                case 'reject': confirmMessage = 'Apakah Anda yakin ingin menolak surat ini?'; break;
                case 'revise': confirmMessage = 'Apakah Anda yakin ingin meminta revisi surat ini?'; break;
                case 'dispose': confirmMessage = 'Apakah Anda yakin ingin mendisposisi surat ini?'; break;
                case 'complete': confirmMessage = 'Apakah Anda yakin surat ini telah selesai diproses?'; break;
            }

            const confirmed = await SuratNotification.confirm(
                'Konfirmasi Workflow',
                confirmMessage,
                'Ya, Lanjutkan',
                'Batal'
            );
            
            if (confirmed) {
                fetch(`<?= base_url('workflow/') ?>${action}/${suratId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                        'keterangan': keterangan
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        SuratNotification.success('Workflow Berhasil!', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        SuratNotification.error('Workflow Gagal!', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    SuratNotification.error('Workflow Error!', 'Terjadi kesalahan saat memproses permintaan');
                });
            }
        }

        async function deleteFile(fileId) {
            const confirmed = await SuratNotification.confirmDelete(
                'Hapus File?',
                'File yang dihapus tidak dapat dikembalikan!'
            );
            
            if (confirmed) {
                fetch(`<?= base_url('file/delete/') ?>${fileId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        SuratNotification.success('File Terhapus!', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        SuratNotification.error('Gagal Hapus!', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    SuratNotification.error('Delete Error!', 'Terjadi kesalahan saat menghapus file');
                });
            }
        }
    </script>

    <style>
        /* Enhanced Detail Surat Styling */
        
        /* Info Item Styling */
        .info-item {
            padding: 1rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 10px;
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }
        
        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
        }
        
        .info-label {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .info-value {
            color: #495057;
            font-weight: 600;
        }
        
        /* Classification Grid */
        .classification-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .classification-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        /* Meta Item Styling */
        .meta-item {
            transition: all 0.3s ease;
        }
        
        .meta-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Quick Info Widgets */
        .quick-info-widget {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .quick-info-widget:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        /* Enhanced Timeline */
        .timeline-item {
            position: relative;
        }
        
        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 12px;
            top: 30px;
            width: 2px;
            height: calc(100% + 10px);
            background: linear-gradient(180deg, #007bff 0%, #e9ecef 100%);
        }
        
        /* File Grid Enhancements */
        .files-grid {
            display: grid;
            gap: 1rem;
        }
        
        .file-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .file-actions .btn {
            transition: all 0.3s ease;
        }
        
        .file-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Action Buttons */
        .action-buttons .btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Header Gradient Animation */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .card[style*="linear-gradient"] {
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .quick-info-widget {
                margin-bottom: 1rem;
            }
            
            .classification-grid .col-md-4 {
                margin-bottom: 1rem;
            }
            
            .meta-item {
                margin-bottom: 1rem;
            }
            
            .action-buttons .col-md-4 {
                margin-bottom: 0.5rem;
            }
            
            .file-item {
                margin-bottom: 1rem;
            }
        }
        
        /* Loading States */
        .loading-placeholder {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Enhanced Badges */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        
        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Focus States */
        .btn:focus,
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        /* Card Hover Effects */
        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        function previewFile(lampiranId, filename) {
            // Show loading state
            document.getElementById('previewModalLabel').textContent = 'Loading...';
            document.getElementById('previewContent').innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Show modal
            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            previewModal.show();

            // Fetch preview data
            fetch(`<?= base_url('file/preview/') ?>${lampiranId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('previewModalLabel').textContent = `Preview: ${filename}`;
                    
                    if (data.error) {
                        document.getElementById('previewContent').innerHTML = 
                            `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>${data.error}</div>`;
                        return;
                    }

                    let content = '';
                    switch(data.type) {
                        case 'image':
                            content = `<div class="text-center"><img src="${data.data}" class="img-fluid" style="max-height: 70vh;" alt="${data.filename}"></div>`;
                            break;
                        case 'pdf':
                            content = `<iframe src="${data.url}" style="width: 100%; height: 70vh; border: none;"></iframe>`;
                            break;
                        case 'text':
                            content = `<pre style="white-space: pre-wrap; max-height: 70vh; overflow-y: auto;">${data.content}</pre>`;
                            break;
                        case 'download':
                            content = `
                                <div class="text-center py-4">
                                    <i class="bi bi-file-earmark" style="font-size: 4rem; color: #6c757d;"></i>
                                    <h5 class="mt-3">${data.message}</h5>
                                    <a href="${data.downloadUrl}" class="btn btn-primary mt-3">
                                        <i class="bi bi-download me-2"></i>Download File
                                    </a>
                                </div>
                            `;
                            break;
                    }
                    
                    document.getElementById('previewContent').innerHTML = content;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('previewContent').innerHTML = 
                        `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Terjadi kesalahan saat memuat preview</div>`;
                });
        }
    </script>

    <!-- File Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>