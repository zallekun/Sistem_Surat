<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <?= $this->include('partials/notifications') ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
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

                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><i class="bi bi-person-circle me-2"></i>Profile Pengguna</h2>
                        <p class="text-muted mb-0">Kelola informasi profile Anda</p>
                    </div>
                </div>

                <div class="row">
                    <!-- Profile Avatar Section -->
                    <div class="col-12 mb-4">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <div class="card-body text-center py-4">
                                <div class="profile-avatar-section">
                                    <div class="avatar-container position-relative d-inline-block">
                                        <div class="profile-avatar" id="profileAvatar">
                                            <?php 
                                            $avatarUrl = $user['avatar'] ?? null;
                                            $initials = strtoupper(substr($user['nama'] ?? 'U', 0, 1) . substr(strrchr($user['nama'] ?? 'User', ' '), 1, 1));
                                            ?>
                                            <?php if ($avatarUrl && file_exists(FCPATH . 'uploads/avatars/' . $avatarUrl)): ?>
                                                <img src="<?= base_url('uploads/avatars/' . $avatarUrl) ?>" alt="Avatar" class="avatar-image">
                                            <?php else: ?>
                                                <div class="avatar-placeholder">
                                                    <span class="avatar-initials"><?= $initials ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-sm btn-light avatar-upload-btn" onclick="triggerAvatarUpload()" title="Change Avatar">
                                            <i class="bi bi-camera"></i>
                                        </button>
                                        <input type="file" id="avatarUpload" accept="image/*" style="display: none;" onchange="handleAvatarUpload(event)">
                                    </div>
                                    <h4 class="mt-3 mb-1"><?= esc($user['nama']) ?></h4>
                                    <div class="user-role-badges mb-2">
                                        <?php 
                                        $roleColors = [
                                            'admin_prodi' => 'primary',
                                            'staff_umum' => 'info', 
                                            'kabag_tu' => 'warning',
                                            'dekan' => 'success',
                                            'wd_akademik' => 'success',
                                            'wd_kemahasiswa' => 'success',
                                            'wd_umum' => 'success',
                                            'kaur_keuangan' => 'secondary'
                                        ];
                                        $roleColor = $roleColors[$user['role']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-white text-<?= $roleColor ?> px-3 py-2" style="font-size: 0.9rem;">
                                            <i class="bi bi-person-badge me-1"></i><?= ucwords(str_replace('_', ' ', $user['role'])) ?>
                                        </span>
                                    </div>
                                    <div class="user-info-quick">
                                        <small class="opacity-75">
                                            <i class="bi bi-envelope me-1"></i><?= esc($user['email']) ?>
                                            <?php if (!empty($user['nip'])): ?>
                                            &nbsp;•&nbsp;
                                            <i class="bi bi-card-text me-1"></i>NIP: <?= esc($user['nip']) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Information -->
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0"><i class="bi bi-person-fill me-2 text-primary"></i>Informasi Profile</h5>
                            </div>
                            <div class="card-body">
                                <form action="<?= base_url('profile/update') ?>" method="post">
                                    <?= csrf_field() ?>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="nama" class="form-label">
                                                <i class="bi bi-person text-primary me-2"></i>Nama Lengkap
                                            </label>
                                            <input type="text" class="form-control" id="nama" name="nama" 
                                                   value="<?= old('nama', $user['nama'] ?? '') ?>" required
                                                   style="border-radius: 10px; padding-left: 3rem;">
                                            <i class="bi bi-person position-absolute" style="left: 1rem; top: 2.7rem; color: #6c757d;"></i>
                                        </div>
                                        <div class="col-md-6 position-relative">
                                            <label for="email" class="form-label">
                                                <i class="bi bi-envelope text-primary me-2"></i>Email
                                            </label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= old('email', $user['email'] ?? '') ?>" readonly
                                                   style="border-radius: 10px; padding-left: 3rem; background-color: #f8f9fa;">
                                            <i class="bi bi-envelope position-absolute" style="left: 1rem; top: 2.7rem; color: #6c757d;"></i>
                                            <small class="text-muted">Email tidak dapat diubah untuk keamanan</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 position-relative">
                                            <label for="nip" class="form-label">
                                                <i class="bi bi-card-text text-primary me-2"></i>NIP
                                            </label>
                                            <input type="text" class="form-control" id="nip" name="nip" 
                                                   value="<?= old('nip', $user['nip'] ?? '') ?>" readonly
                                                   style="border-radius: 10px; padding-left: 3rem; background-color: #f8f9fa;">
                                            <i class="bi bi-card-text position-absolute" style="left: 1rem; top: 2.7rem; color: #6c757d;"></i>
                                            <small class="text-muted">NIP tidak dapat diubah</small>
                                        </div>
                                        <div class="col-md-6 position-relative">
                                            <label for="role" class="form-label">
                                                <i class="bi bi-person-badge text-primary me-2"></i>Role/Jabatan
                                            </label>
                                            <div class="role-display" style="border-radius: 10px; padding: 0.75rem 1rem 0.75rem 3rem; background-color: #f8f9fa; border: 1px solid #ced4da; position: relative;">
                                                <i class="bi bi-person-badge position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                                                <span class="fw-medium"><?= ucwords(str_replace('_', ' ', $user['role'] ?? '')) ?></span>
                                                <span class="badge bg-<?= $roleColor ?> ms-2">Active</span>
                                            </div>
                                            <small class="text-muted">Role ditentukan oleh administrator sistem</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 position-relative">
                                            <label for="fakultas" class="form-label">
                                                <i class="bi bi-building text-primary me-2"></i>Fakultas
                                            </label>
                                            <div style="border-radius: 10px; padding: 0.75rem 1rem 0.75rem 3rem; background-color: #f8f9fa; border: 1px solid #ced4da; position: relative;">
                                                <i class="bi bi-building position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                                                <?= $user['fakultas_nama'] ?? 'Tidak ditentukan' ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 position-relative">
                                            <label for="prodi" class="form-label">
                                                <i class="bi bi-mortarboard text-primary me-2"></i>Program Studi
                                            </label>
                                            <div style="border-radius: 10px; padding: 0.75rem 1rem 0.75rem 3rem; background-color: #f8f9fa; border: 1px solid #ced4da; position: relative;">
                                                <i class="bi bi-mortarboard position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                                                <?= $user['prodi_nama'] ?? 'Tidak ditentukan' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative">
                                        <label for="alamat" class="form-label">
                                            <i class="bi bi-geo-alt text-primary me-2"></i>Alamat
                                        </label>
                                        <textarea class="form-control" id="alamat" name="alamat" rows="3" 
                                                  style="border-radius: 10px; padding-left: 3rem; resize: vertical;"
                                                  placeholder="Masukkan alamat lengkap..."><?= old('alamat', $user['alamat'] ?? '') ?></textarea>
                                        <i class="bi bi-geo-alt position-absolute" style="left: 1rem; top: 2.7rem; color: #6c757d;"></i>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 position-relative">
                                            <label for="telepon" class="form-label">
                                                <i class="bi bi-phone text-primary me-2"></i>Nomor Telepon
                                            </label>
                                            <input type="tel" class="form-control" id="telepon" name="telepon" 
                                                   value="<?= old('telepon', $user['telepon'] ?? '') ?>"
                                                   style="border-radius: 10px; padding-left: 3rem;"
                                                   placeholder="+62 812-3456-7890"
                                                   pattern="[+]?[0-9\s\-\(\)]{10,15}">
                                            <i class="bi bi-phone position-absolute" style="left: 1rem; top: 2.7rem; color: #6c757d;"></i>
                                            <small class="text-muted">Format: +62 atau 08xx-xxxx-xxxx</small>
                                        </div>
                                        <div class="col-md-6 position-relative">
                                            <label for="whatsapp" class="form-label">
                                                <i class="bi bi-whatsapp text-success me-2"></i>WhatsApp (Opsional)
                                            </label>
                                            <input type="tel" class="form-control" id="whatsapp" name="whatsapp" 
                                                   value="<?= old('whatsapp', $user['whatsapp'] ?? '') ?>"
                                                   style="border-radius: 10px; padding-left: 3rem;"
                                                   placeholder="+62 812-3456-7890">
                                            <i class="bi bi-whatsapp position-absolute" style="left: 1rem; top: 2.7rem; color: #25D366;"></i>
                                            <small class="text-muted">Untuk notifikasi WhatsApp</small>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                                        </button>
                                        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left me-2"></i>Kembali
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-shield-lock me-2 text-warning"></i>
                                    <span style="color: #8b5a2b;">Keamanan Password</span>
                                </h5>
                                <small class="text-muted" style="color: #8b5a2b !important; opacity: 0.8;">Pastikan akun Anda tetap aman</small>
                            </div>
                            <div class="card-body" style="background: white; border-radius: 0 0 15px 15px;">
                                <form action="<?= base_url('profile/change-password') ?>" method="post" id="password-form">
                                    <?= csrf_field() ?>
                                    
                                    <div class="mb-3 position-relative">
                                        <label for="current_password" class="form-label">
                                            <i class="bi bi-key text-warning me-2"></i>Password Lama
                                        </label>
                                        <input type="password" class="form-control" id="current_password" 
                                               name="current_password" required
                                               style="border-radius: 10px; padding-left: 3rem;"
                                               placeholder="Masukkan password lama">
                                        <i class="bi bi-key position-absolute" style="left: 1rem; top: 2.7rem; color: #6c757d;"></i>
                                    </div>

                                    <div class="mb-3 position-relative">
                                        <label for="new_password" class="form-label">
                                            <i class="bi bi-shield-lock text-success me-2"></i>Password Baru
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password" 
                                                   name="new_password" required minlength="6"
                                                   style="border-radius: 10px 0 0 10px; padding-left: 3rem;"
                                                   placeholder="Minimal 6 karakter">
                                            <button class="btn btn-outline-secondary" type="button" 
                                                    style="border-radius: 0 10px 10px 0;" 
                                                    onclick="togglePasswordVisibility('new_password')">
                                                <i class="bi bi-eye" id="new_password_eye"></i>
                                            </button>
                                        </div>
                                        <i class="bi bi-shield-lock position-absolute" style="left: 1rem; top: 2.7rem; color: #6c757d; z-index: 3;"></i>
                                        <div id="password-requirements" class="mt-2" style="font-size: 0.8rem;">
                                            <div class="requirement" id="req-length">
                                                <i class="bi bi-x-circle text-danger"></i> Minimal 6 karakter
                                            </div>
                                            <div class="requirement" id="req-uppercase">
                                                <i class="bi bi-x-circle text-danger"></i> Huruf besar (A-Z)
                                            </div>
                                            <div class="requirement" id="req-lowercase">
                                                <i class="bi bi-x-circle text-danger"></i> Huruf kecil (a-z)
                                            </div>
                                            <div class="requirement" id="req-number">
                                                <i class="bi bi-x-circle text-danger"></i> Angka (0-9)
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative">
                                        <label for="confirm_password" class="form-label">
                                            <i class="bi bi-check2-circle text-info me-2"></i>Konfirmasi Password
                                        </label>
                                        <input type="password" class="form-control" id="confirm_password" 
                                               name="confirm_password" required
                                               style="border-radius: 10px; padding-left: 3rem;"
                                               placeholder="Ulangi password baru">
                                        <i class="bi bi-check2-circle position-absolute" style="left: 1rem; top: 2.7rem; color: #6c757d;"></i>
                                        <div id="password-match" class="mt-1" style="font-size: 0.8rem; display: none;"></div>
                                    </div>

                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-key me-2"></i>Ubah Password
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Enhanced Profile Stats -->
                        <div class="card border-0 shadow-sm mt-3" style="border-radius: 15px;">
                            <div class="card-header bg-transparent">
                                <h6 class="mb-0">
                                    <i class="bi bi-graph-up text-primary me-2"></i>
                                    Statistik Aktivitas
                                </h6>
                                <small class="text-muted">Performance bulan ini</small>
                            </div>
                            <div class="card-body">
                                <!-- Main Stats -->
                                <div class="row g-3 text-center mb-3">
                                    <div class="col-6">
                                        <div class="stat-item p-3" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 10px;">
                                            <div class="d-flex align-items-center justify-content-center mb-2">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="bi bi-file-plus text-primary"></i>
                                                </div>
                                                <h4 class="text-primary mb-0"><?= $stats['total_created'] ?? 12 ?></h4>
                                            </div>
                                            <small class="text-muted fw-medium">Surat Dibuat</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-item p-3" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); border-radius: 10px;">
                                            <div class="d-flex align-items-center justify-content-center mb-2">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="bi bi-check-circle text-success"></i>
                                                </div>
                                                <h4 class="text-success mb-0"><?= $stats['total_approved'] ?? 8 ?></h4>
                                            </div>
                                            <small class="text-muted fw-medium">Surat Disetujui</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Additional Stats -->
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="mini-stat">
                                            <h6 class="text-warning mb-0"><?= $stats['pending_approval'] ?? 3 ?></h6>
                                            <small class="text-muted" style="font-size: 0.7rem;">Menunggu</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mini-stat">
                                            <h6 class="text-info mb-0"><?= $stats['avg_processing_days'] ?? 2.5 ?></h6>
                                            <small class="text-muted" style="font-size: 0.7rem;">Avg Days</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mini-stat">
                                            <h6 class="text-danger mb-0"><?= $stats['rejected'] ?? 1 ?></h6>
                                            <small class="text-muted" style="font-size: 0.7rem;">Ditolak</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Performance Indicator -->
                                <hr class="my-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Performance</small>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                                        </div>
                                        <small class="text-success fw-medium">85%</small>
                                        <i class="bi bi-arrow-up text-success ms-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Last Login & Activity Info -->
                        <div class="card border-0 shadow-sm mt-3" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <div class="card-body text-center py-4">
                                <div class="activity-info">
                                    <div class="mb-3">
                                        <i class="bi bi-clock-history" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                    </div>
                                    <h6 class="mb-2">Terakhir Login</h6>
                                    <div class="mb-2">
                                        <small class="opacity-90">
                                            <?= date('d M Y', strtotime($user['last_login'] ?? 'now')) ?>
                                        </small><br>
                                        <small class="opacity-75">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('H:i', strtotime($user['last_login'] ?? 'now')) ?>
                                        </small>
                                    </div>
                                    
                                    <!-- Login Streak -->
                                    <div class="login-streak mt-3 pt-3 border-top border-light border-opacity-25">
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="opacity-75">Login Streak</small>
                                                <div class="fw-bold"><?= $stats['login_streak'] ?? 5 ?> hari</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="opacity-75">Total Login</small>
                                                <div class="fw-bold"><?= $stats['total_logins'] ?? 47 ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Security & Preferences -->
                        <div class="card border-0 shadow-sm mt-3" style="border-radius: 15px;">
                            <div class="card-header bg-transparent">
                                <h6 class="mb-0">
                                    <i class="bi bi-gear text-primary me-2"></i>
                                    Pengaturan Cepat
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary btn-sm" onclick="exportUserData()">
                                        <i class="bi bi-download me-2"></i>Export Data Saya
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="viewActivityLog()">
                                        <i class="bi bi-activity me-2"></i>Lihat Log Aktivitas
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="notificationSettings()">
                                        <i class="bi bi-bell me-2"></i>Pengaturan Notifikasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        /* Profile Page Enhancements */
        .profile-avatar-section .avatar-container {
            margin-bottom: 1rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .avatar-initials {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .avatar-upload-btn {
            position: absolute;
            bottom: -5px;
            right: -5px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            border: 3px solid white;
        }
        
        .user-role-badges .badge {
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .requirement {
            transition: all 0.3s ease;
        }
        
        .requirement.valid {
            color: #28a745;
        }
        
        .requirement.valid i {
            color: #28a745;
        }
        
        .requirement.invalid {
            color: #dc3545;
        }
        
        .requirement.invalid i {
            color: #dc3545;
        }
        
        .stat-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .mini-stat {
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .mini-stat:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .activity-info {
            position: relative;
        }
        
        .login-streak {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.75rem;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
            
            .avatar-initials {
                font-size: 2rem;
            }
            
            .avatar-upload-btn {
                width: 35px;
                height: 35px;
            }
            
            .user-role-badges {
                margin-bottom: 1rem;
            }
            
            .stat-item {
                padding: 1rem !important;
            }
        }
        
        /* Animation effects */
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
        
        .card {
            animation: fadeInUp 0.5s ease-out;
        }
        
        .card:nth-child(2) {
            animation-delay: 0.1s;
        }
        
        .card:nth-child(3) {
            animation-delay: 0.2s;
        }
        
        .card:nth-child(4) {
            animation-delay: 0.3s;
        }
    </style>
    
    <script>
        // Avatar upload functionality
        function triggerAvatarUpload() {
            document.getElementById('avatarUpload').click();
        }
        
        function handleAvatarUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                SuratNotification.warning('File Tidak Valid', 'Silakan pilih file gambar (JPG, PNG, GIF)');
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                SuratNotification.warning('File Terlalu Besar', 'Ukuran file maksimal 2MB');
                return;
            }
            
            // Preview avatar
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarContainer = document.getElementById('profileAvatar');
                avatarContainer.innerHTML = `<img src="${e.target.result}" alt="Avatar" class="avatar-image">`;
            };
            reader.readAsDataURL(file);
            
            // Upload avatar (you would implement actual upload here)
            uploadAvatar(file);
        }
        
        function uploadAvatar(file) {
            const formData = new FormData();
            formData.append('avatar', file);
            
            SuratNotification.loading('Upload Avatar', 'Sedang mengupload foto profil...');
            
            fetch('<?= base_url('profile/upload-avatar') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    SuratNotification.success('Berhasil!', 'Foto profil berhasil diperbarui');
                } else {
                    SuratNotification.error('Gagal!', data.message || 'Gagal mengupload foto profil');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                SuratNotification.error('Error!', 'Terjadi kesalahan saat mengupload');
            });
        }
        
        // Password visibility toggle
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '_eye');
            
            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.className = 'bi bi-eye-slash';
            } else {
                field.type = 'password';
                eyeIcon.className = 'bi bi-eye';
            }
        }
        
        // Quick settings functions
        function exportUserData() {
            SuratNotification.confirm(
                'Export Data Pengguna?',
                'Data profil dan aktivitas Anda akan diexport dalam format PDF',
                'Ya, Export',
                'Batal'
            ).then(confirmed => {
                if (confirmed) {
                    window.open('<?= base_url('profile/export-data') ?>', '_blank');
                }
            });
        }
        
        function viewActivityLog() {
            window.location.href = '<?= base_url('profile/activity-log') ?>';
        }
        
        function notificationSettings() {
            window.location.href = '<?= base_url('profile/notification-settings') ?>';
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const passwordForm = document.getElementById('password-form');
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            
            // Enhanced password validation with real-time requirements check
            function validatePasswords() {
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                // Check password requirements
                if (newPassword.length > 0) {
                    const requirements = {
                        length: newPassword.length >= 6,
                        lowercase: /[a-z]/.test(newPassword),
                        uppercase: /[A-Z]/.test(newPassword),
                        numbers: /\d/.test(newPassword)
                    };
                    
                    // Update requirement indicators
                    Object.keys(requirements).forEach(req => {
                        const element = document.getElementById('req-' + req);
                        const icon = element.querySelector('i');
                        
                        if (requirements[req]) {
                            element.classList.add('valid');
                            element.classList.remove('invalid');
                            icon.className = 'bi bi-check-circle text-success';
                        } else {
                            element.classList.add('invalid');
                            element.classList.remove('valid');
                            icon.className = 'bi bi-x-circle text-danger';
                        }
                    });
                    
                    // Calculate overall strength
                    const strength = Object.values(requirements).filter(Boolean).length;
                    let strengthText = '';
                    let strengthClass = '';
                    
                    if (strength < 2) {
                        strengthText = 'Lemah';
                        strengthClass = 'text-danger';
                    } else if (strength < 3) {
                        strengthText = 'Sedang';
                        strengthClass = 'text-warning';
                    } else {
                        strengthText = 'Kuat';
                        strengthClass = 'text-success';
                    }
                    
                    // Update or create strength indicator
                    let strengthIndicator = document.getElementById('password-strength');
                    if (!strengthIndicator) {
                        strengthIndicator = document.createElement('div');
                        strengthIndicator.id = 'password-strength';
                        strengthIndicator.className = 'mt-2';
                        document.getElementById('password-requirements').appendChild(strengthIndicator);
                    }
                    
                    strengthIndicator.innerHTML = `
                        <div class="d-flex align-items-center ${strengthClass}">
                            <div class="progress flex-grow-1 me-2" style="height: 4px;">
                                <div class="progress-bar ${strengthClass.replace('text-', 'bg-')}" 
                                     style="width: ${(strength / 4) * 100}%"></div>
                            </div>
                            <small class="fw-medium">${strengthText}</small>
                        </div>
                    `;
                }
                
                // Enhanced password confirmation validation
                if (confirmPassword.length > 0) {
                    const matchElement = document.getElementById('password-match');
                    
                    if (newPassword !== confirmPassword) {
                        confirmPasswordInput.setCustomValidity('Password tidak cocok');
                        confirmPasswordInput.classList.add('is-invalid');
                        confirmPasswordInput.classList.remove('is-valid');
                        
                        matchElement.style.display = 'block';
                        matchElement.innerHTML = '<i class="bi bi-x-circle text-danger me-1"></i>Password tidak cocok';
                        matchElement.className = 'mt-1 text-danger';
                    } else {
                        confirmPasswordInput.setCustomValidity('');
                        confirmPasswordInput.classList.remove('is-invalid');
                        confirmPasswordInput.classList.add('is-valid');
                        
                        matchElement.style.display = 'block';
                        matchElement.innerHTML = '<i class="bi bi-check-circle text-success me-1"></i>Password cocok';
                        matchElement.className = 'mt-1 text-success';
                    }
                } else {
                    document.getElementById('password-match').style.display = 'none';
                }
            }
            
            // Add event listeners
            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', validatePasswords);
            }
            
            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', validatePasswords);
            }
            
            // Password form submission with confirmation
            if (passwordForm) {
                passwordForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const currentPassword = document.getElementById('current_password').value;
                    const newPassword = newPasswordInput.value;
                    const confirmPassword = confirmPasswordInput.value;
                    
                    // Validate form
                    if (!currentPassword) {
                        SuratNotification.warning('Validasi Gagal', 'Password lama harus diisi');
                        return;
                    }
                    
                    if (!newPassword || newPassword.length < 6) {
                        SuratNotification.warning('Validasi Gagal', 'Password baru minimal 6 karakter');
                        return;
                    }
                    
                    if (newPassword !== confirmPassword) {
                        SuratNotification.warning('Validasi Gagal', 'Konfirmasi password tidak cocok');
                        return;
                    }
                    
                    // Show confirmation dialog
                    const confirmed = await SuratNotification.confirm(
                        'Ubah Password?', 
                        'Apakah Anda yakin ingin mengubah password Anda?',
                        'Ya, Ubah Password',
                        'Batal'
                    );
                    
                    if (confirmed) {
                        // Show loading
                        SuratNotification.loading('Mengubah Password...', 'Harap tunggu sebentar');
                        
                        // Submit form
                        this.submit();
                    }
                });
            }
            
            // Profile form enhancement
            const profileForm = document.querySelector('form[action*="profile/update"]');
            if (profileForm) {
                profileForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const nama = document.getElementById('nama').value.trim();
                    const email = document.getElementById('email').value.trim();
                    
                    if (!nama || nama.length < 3) {
                        SuratNotification.warning('Validasi Gagal', 'Nama harus diisi minimal 3 karakter');
                        return;
                    }
                    
                    if (!email || !email.includes('@')) {
                        SuratNotification.warning('Validasi Gagal', 'Email tidak valid');
                        return;
                    }
                    
                    // Show confirmation
                    const confirmed = await SuratNotification.confirm(
                        'Simpan Perubahan?', 
                        'Apakah Anda yakin ingin menyimpan perubahan profile?',
                        'Ya, Simpan',
                        'Batal'
                    );
                    
                    if (confirmed) {
                        // Show loading
                        SuratNotification.loading('Menyimpan Perubahan...', 'Memperbarui informasi profile');
                        
                        // Submit form
                        this.submit();
                    }
                });
            }
            
            // Add visual feedback for form inputs
            const formInputs = document.querySelectorAll('.form-control:not([readonly])');
            formInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() && this.checkValidity()) {
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                    }
                });
                
                input.addEventListener('input', function() {
                    this.classList.remove('is-valid', 'is-invalid');
                });
            });
        });
    </script>
</body>
</html>