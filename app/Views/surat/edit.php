<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?= $this->include('partials/sidebar') ?>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="bi bi-pencil me-2"></i>Edit Surat</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?= base_url('surat/' . $surat['id']) ?>" class="btn btn-outline-secondary">
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

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Form Edit Surat</h5>
                            </div>
                            <div class="card-body">
                                <form action="<?= base_url('surat/' . $surat['id'] . '/update') ?>" method="post" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="nomor_surat" class="form-label">Nomor Surat</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="nomor_surat" 
                                                   name="nomor_surat" 
                                                   value="<?= esc($surat['nomor_surat']) ?>"
                                                   readonly>
                                            <div class="form-text">Nomor surat tidak dapat diubah</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                            <input type="date" 
                                                   class="form-control <?= isset($validation) && $validation->hasError('tanggal_surat') ? 'is-invalid' : '' ?>" 
                                                   id="tanggal_surat" 
                                                   name="tanggal_surat" 
                                                   value="<?= old('tanggal_surat', $surat['tanggal_surat']) ?>"
                                                   required>
                                            <?php if (isset($validation) && $validation->hasError('tanggal_surat')): ?>
                                                <div class="invalid-feedback"><?= $validation->getError('tanggal_surat') ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="perihal" class="form-label">Perihal <span class="text-danger">*</span></label>
                                        <textarea class="form-control <?= isset($validation) && $validation->hasError('perihal') ? 'is-invalid' : '' ?>" 
                                                  id="perihal" 
                                                  name="perihal" 
                                                  rows="3" 
                                                  placeholder="Masukkan perihal surat..."
                                                  required><?= old('perihal', $surat['perihal']) ?></textarea>
                                        <?php if (isset($validation) && $validation->hasError('perihal')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('perihal') ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <select class="form-select <?= isset($validation) && $validation->hasError('kategori') ? 'is-invalid' : '' ?>" 
                                                    id="kategori" 
                                                    name="kategori" 
                                                    required>
                                                <option value="">Pilih Kategori</option>
                                                <option value="akademik" <?= old('kategori', $surat['kategori']) === 'akademik' ? 'selected' : '' ?>>Akademik</option>
                                                <option value="kemahasiswaan" <?= old('kategori', $surat['kategori']) === 'kemahasiswaan' ? 'selected' : '' ?>>Kemahasiswaan</option>
                                                <option value="kepegawaian" <?= old('kategori', $surat['kategori']) === 'kepegawaian' ? 'selected' : '' ?>>Kepegawaian</option>
                                                <option value="keuangan" <?= old('kategori', $surat['kategori']) === 'keuangan' ? 'selected' : '' ?>>Keuangan</option>
                                                <option value="umum" <?= old('kategori', $surat['kategori']) === 'umum' ? 'selected' : '' ?>>Umum</option>
                                            </select>
                                            <?php if (isset($validation) && $validation->hasError('kategori')): ?>
                                                <div class="invalid-feedback"><?= $validation->getError('kategori') ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="prioritas" class="form-label">Prioritas <span class="text-danger">*</span></label>
                                            <select class="form-select <?= isset($validation) && $validation->hasError('prioritas') ? 'is-invalid' : '' ?>" 
                                                    id="prioritas" 
                                                    name="prioritas" 
                                                    required>
                                                <option value="">Pilih Prioritas</option>
                                                <option value="normal" <?= old('prioritas', $surat['prioritas']) === 'normal' ? 'selected' : '' ?>>Normal</option>
                                                <option value="urgent" <?= old('prioritas', $surat['prioritas']) === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                                <option value="sangat_urgent" <?= old('prioritas', $surat['prioritas']) === 'sangat_urgent' ? 'selected' : '' ?>>Sangat Urgent</option>
                                            </select>
                                            <?php if (isset($validation) && $validation->hasError('prioritas')): ?>
                                                <div class="invalid-feedback"><?= $validation->getError('prioritas') ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="deadline" class="form-label">Batas Waktu</label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="deadline" 
                                                   name="deadline" 
                                                   value="<?= old('deadline', $surat['deadline']) ?>">
                                            <div class="form-text">Opsional</div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tujuan" class="form-label">Tujuan <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control <?= isset($validation) && $validation->hasError('tujuan') ? 'is-invalid' : '' ?>" 
                                               id="tujuan" 
                                               name="tujuan" 
                                               placeholder="Contoh: Dekan Fakultas Teknik"
                                               value="<?= old('tujuan', $surat['tujuan']) ?>"
                                               required>
                                        <?php if (isset($validation) && $validation->hasError('tujuan')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('tujuan') ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan Tambahan</label>
                                        <textarea class="form-control" 
                                                  id="keterangan" 
                                                  name="keterangan" 
                                                  rows="3" 
                                                  placeholder="Catatan atau keterangan tambahan..."><?= old('keterangan', $surat['keterangan']) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="file_surat_revisi" class="form-label">Upload File Revisi <span class="text-info">(Opsional)</span></label>
                                        <input type="file" 
                                               class="form-control <?= isset($validation) && $validation->hasError('file_surat_revisi') ? 'is-invalid' : '' ?>" 
                                               id="file_surat_revisi" 
                                               name="file_surat_revisi" 
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Upload file baru jika ada revisi. File lama akan tetap tersimpan untuk history. Format: PDF, JPG, PNG (Max: 5MB)
                                        </div>
                                        <?php if (isset($validation) && $validation->hasError('file_surat_revisi')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('file_surat_revisi') ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="<?= base_url('surat/' . $surat['id']) ?>" class="btn btn-outline-secondary me-md-2">
                                            <i class="bi bi-x-circle me-2"></i>Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Surat</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Status Saat Ini</small>
                                    <span class="badge bg-warning"><?= str_replace('_', ' ', $surat['status']) ?></span>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Dibuat</small>
                                    <strong><?= date('d/m/Y H:i', strtotime($surat['created_at'])) ?></strong>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Terakhir Diubah</small>
                                    <strong><?= date('d/m/Y H:i', strtotime($surat['updated_at'] ?? $surat['created_at'])) ?></strong>
                                </div>

                                <hr>

                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <small><strong>Perhatian:</strong> Surat hanya dapat diedit dalam status DRAFT atau NEED_REVISION.</small>
                                </div>
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