<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background-color: #f8f9ff;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #0056b3;
            background-color: #e6f2ff;
        }
        .upload-area.dragover {
            border-color: #28a745;
            background-color: #e8f5e8;
        }
        .file-list {
            max-height: 400px;
            overflow-y: auto;
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
                    <h1 class="h2"><i class="bi bi-cloud-upload me-2"></i>Upload File</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?= base_url('surat/' . $surat['id']) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Surat
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
                    <div class="col-lg-8">
                        <!-- Surat Info -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Informasi Surat</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Nomor Surat:</strong><br>
                                        <span class="text-muted"><?= esc($surat['nomor_surat']) ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Status:</strong><br>
                                        <span class="badge bg-primary"><?= str_replace('_', ' ', $surat['status']) ?></span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <strong>Perihal:</strong><br>
                                    <span class="text-muted"><?= esc($surat['perihal']) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Area -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Upload File Lampiran</h5>
                            </div>
                            <div class="card-body">
                                <form id="uploadForm" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    
                                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                                        <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #007bff; margin-bottom: 1rem;"></i>
                                        <h4>Pilih atau Drop File Di Sini</h4>
                                        <p class="text-muted mb-3">
                                            Drag & drop file atau klik untuk memilih file
                                        </p>
                                        <div class="small text-muted">
                                            <strong>Format yang didukung:</strong> PDF, DOC, DOCX, JPG, PNG, GIF<br>
                                            <strong>Maksimal ukuran:</strong> 10MB per file
                                        </div>
                                    </div>
                                    
                                    <input type="file" id="fileInput" name="file" style="display: none;" 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                                    
                                    <div class="mt-3">
                                        <label for="keterangan" class="form-label">Keterangan (opsional)</label>
                                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2" 
                                                  placeholder="Deskripsi atau keterangan tentang file..."></textarea>
                                    </div>
                                    
                                    <div class="d-none mt-3" id="fileInfo">
                                        <div class="alert alert-info">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="bi bi-file-earmark me-2"></i>
                                                    <span id="fileName"></span>
                                                    <small class="text-muted d-block" id="fileSize"></small>
                                                </div>
                                                <button type="button" class="btn-close" onclick="resetFileInput()"></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                        <button type="button" class="btn btn-outline-secondary me-md-2" onclick="resetFileInput()">
                                            <i class="bi bi-x-circle me-2"></i>Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                                            <i class="bi bi-cloud-upload me-2"></i>Upload File
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- File List -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-files me-2"></i>File Lampiran</h6>
                            </div>
                            <div class="card-body">
                                <div class="file-list" id="fileList">
                                    <?php if (empty($lampiran)): ?>
                                        <div class="text-center text-muted" id="emptyState">
                                            <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="mt-2 mb-0">Belum ada file</p>
                                            <small>Upload file untuk melampirkan ke surat</small>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($lampiran as $l): ?>
                                        <div class="file-item mb-2 p-3 border rounded" data-id="<?= $l['id'] ?>">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                                        <strong class="small"><?= esc($l['nama_asli']) ?></strong>
                                                    </div>
                                                    <div class="small text-muted">
                                                        Ver. <?= $l['versi'] ?> • 
                                                        <?= number_format($l['ukuran_file'] / 1024, 1) ?> KB<br>
                                                        <?= date('d M Y H:i', strtotime($l['created_at'])) ?>
                                                        <?php if ($l['keterangan']): ?>
                                                        <br><em><?= esc($l['keterangan']) ?></em>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('file/download/' . $l['id']) ?>" 
                                                       class="btn btn-outline-primary btn-sm" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <?php if (in_array($surat['status'], ['DRAFT', 'NEED_REVISION']) && 
                                                              $surat['created_by'] == session()->get('user_id')): ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                                            onclick="deleteFile(<?= $l['id'] ?>)" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        
                        <!-- Upload Guidelines -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Panduan Upload</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        File akan disimpan dengan sistem versioning
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Upload ulang akan membuat versi baru
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        History file dapat dilihat
                                    </li>
                                    <li class="mb-0">
                                        <i class="bi bi-info-circle text-info me-2"></i>
                                        File hanya bisa diupload saat status DRAFT atau NEED_REVISION
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const uploadBtn = document.getElementById('uploadBtn');
        const uploadForm = document.getElementById('uploadForm');

        // Drag and drop handlers
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });

        fileInput.addEventListener('change', handleFileSelect);

        function handleFileSelect() {
            const file = fileInput.files[0];
            if (file) {
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.classList.remove('d-none');
                uploadBtn.disabled = false;
            }
        }

        function resetFileInput() {
            fileInput.value = '';
            fileInfo.classList.add('d-none');
            uploadBtn.disabled = true;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('keterangan', document.getElementById('keterangan').value);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Uploading...';

            fetch('<?= base_url('file/upload/' . $surat['id']) ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    SuratNotification.success('Upload Berhasil!', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    SuratNotification.error('Upload Gagal!', data.message);
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = '<i class="bi bi-cloud-upload me-2"></i>Upload File';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                SuratNotification.error('Upload Error!', 'Terjadi kesalahan saat upload file');
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="bi bi-cloud-upload me-2"></i>Upload File';
            });
        });

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
</body>
</html>