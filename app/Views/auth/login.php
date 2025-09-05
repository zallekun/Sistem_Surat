<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card login-card">
                    <div class="login-header">
                        <div class="mb-3">
                            <img src="<?= base_url('assets/images/logos/logo_unjani.png') ?>" alt="Logo UNJANI" class="logo">
                        </div>
                        <h4 class="mb-0">Sistem Surat Menyurat</h4>
                        <div class="university-name">Universitas Jenderal Ahmad Yani</div>
                        <div class="system-name">Fakultas Sains dan Informatika</div>
                    </div>
                    <div class="login-body">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('info')): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <?= session()->getFlashdata('info') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('auth/authenticate') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                           id="email" 
                                           name="email" 
                                           placeholder="Masukkan email Anda"
                                           value="<?= old('email', isset($old_input['email']) ? $old_input['email'] : '') ?>"
                                           required>
                                    <?php if (isset($validation) && $validation->hasError('email')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('email') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Masukkan password Anda"
                                           required>
                                    <?php if (isset($validation) && $validation->hasError('password')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('password') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Login
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">
                        
                        <div class="demo-accounts p-3">
                            <div class="text-center mb-2">
                                <small class="text-primary fw-bold">
                                    <i class="bi bi-info-circle me-1"></i>Demo Accounts
                                </small>
                            </div>
                            <small class="text-muted">
                                <strong>Admin Prodi:</strong> admin.ti@universitas.ac.id / adminprodi123<br>
                                <strong>Staff Umum:</strong> staff.umum@universitas.ac.id / staffumum123<br>
                                <strong>Kabag TU:</strong> kabag.tu@universitas.ac.id / kabagtu123<br>
                                <strong>Dekan:</strong> dekan@universitas.ac.id / dekan123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>