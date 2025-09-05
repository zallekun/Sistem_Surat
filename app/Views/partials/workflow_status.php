<?php 
// Workflow Status Widget - Can be included in any view
$statusConfig = [
    'DRAFT' => ['color' => 'secondary', 'icon' => 'bi-pencil-square', 'text' => 'Draft'],
    'SUBMITTED' => ['color' => 'primary', 'icon' => 'bi-arrow-up-circle', 'text' => 'Submitted'],
    'UNDER_REVIEW' => ['color' => 'info', 'icon' => 'bi-eye', 'text' => 'Under Review'],
    'NEED_REVISION' => ['color' => 'warning', 'icon' => 'bi-exclamation-circle', 'text' => 'Need Revision'],
    'APPROVED_L1' => ['color' => 'success', 'icon' => 'bi-check-circle', 'text' => 'Approved L1'],
    'APPROVED_L2' => ['color' => 'success', 'icon' => 'bi-check-circle-fill', 'text' => 'Approved L2'],
    'READY_DISPOSISI' => ['color' => 'success', 'icon' => 'bi-check2-all', 'text' => 'Ready for Disposition'],
    'IN_PROCESS' => ['color' => 'warning', 'icon' => 'bi-arrow-repeat', 'text' => 'In Process'],
    'COMPLETED' => ['color' => 'success', 'icon' => 'bi-check-all', 'text' => 'Completed'],
    'REJECTED' => ['color' => 'danger', 'icon' => 'bi-x-circle', 'text' => 'Rejected']
];

$currentStatus = $statusConfig[$surat['status']] ?? ['color' => 'secondary', 'icon' => 'bi-circle', 'text' => 'Unknown'];

// Calculate progress percentage
$statusOrder = [
    'DRAFT' => 10,
    'SUBMITTED' => 20,
    'UNDER_REVIEW' => 30,
    'APPROVED_L1' => 50,
    'APPROVED_L2' => 70,
    'READY_DISPOSISI' => 80,
    'IN_PROCESS' => 90,
    'COMPLETED' => 100,
    'REJECTED' => 0,
    'NEED_REVISION' => 15
];

$progress = $statusOrder[$surat['status']] ?? 0;
?>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
    <div class="card-header bg-transparent">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-diagram-3 text-primary me-2"></i>
                Workflow Status
            </h5>
            <a href="<?= base_url('workflow/timeline/' . $surat['id']) ?>" 
               class="btn btn-outline-primary btn-sm">
                <i class="bi bi-clock-history me-1"></i>View Timeline
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Current Status Display -->
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="bg-<?= $currentStatus['color'] ?> bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi <?= $currentStatus['icon'] ?> text-<?= $currentStatus['color'] ?>" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-<?= $currentStatus['color'] ?>"><?= $currentStatus['text'] ?></h6>
                        <small class="text-muted">Current Status</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-md-end">
                    <div class="progress" style="height: 8px; background: #e9ecef; border-radius: 10px;">
                        <div class="progress-bar bg-<?= $currentStatus['color'] ?>" 
                             role="progressbar" 
                             style="width: <?= $progress ?>%; border-radius: 10px;"
                             aria-valuenow="<?= $progress ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted mt-1 d-block"><?= $progress ?>% Complete</small>
                </div>
            </div>
        </div>

        <!-- Mini Timeline Preview -->
        <div class="workflow-mini-timeline">
            <div class="d-flex justify-content-between align-items-center text-center">
                <?php 
                $miniSteps = [
                    ['status' => 'SUBMITTED', 'icon' => 'bi-arrow-up-circle', 'label' => 'Submit'],
                    ['status' => 'APPROVED_L1', 'icon' => 'bi-person-check', 'label' => 'Staff'],
                    ['status' => 'APPROVED_L2', 'icon' => 'bi-person-badge', 'label' => 'Kabag'],
                    ['status' => 'READY_DISPOSISI', 'icon' => 'bi-person-crown', 'label' => 'Dekan'],
                    ['status' => 'COMPLETED', 'icon' => 'bi-check-all', 'label' => 'Done']
                ];
                
                foreach ($miniSteps as $index => $step):
                    $stepProgress = $statusOrder[$step['status']] ?? 0;
                    $isCompleted = $progress >= $stepProgress;
                    $isCurrent = ($surat['status'] === $step['status']);
                ?>
                <div class="mini-step <?= $isCompleted ? 'completed' : '' ?> <?= $isCurrent ? 'current' : '' ?>">
                    <div class="mini-circle">
                        <i class="bi <?= $step['icon'] ?>"></i>
                    </div>
                    <small class="step-label"><?= $step['label'] ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex gap-2 justify-content-center">
                    <?php if ($surat['status'] !== 'COMPLETED' && $surat['status'] !== 'REJECTED'): ?>
                        <?php if (in_array($user_role, ['staff_umum', 'kabag_tu', 'dekan'])): ?>
                            <?php if ($this->canApprove($surat, $user_role)): ?>
                            <button class="btn btn-success btn-sm" onclick="approveWorkflow(<?= $surat['id'] ?>)">
                                <i class="bi bi-check-circle me-1"></i>Approve
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="reviseWorkflow(<?= $surat['id'] ?>)">
                                <i class="bi bi-exclamation-circle me-1"></i>Revise
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="rejectWorkflow(<?= $surat['id'] ?>)">
                                <i class="bi bi-x-circle me-1"></i>Reject
                            </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline-info btn-sm" onclick="refreshWorkflowStatus()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.workflow-mini-timeline {
    padding: 1rem 0;
    position: relative;
}

