<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <?= $this->include('partials/notifications') ?>
    <style>
        /* Step Progress Indicator Styling */
        .step-indicator {
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .step-number {
            width: 35px;
            height: 35px;
            background: #e9ecef;
            color: #6c757d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .step-indicator.active .step-number {
            background: linear-gradient(135deg, #9EBEF5 0%, #667eea 100%);
            color: white;
            box-shadow: 0 4px 8px rgba(158, 190, 245, 0.3);
        }
        
        .step-indicator.completed .step-number {
            background: #28a745;
            color: white;
        }
        
        .step-title {
            font-weight: 500;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .step-indicator.active .step-title {
            color: var(--unjani-primary);
            font-weight: 600;
        }
        
        .step-connector {
            width: 50px;
            height: 2px;
            background: #e9ecef;
            margin: 0 15px;
        }
        
        .step-connector.active {
            background: var(--unjani-primary);
        }
        
        /* Upload Area Styling */
        .upload-area {
            transition: all 0.3s ease;
            cursor: pointer;
            border-color: #dee2e6 !important;
        }
        
        .upload-area:hover {
            border-color: var(--unjani-primary) !important;
            background-color: rgba(158, 190, 245, 0.05);
        }
        
        .upload-area.dragover {
            border-color: var(--unjani-primary) !important;
            background-color: rgba(158, 190, 245, 0.1);
        }
        
        /* Form Enhancements */
        .step-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
        }
        
        .step-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        
        .input-group-text {
            background: rgba(158, 190, 245, 0.1);
            border-color: #dee2e6;
        }
        
        /* Animation for form transitions */
        .step-card {
            animation: fadeInUp 0.5s ease-in-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Priority help text colors */
        .priority-normal { color: #6c757d; }
        .priority-urgent { color: #fd7e14; }
        .priority-sangat-urgent { color: #dc3545; }
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
                    <h1 class="h2"><i class="bi bi-plus-circle me-2"></i>Buat Surat Baru</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?= base_url('surat') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Step Progress Indicator -->
                <div class="card mb-4">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="step-indicator active" id="step-indicator-1">
                                    <span class="step-number">1</span>
                                    <span class="step-title">Info Surat</span>
                                </div>
                                <div class="step-connector" id="connector-1-2"></div>
                                <div class="step-indicator" id="step-indicator-2">
                                    <span class="step-number">2</span>
                                    <span class="step-title">Tujuan & Keterangan</span>
                                </div>
                                <div class="step-connector" id="connector-2-3"></div>
                                <div class="step-indicator" id="step-indicator-3">
                                    <span class="step-number">3</span>
                                    <span class="step-title">Lampiran & Submit</span>
                                </div>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-info-circle"></i> Isi semua data dengan lengkap
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <form id="suratForm" action="<?= base_url('surat/store') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            
                            <!-- Step 1: Info Surat -->
                            <div class="card mb-4 step-card" id="step-1">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-1-circle-fill me-2"></i>Informasi Surat
                                    </h5>
                                    <small class="opacity-75">Data dasar dan identitas surat</small>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="nomor_surat" class="form-label">Nomor Surat</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="bi bi-hash text-primary"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control bg-light <?= isset($validation) && $validation->hasError('nomor_surat') ? 'is-invalid' : '' ?>" 
                                                       id="nomor_surat" 
                                                       name="nomor_surat" 
                                                       value="<?= old('nomor_surat', $nomor_surat) ?>"
                                                       readonly>
                                            </div>
                                            <div class="form-text text-success">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                <strong>Otomatis:</strong> Nomor akan dibuat sistem setelah disimpan
                                            </div>
                                            <?php if (isset($validation) && $validation->hasError('nomor_surat')): ?>
                                                <div class="invalid-feedback"><?= $validation->getError('nomor_surat') ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-calendar-date text-primary"></i>
                                                </span>
                                                <input type="date" 
                                                       class="form-control <?= isset($validation) && $validation->hasError('tanggal_surat') ? 'is-invalid' : '' ?>" 
                                                       id="tanggal_surat" 
                                                       name="tanggal_surat" 
                                                       value="<?= old('tanggal_surat', date('Y-m-d')) ?>"
                                                       required>
                                            </div>
                                            <?php if (isset($validation) && $validation->hasError('tanggal_surat')): ?>
                                                <div class="invalid-feedback"><?= $validation->getError('tanggal_surat') ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="perihal" class="form-label">Perihal Surat <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-card-text text-primary"></i>
                                            </span>
                                            <textarea class="form-control <?= isset($validation) && $validation->hasError('perihal') ? 'is-invalid' : '' ?>" 
                                                      id="perihal" 
                                                      name="perihal" 
                                                      rows="3" 
                                                      placeholder="Contoh: Permohonan Izin Penelitian di Laboratorium Komputer"
                                                      required><?= old('perihal') ?></textarea>
                                        </div>
                                        <div class="form-text">
                                            <i class="bi bi-lightbulb"></i> Gunakan kalimat yang jelas dan spesifik
                                        </div>
                                        <?php if (isset($validation) && $validation->hasError('perihal')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('perihal') ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label for="kategori" class="form-label">Kategori Surat <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-tag text-primary"></i>
                                                </span>
                                                <select class="form-select <?= isset($validation) && $validation->hasError('kategori') ? 'is-invalid' : '' ?>" 
                                                        id="kategori" 
                                                        name="kategori" 
                                                        required>
                                                    <option value="">-- Pilih Kategori --</option>
                                                    <option value="akademik" <?= old('kategori') === 'akademik' ? 'selected' : '' ?>>📚 Akademik</option>
                                                    <option value="kemahasiswaan" <?= old('kategori') === 'kemahasiswaan' ? 'selected' : '' ?>>🎓 Kemahasiswaan</option>
                                                    <option value="kepegawaian" <?= old('kategori') === 'kepegawaian' ? 'selected' : '' ?>>👥 Kepegawaian</option>
                                                    <option value="keuangan" <?= old('kategori') === 'keuangan' ? 'selected' : '' ?>>💰 Keuangan</option>
                                                    <option value="umum" <?= old('kategori') === 'umum' ? 'selected' : '' ?>>📋 Umum</option>
                                                </select>
                                            </div>
                                            <?php if (isset($validation) && $validation->hasError('kategori')): ?>
                                                <div class="invalid-feedback"><?= $validation->getError('kategori') ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="prioritas" class="form-label">Tingkat Prioritas <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                                </span>
                                                <select class="form-select <?= isset($validation) && $validation->hasError('prioritas') ? 'is-invalid' : '' ?>" 
                                                        id="prioritas" 
                                                        name="prioritas" 
                                                        required>
                                                    <option value="">-- Pilih Prioritas --</option>
                                                    <option value="normal" <?= old('prioritas') === 'normal' ? 'selected' : '' ?>>🟢 Normal</option>
                                                    <option value="urgent" <?= old('prioritas') === 'urgent' ? 'selected' : '' ?>>🟡 Urgent</option>
                                                    <option value="sangat_urgent" <?= old('prioritas') === 'sangat_urgent' ? 'selected' : '' ?>>🔴 Sangat Urgent</option>
                                                </select>
                                            </div>
                                            <div class="form-text" id="prioritasHelp"></div>
                                            <?php if (isset($validation) && $validation->hasError('prioritas')): ?>
                                                <div class="invalid-feedback"><?= $validation->getError('prioritas') ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="deadline" class="form-label">Batas Waktu Penyelesaian</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-clock text-info"></i>
                                                </span>
                                                <input type="date" 
                                                       class="form-control" 
                                                       id="deadline" 
                                                       name="deadline" 
                                                       value="<?= old('deadline') ?>"
                                                       min="<?= date('Y-m-d') ?>">
                                            </div>
                                            <div class="form-text">Opsional - Deadline maksimal untuk penyelesaian</div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-outline-primary" onclick="nextStep(2)">
                                            Selanjutnya: Tujuan & Keterangan <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 2: Tujuan & Keterangan -->
                            <div class="card mb-4 step-card" id="step-2" style="display: none;">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-2-circle-fill me-2"></i>Tujuan & Keterangan
                                    </h5>
                                    <small class="opacity-75">Penerima surat dan keterangan tambahan</small>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="tujuan" class="form-label">Ditujukan Kepada <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-person-badge text-primary"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control <?= isset($validation) && $validation->hasError('tujuan') ? 'is-invalid' : '' ?>" 
                                                   id="tujuan" 
                                                   name="tujuan" 
                                                   placeholder="Contoh: Dekan Fakultas Teknik UNJANI"
                                                   value="<?= old('tujuan') ?>"
                                                   required>
                                        </div>
                                        <div class="form-text">
                                            <i class="bi bi-info-circle"></i> Sebutkan jabatan dan unit kerja yang lengkap
                                        </div>
                                        <?php if (isset($validation) && $validation->hasError('tujuan')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('tujuan') ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-4">
                                        <label for="keterangan" class="form-label">Keterangan Tambahan</label>
                                        <div class="input-group">
                                            <span class="input-group-text align-self-start mt-2">
                                                <i class="bi bi-chat-text text-secondary"></i>
                                            </span>
                                            <textarea class="form-control" 
                                                      id="keterangan" 
                                                      name="keterangan" 
                                                      rows="4" 
                                                      placeholder="Tambahkan catatan, konteks, atau informasi tambahan yang diperlukan..."><?= old('keterangan') ?></textarea>
                                        </div>
                                        <div class="form-text">
                                            <i class="bi bi-lightbulb"></i> Opsional - Informasi pendukung untuk memperjelas maksud surat
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                                            <i class="bi bi-arrow-left me-2"></i>Kembali: Info Surat
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="nextStep(3)">
                                            Selanjutnya: Lampiran <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 3: Lampiran & Submit -->
                            <div class="card mb-4 step-card" id="step-3" style="display: none;">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-3-circle-fill me-2"></i>Lampiran & Submit
                                    </h5>
                                    <small class="opacity-75">Upload dokumen dan proses pengajuan</small>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="file_surat" class="form-label">Upload Scan Surat Fisik <span class="text-danger">*</span></label>
                                        <div class="upload-area border-2 border-dashed rounded p-4 text-center" id="uploadArea">
                                            <i class="bi bi-cloud-upload text-primary" style="font-size: 3rem;"></i>
                                            <h6 class="mt-3 mb-2">Drag & Drop atau Klik untuk Upload</h6>
                                            <input type="file" 
                                                   class="form-control <?= isset($validation) && $validation->hasError('file_surat') ? 'is-invalid' : '' ?>" 
                                                   id="file_surat" 
                                                   name="file_surat" 
                                                   accept=".pdf,.jpg,.jpeg,.png"
                                                   required
                                                   style="display: none;">
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('file_surat').click()">
                                                    <i class="bi bi-folder2-open me-1"></i> Pilih File
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-text mt-2">
                                            <div class="row text-center">
                                                <div class="col">
                                                    <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                                                </div>
                                                <div class="col">
                                                    <i class="bi bi-file-earmark-image text-primary"></i> JPG/PNG
                                                </div>
                                                <div class="col">
                                                    <i class="bi bi-hdd text-warning"></i> Max 5MB
                                                </div>
                                            </div>
                                        </div>
                                        <div id="filePreview" class="mt-3" style="display: none;"></div>
                                        <?php if (isset($validation) && $validation->hasError('file_surat')): ?>
                                            <div class="invalid-feedback d-block"><?= $validation->getError('file_surat') ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Submit Options Card -->
                                    <div class="card border-warning mb-4">
                                        <div class="card-header bg-warning bg-opacity-10">
                                            <h6 class="mb-0 text-warning-emphasis">
                                                <i class="bi bi-gear me-2"></i>Opsi Pengajuan
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="submit_type" id="save_draft" value="draft" checked>
                                                        <label class="form-check-label" for="save_draft">
                                                            <strong class="text-secondary">
                                                                <i class="bi bi-save me-1"></i>Simpan sebagai Draft
                                                            </strong>
                                                            <br><small class="text-muted">Surat disimpan dan bisa diedit lagi nanti</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="submit_type" id="auto_submit" value="submit">
                                                        <label class="form-check-label" for="auto_submit">
                                                            <strong class="text-primary">
                                                                <i class="bi bi-send me-1"></i>Simpan & Submit untuk Review
                                                            </strong>
                                                            <br><small class="text-muted">Langsung dikirim ke Staff Umum untuk review</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div id="submitWarning" class="alert alert-info d-none mt-3">
                                                <i class="bi bi-info-circle me-2"></i>
                                                <strong>Perhatian:</strong> Setelah disubmit, surat tidak dapat diedit lagi kecuali dikembalikan untuk revisi.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                                            <i class="bi bi-arrow-left me-2"></i>Kembali: Tujuan
                                        </button>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="<?= base_url('surat') ?>" class="btn btn-outline-danger" id="btnCancel">
                                                <i class="bi bi-x-circle me-2"></i>Batal
                                            </a>
                                            <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                                                <i class="bi bi-save me-2"></i><span id="btn-text">Simpan Sebagai Draft</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-4">
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Program Studi</small>
                                    <strong><?= $prodi['nama_prodi'] ?></strong>
                                    <small class="text-muted d-block"><?= $prodi['kode_prodi'] ?></small>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block">Pembuat</small>
                                    <strong><?= session()->get('user_name') ?></strong>
                                </div>

                                <hr>

                                <h6>Alur Surat:</h6>
                                <ol class="list-group list-group-numbered list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">Draft + Upload</div>
                                            <small>Buat surat & upload scan fisik</small>
                                        </div>
                                        <span class="badge bg-secondary rounded-pill">1</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">Submit</div>
                                            <small>Dikirim untuk review Staff Umum</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">2</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">Review L1</div>
                                            <small>Direview oleh Kabag TU</small>
                                        </div>
                                        <span class="badge bg-warning rounded-pill">3</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">Approval</div>
                                            <small>Disetujui oleh Dekan</small>
                                        </div>
                                        <span class="badge bg-success rounded-pill">4</span>
                                    </li>
                                </ol>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <small><strong>Penting:</strong> Upload scan surat fisik wajib dilakukan untuk melanjutkan proses.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form tracking for unsaved changes
        let formChanged = false;
        let currentStep = 1;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize
            updateStepIndicator(1);
            
            // Track form changes
            const form = document.getElementById('suratForm');
            const inputs = form.querySelectorAll('input, textarea, select');
            
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    formChanged = true;
                });
            });
            
            // Priority help text
            document.getElementById('prioritas').addEventListener('change', function() {
                const helpText = document.getElementById('prioritasHelp');
                const value = this.value;
                
                switch(value) {
                    case 'normal':
                        helpText.innerHTML = '<i class="bi bi-info-circle priority-normal"></i> Proses normal (3-5 hari kerja)';
                        helpText.className = 'form-text priority-normal';
                        break;
                    case 'urgent':
                        helpText.innerHTML = '<i class="bi bi-exclamation-circle priority-urgent"></i> Perlu perhatian khusus (1-2 hari kerja)';
                        helpText.className = 'form-text priority-urgent';
                        break;
                    case 'sangat_urgent':
                        helpText.innerHTML = '<i class="bi bi-exclamation-triangle-fill priority-sangat-urgent"></i> <strong>Sangat mendesak!</strong> (maksimal 24 jam)';
                        helpText.className = 'form-text priority-sangat-urgent';
                        break;
                    default:
                        helpText.innerHTML = '';
                }
            });
            
            // Submit type change
            document.querySelectorAll('input[name="submit_type"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const btnText = document.getElementById('btn-text');
                    const submitWarning = document.getElementById('submitWarning');
                    
                    if (this.value === 'submit') {
                        btnText.textContent = 'Simpan & Submit untuk Review';
                        submitWarning.classList.remove('d-none');
                    } else {
                        btnText.textContent = 'Simpan Sebagai Draft';
                        submitWarning.classList.add('d-none');
                    }
                });
            });
            
            // File upload handling
            const fileInput = document.getElementById('file_surat');
            const uploadArea = document.getElementById('uploadArea');
            const filePreview = document.getElementById('filePreview');
            
            // Click to upload
            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            
            // Drag & drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelection(files[0]);
                }
            });
            
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFileSelection(this.files[0]);
                }
            });
            
            function handleFileSelection(file) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                
                if (!allowedTypes.includes(file.type)) {
                    SuratNotification.error('File Tidak Valid', 'Hanya file PDF, JPG, atau PNG yang diperbolehkan.');
                    fileInput.value = '';
                    return;
                }
                
                if (file.size > maxSize) {
                    SuratNotification.error('File Terlalu Besar', 'Ukuran file maksimal adalah 5MB.');
                    fileInput.value = '';
                    return;
                }
                
                // Show preview
                filePreview.innerHTML = `
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>
                            <strong>${file.name}</strong><br>
                            <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                        </div>
                    </div>
                `;
                filePreview.style.display = 'block';
            }
            
            // Unsaved changes warning
            document.getElementById('btnCancel').addEventListener('click', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    SuratNotification.confirm(
                        'Keluar Tanpa Menyimpan?',
                        'Perubahan yang Anda buat akan hilang. Apakah yakin ingin keluar?',
                        'Ya, Keluar',
                        'Batal'
                    ).then(confirmed => {
                        if (confirmed) {
                            window.location.href = this.href;
                        }
                    });
                }
            });
            
            // Form validation before submit
            form.addEventListener('submit', function(e) {
                if (!validateCurrentStep()) {
                    e.preventDefault();
                }
            });
        });
        
        // Step navigation functions
        function nextStep(step) {
            if (!validateCurrentStep()) return;
            
            showStep(step);
            updateStepIndicator(step);
            currentStep = step;
        }
        
        function prevStep(step) {
            showStep(step);
            updateStepIndicator(step);
            currentStep = step;
        }
        
        function showStep(stepNumber) {
            // Hide all steps
            for (let i = 1; i <= 3; i++) {
                const stepElement = document.getElementById(`step-${i}`);
                if (stepElement) {
                    stepElement.style.display = 'none';
                }
            }
            
            // Show current step
            const currentStepElement = document.getElementById(`step-${stepNumber}`);
            if (currentStepElement) {
                currentStepElement.style.display = 'block';
                currentStepElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        
        function updateStepIndicator(activeStep) {
            // Reset all indicators
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById(`step-indicator-${i}`);
                const connector = document.getElementById(`connector-${i}-${i + 1}`);
                
                indicator.classList.remove('active', 'completed');
                if (connector) connector.classList.remove('active');
                
                if (i < activeStep) {
                    indicator.classList.add('completed');
                    if (connector) connector.classList.add('active');
                } else if (i === activeStep) {
                    indicator.classList.add('active');
                }
            }
        }
        
        function validateCurrentStep() {
            let isValid = true;
            const step = currentStep;
            
            if (step === 1) {
                // Validate Step 1
                const tanggal = document.getElementById('tanggal_surat').value;
                const perihal = document.getElementById('perihal').value.trim();
                const kategori = document.getElementById('kategori').value;
                const prioritas = document.getElementById('prioritas').value;
                
                if (!tanggal || !perihal || !kategori || !prioritas) {
                    SuratNotification.warning('Data Belum Lengkap', 'Mohon lengkapi semua field yang wajib diisi pada Step 1.');
                    isValid = false;
                }
            } else if (step === 2) {
                // Validate Step 2
                const tujuan = document.getElementById('tujuan').value.trim();
                
                if (!tujuan) {
                    SuratNotification.warning('Data Belum Lengkap', 'Mohon isi field "Ditujukan Kepada" pada Step 2.');
                    isValid = false;
                }
            } else if (step === 3) {
                // Validate Step 3
                const fileInput = document.getElementById('file_surat');
                
                if (!fileInput.files.length) {
                    SuratNotification.warning('File Belum Diupload', 'Mohon upload scan surat fisik pada Step 3.');
                    isValid = false;
                }
            }
            
            return isValid;
        }
    </script>
</body>
</html>