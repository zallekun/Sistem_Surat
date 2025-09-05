<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow Timeline - <?= esc($surat['nomor_surat']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?= $this->include('partials/sidebar') ?>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="bi bi-clock-history me-2 text-primary"></i>
                        Workflow Timeline
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="<?= base_url('surat/' . $surat['id']) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Surat
                            </a>
                            <button class="btn btn-outline-primary" onclick="exportTimeline()">
                                <i class="bi bi-download me-1"></i>Export Timeline
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Surat Info Header -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">
                                    <i class="bi bi-file-text me-2"></i>
                                    <?= esc($surat['nomor_surat']) ?>
                                </h4>
                                <p class="mb-2 opacity-90"><?= esc($surat['perihal']) ?></p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-white text-primary">
                                        <i class="bi bi-building me-1"></i><?= esc($surat['prodi_name'] ?? 'N/A') ?>
                                    </span>
                                    <span class="badge bg-white text-primary">
                                        <i class="bi bi-tag me-1"></i><?= ucfirst($surat['kategori']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="bg-white bg-opacity-20 rounded-3 p-3">
                                    <h6 class="mb-1">Current Status</h6>
                                    <h5 class="mb-0">
                                        <?php 
                                        $statusIcons = [
                                            'DRAFT' => 'bi-pencil-square',
                                            'SUBMITTED' => 'bi-arrow-up-circle',
                                            'UNDER_REVIEW' => 'bi-eye',
                                            'NEED_REVISION' => 'bi-exclamation-circle',
                                            'APPROVED_L1' => 'bi-check-circle',
                                            'APPROVED_L2' => 'bi-check-circle-fill',
                                            'READY_DISPOSISI' => 'bi-check2-all',
                                            'IN_PROCESS' => 'bi-arrow-repeat',
                                            'COMPLETED' => 'bi-check-all',
                                            'REJECTED' => 'bi-x-circle'
                                        ];
                                        ?>
                                        <i class="bi <?= $statusIcons[$surat['status']] ?? 'bi-circle' ?> me-2"></i>
                                        <?= str_replace('_', ' ', $surat['status']) ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Processing Statistics -->
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <!-- Workflow Progress Indicator -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="bi bi-diagram-3 text-primary me-2"></i>
                                    Approval Progress
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Progress Steps -->
                                <div class="workflow-progress">
                                    <?php 
                                    $steps = [
                                        1 => ['status' => 'SUBMITTED', 'role' => 'Staff Umum', 'icon' => 'bi-person-check', 'title' => 'Verifikasi Administratif'],
                                        2 => ['status' => 'APPROVED_L1', 'role' => 'Kabag TU', 'icon' => 'bi-person-badge', 'title' => 'Persetujuan Kepala Bagian'],
                                        3 => ['status' => 'APPROVED_L2', 'role' => 'Dekan', 'icon' => 'bi-person-crown', 'title' => 'Persetujuan Dekan'],
                                        4 => ['status' => 'READY_DISPOSISI', 'role' => 'Dekan', 'icon' => 'bi-arrow-right-circle', 'title' => 'Disposisi'],
                                        5 => ['status' => 'IN_PROCESS', 'role' => 'Unit Terkait', 'icon' => 'bi-gear', 'title' => 'Proses Pelaksanaan'],
                                        6 => ['status' => 'COMPLETED', 'role' => 'Selesai', 'icon' => 'bi-check-all', 'title' => 'Selesai']
                                    ];
                                    
                                    $currentStatusOrder = [
                                        'DRAFT' => 0,
                                        'SUBMITTED' => 1,
                                        'UNDER_REVIEW' => 1,
                                        'APPROVED_L1' => 2,
                                        'APPROVED_L2' => 3,
                                        'READY_DISPOSISI' => 4,
                                        'IN_PROCESS' => 5,
                                        'COMPLETED' => 6,
                                        'REJECTED' => -1,
                                        'NEED_REVISION' => 0
                                    ];
                                    
                                    $currentOrder = $currentStatusOrder[$surat['status']] ?? 0;
                                    ?>
                                    
                                    <div class="progress-container">
                                        <?php foreach ($steps as $stepNumber => $step): ?>
                                        <?php 
                                        $isCompleted = ($currentOrder >= $stepNumber && $currentOrder > 0);
                                        $isActive = ($currentOrder == $stepNumber);
                                        $isCurrent = $isActive;
                                        ?>
                                        <div class="progress-step <?= $isCompleted ? 'completed' : '' ?> <?= $isCurrent ? 'current' : '' ?>">
                                            <div class="step-circle">
                                                <i class="bi <?= $step['icon'] ?>"></i>
                                            </div>
                                            <div class="step-content">
                                                <div class="step-title"><?= $step['title'] ?></div>
                                                <div class="step-role"><?= $step['role'] ?></div>
                                            </div>
                                            <?php if ($stepNumber < count($steps)): ?>
                                            <div class="step-connector"></div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Processing Time Stats -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="bi bi-speedometer text-info me-2"></i>
                                    Processing Time
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <h2 class="text-primary mb-0"><?= $processing_time['total_days'] ?? 0 ?></h2>
                                    <small class="text-muted">Total Days</small>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-success mb-0"><?= $processing_time['total_hours'] ?? 0 ?></h4>
                                        <small class="text-muted">Hours</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-warning mb-0"><?= count($workflow ?? []) ?></h4>
                                        <small class="text-muted">Actions</small>
                                    </div>
                                </div>
                                
                                <!-- Average Comparison -->
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">vs Average</small>
                                    <span class="badge bg-success">-15% faster</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Timeline -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-clock-history text-primary me-2"></i>
                                Detailed Timeline
                            </h5>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary active" onclick="filterTimeline('all')">All</button>
                                <button class="btn btn-outline-secondary" onclick="filterTimeline('approvals')">Approvals</button>
                                <button class="btn btn-outline-secondary" onclick="filterTimeline('actions')">Actions</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($workflow)): ?>
                        <div class="timeline">
                            <?php foreach ($workflow as $index => $item): ?>
                            <?php 
                            $actionConfig = [
                                'SUBMIT' => ['color' => 'primary', 'icon' => 'bi-arrow-up-circle', 'bg' => 'bg-primary'],
                                'APPROVE' => ['color' => 'success', 'icon' => 'bi-check-circle', 'bg' => 'bg-success'],
                                'REJECT' => ['color' => 'danger', 'icon' => 'bi-x-circle', 'bg' => 'bg-danger'],
                                'REVISE' => ['color' => 'warning', 'icon' => 'bi-exclamation-circle', 'bg' => 'bg-warning'],
                                'DISPOSE' => ['color' => 'info', 'icon' => 'bi-arrow-right-circle', 'bg' => 'bg-info'],
                                'COMPLETE' => ['color' => 'success', 'icon' => 'bi-check-all', 'bg' => 'bg-success'],
                            ];
                            
                            $config = $actionConfig[$item['action_type']] ?? ['color' => 'secondary', 'icon' => 'bi-circle', 'bg' => 'bg-secondary'];
                            $isLast = ($index === count($workflow) - 1);
                            ?>
                            
                            <div class="timeline-item <?= $item['action_type'] ?>" data-action="<?= strtolower($item['action_type']) ?>">
                                <div class="timeline-marker <?= $config['bg'] ?>">
                                    <i class="bi <?= $config['icon'] ?>"></i>
                                </div>
                                
                                <?php if (!$isLast): ?>
                                <div class="timeline-connector"></div>
                                <?php endif; ?>
                                
                                <div class="timeline-content">
                                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                        <div class="card-body">
                                            <div class="row align-items-start">
                                                <div class="col-md-8">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="<?= $config['bg'] ?> bg-opacity-10 rounded-circle p-2 me-3">
                                                            <i class="bi <?= $config['icon'] ?> text-<?= $config['color'] ?>"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <?= ucfirst(strtolower($item['action_type'])) ?>
                                                                <span class="text-muted">by</span>
                                                                <strong class="text-<?= $config['color'] ?>">
                                                                    <?= esc($item['action_by_name']) ?>
                                                                </strong>
                                                            </h6>
                                                            <div class="text-muted small">
                                                                <span class="badge bg-light text-dark">
                                                                    <?= ucfirst(str_replace('_', ' ', $item['role'])) ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Status Transition -->
                                                    <div class="status-transition mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-light text-dark">
                                                                <?= str_replace('_', ' ', $item['from_status']) ?>
                                                            </span>
                                                            <i class="bi bi-arrow-right mx-2 text-muted"></i>
                                                            <span class="badge bg-<?= $config['color'] ?>">
                                                                <?= str_replace('_', ' ', $item['to_status']) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Keterangan -->
                                                    <?php if (!empty($item['keterangan'])): ?>
                                                    <div class="action-note">
                                                        <div class="bg-light rounded p-3">
                                                            <i class="bi bi-chat-left-text text-muted me-2"></i>
                                                            <small class="text-dark">
                                                                "<?= esc($item['keterangan']) ?>"
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="col-md-4 text-md-end">
                                                    <!-- Timestamp -->
                                                    <div class="timestamp-info">
                                                        <div class="text-primary fw-medium">
                                                            <?= date('d M Y', strtotime($item['created_at'])) ?>
                                                        </div>
                                                        <div class="text-muted">
                                                            <i class="bi bi-clock me-1"></i>
                                                            <?= date('H:i:s', strtotime($item['created_at'])) ?>
                                                        </div>
                                                        
                                                        <!-- Processing Duration -->
                                                        <?php if ($index > 0): ?>
                                                        <?php 
                                                        $prevTime = strtotime($workflow[$index - 1]['created_at']);
                                                        $currentTime = strtotime($item['created_at']);
                                                        $duration = $currentTime - $prevTime;
                                                        $hours = floor($duration / 3600);
                                                        $minutes = floor(($duration % 3600) / 60);
                                                        ?>
                                                        <div class="duration-badge mt-2">
                                                            <span class="badge bg-info bg-opacity-25 text-info">
                                                                <i class="bi bi-hourglass-split me-1"></i>
                                                                <?php if ($hours > 0): ?>
                                                                    <?= $hours ?>h <?= $minutes ?>m
                                                                <?php else: ?>
                                                                    <?= $minutes ?>m
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-clock-history" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="text-muted mt-3">No workflow history available</h4>
                            <p class="text-muted">
                                This letter hasn't been processed yet.
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Next Steps Prediction -->
                <?php if ($surat['status'] !== 'COMPLETED' && $surat['status'] !== 'REJECTED'): ?>
                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="bi bi-compass me-2"></i>
                            Next Steps Prediction
                        </h5>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="next-action mb-3">
                                    <?php 
                                    $nextSteps = [
                                        'SUBMITTED' => 'Menunggu verifikasi dari Staff Umum',
                                        'UNDER_REVIEW' => 'Sedang direview oleh Staff Umum',
                                        'APPROVED_L1' => 'Menunggu persetujuan dari Kepala Bagian TU',
                                        'APPROVED_L2' => 'Menunggu persetujuan final dari Dekan',
                                        'READY_DISPOSISI' => 'Siap untuk didisposisi ke unit terkait',
                                        'IN_PROCESS' => 'Sedang diproses oleh unit terkait',
                                        'NEED_REVISION' => 'Menunggu revisi dari pembuat surat'
                                    ];
                                    ?>
                                    <h6 class="opacity-90"><?= $nextSteps[$surat['status']] ?? 'Status tidak dikenali' ?></h6>
                                </div>
                                
                                <div class="estimated-time">
                                    <small class="opacity-75">
                                        <i class="bi bi-hourglass-split me-1"></i>
                                        Estimated completion: 2-3 business days
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="next-approver">
                                    <?php 
                                    $nextApprovers = [
                                        'SUBMITTED' => 'Staff Umum',
                                        'UNDER_REVIEW' => 'Staff Umum', 
                                        'APPROVED_L1' => 'Kepala Bagian TU',
                                        'APPROVED_L2' => 'Dekan',
                                        'READY_DISPOSISI' => 'Dekan (Disposisi)',
                                        'IN_PROCESS' => 'Unit Terkait'
                                    ];
                                    ?>
                                    <small class="opacity-75">Next approver</small>
                                    <div class="fw-medium"><?= $nextApprovers[$surat['status']] ?? 'N/A' ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Filter timeline by action type
        function filterTimeline(filter) {
            const items = document.querySelectorAll('.timeline-item');
            const buttons = document.querySelectorAll('.btn-group .btn');
            
            // Update button states
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            items.forEach(item => {
                if (filter === 'all') {
                    item.style.display = 'block';
                } else if (filter === 'approvals') {
                    const isApproval = ['approve', 'reject', 'revise'].includes(item.dataset.action);
                    item.style.display = isApproval ? 'block' : 'none';
                } else if (filter === 'actions') {
                    const isAction = ['submit', 'dispose', 'complete'].includes(item.dataset.action);
                    item.style.display = isAction ? 'block' : 'none';
                }
            });
        }
        
        // Export timeline functionality
        function exportTimeline() {
            const printWindow = window.open('', '_blank');
            const content = document.querySelector('.timeline').outerHTML;
            
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Workflow Timeline - <?= esc($surat['nomor_surat']) ?></title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
                        <style>
                            body { font-family: Arial, sans-serif; }
                            .timeline { padding: 20px; }
                        </style>
                    </head>
                    <body>
                        <h2>Workflow Timeline - <?= esc($surat['nomor_surat']) ?></h2>
                        <p><strong>Perihal:</strong> <?= esc($surat['perihal']) ?></p>
                        <hr>
                        ${content}
                    </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.print();
        }
        
        // Auto-refresh timeline every 30 seconds
        setInterval(() => {
            // Only refresh if page is visible
            if (!document.hidden) {
                location.reload();
            }
        }, 30000);
    </script>
    
    <style>
        /* Workflow Progress Styling */
        .progress-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 2rem 1rem;
            overflow-x: auto;
        }
        
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-width: 120px;
            text-align: center;
        }
        
        .step-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            color: #6c757d;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            border: 4px solid #e9ecef;
        }
        
        .progress-step.completed .step-circle {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-color: #28a745;
            transform: scale(1.1);
        }
        
        .progress-step.current .step-circle {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-color: #007bff;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .step-content {
            min-height: 60px;
        }
        
        .step-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            color: #495057;
        }
        
        .step-role {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .progress-step.completed .step-title,
        .progress-step.completed .step-role {
            color: #28a745;
        }
        
        .progress-step.current .step-title {
            color: #007bff;
            font-weight: 700;
        }
        
        .step-connector {
            position: absolute;
            top: 30px;
            left: 80%;
            right: -80%;
            height: 4px;
            background: #e9ecef;
            z-index: -1;
        }
        
        .progress-step.completed .step-connector {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        }
        
        /* Timeline Styling */
        .timeline {
            position: relative;
            padding-left: 0;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 4rem;
            padding-bottom: 2rem;
        }
        
        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            z-index: 2;
        }
        
        .timeline-connector {
            position: absolute;
            left: 24px;
            top: 50px;
            width: 2px;
            height: calc(100% + 2rem);
            background: #e9ecef;
            z-index: 1;
        }
        
        .timeline-content {
            margin-left: 1rem;
            animation: slideInRight 0.5s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .status-transition {
            padding: 0.5rem 0;
        }
        
        .action-note {
            margin-top: 1rem;
        }
        
        .timestamp-info {
            text-align: right;
        }
        
        .duration-badge {
            display: inline-block;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .progress-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .progress-step {
                flex-direction: row;
                min-width: 100%;
                text-align: left;
                padding: 1rem;
                background: #f8f9fa;
                border-radius: 10px;
            }
            
            .step-circle {
                margin-right: 1rem;
                margin-bottom: 0;
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
            
            .step-connector {
                display: none;
            }
            
            .timeline-item {
                padding-left: 3rem;
            }
            
            .timeline-marker {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .timeline-connector {
                left: 19px;
            }
            
            .timestamp-info {
                text-align: left;
                margin-top: 1rem;
            }
        }
    </style>
</body>
</html>