.workflow-mini-timeline::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 10%;
    right: 10%;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.mini-step {
    flex: 1;
    position: relative;
    z-index: 2;
}

.mini-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.mini-step.completed .mini-circle {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    transform: scale(1.1);
}

.mini-step.current .mini-circle {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    animation: pulse 2s infinite;
}

.step-label {
    display: block;
    font-size: 0.7rem;
    color: #6c757d;
    font-weight: 500;
}

.mini-step.completed .step-label {
    color: #28a745;
    font-weight: 600;
}

.mini-step.current .step-label {
    color: #007bff;
    font-weight: 700;
}

@keyframes pulse {
    0% { transform: scale(1.1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1.1); }
}

/* Responsive */
@media (max-width: 576px) {
    .mini-circle {
        width: 25px;
        height: 25px;
        font-size: 0.7rem;
    }
    
    .step-label {
        font-size: 0.6rem;
    }
    
    .workflow-mini-timeline::before {
        left: 15%;
        right: 15%;
    }
}
</style>

<script>
// Workflow action functions
function approveWorkflow(suratId) {
    Swal.fire({
        title: 'Approve Surat?',
        text: 'Apakah Anda yakin ingin menyetujui surat ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Setuju',
        cancelButtonText: 'Batal',
        input: 'textarea',
        inputPlaceholder: 'Keterangan persetujuan (opsional)...'
    }).then((result) => {
        if (result.isConfirmed) {
            performWorkflowAction('approve', suratId, result.value);
        }
    });
}

function reviseWorkflow(suratId) {
    Swal.fire({
        title: 'Minta Revisi?',
        text: 'Surat akan dikembalikan untuk diperbaiki',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Minta Revisi',
        cancelButtonText: 'Batal',
        input: 'textarea',
        inputPlaceholder: 'Catatan revisi yang diperlukan...',
        inputValidator: (value) => {
            if (!value) {
                return 'Silakan berikan catatan revisi!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            performWorkflowAction('revise', suratId, result.value);
        }
    });
}

function rejectWorkflow(suratId) {
    Swal.fire({
        title: 'Tolak Surat?',
        text: 'Surat akan ditolak dan tidak dapat diproses lebih lanjut',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal',
        input: 'textarea',
        inputPlaceholder: 'Alasan penolakan...',
        inputValidator: (value) => {
            if (!value) {
                return 'Silakan berikan alasan penolakan!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            performWorkflowAction('reject', suratId, result.value);
        }
    });
}

function performWorkflowAction(action, suratId, keterangan) {
    fetch(`<?= base_url('workflow') ?>/${action}/${suratId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            keterangan: keterangan || ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan sistem',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

function refreshWorkflowStatus() {
    location.reload();
}
</script>