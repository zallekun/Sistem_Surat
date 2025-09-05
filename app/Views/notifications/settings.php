<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">Pengaturan Notifikasi</h3>
                    <p class="text-muted">Kelola preferensi notifikasi Anda</p>
                </div>
                <div>
                    <a href="<?= base_url('notifications') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <form action="<?= base_url('notifications/settings') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="card card-enhanced mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-envelope"></i> Notifikasi Email
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="email_enabled" name="email_enabled" 
                                   <?= $settings['email_enabled'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="email_enabled">
                                <strong>Aktifkan Notifikasi Email</strong>
                            </label>
                            <div class="form-text">Terima notifikasi melalui email selain notifikasi internal</div>
                        </div>

                        <div id="emailSettings" class="<?= !$settings['email_enabled'] ? 'd-none' : '' ?>">
                            <label class="form-label">Kirim Email untuk Prioritas:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="email_low" name="email_priorities[]" value="LOW"
                                               <?= in_array('LOW', $settings['email_priorities']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="email_low">
                                            <span class="badge bg-secondary">Rendah</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="email_normal" name="email_priorities[]" value="NORMAL"
                                               <?= in_array('NORMAL', $settings['email_priorities']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="email_normal">
                                            <span class="badge bg-primary">Normal</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="email_high" name="email_priorities[]" value="HIGH"
                                               <?= in_array('HIGH', $settings['email_priorities']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="email_high">
                                            <span class="badge bg-warning text-dark">Tinggi</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="email_urgent" name="email_priorities[]" value="URGENT"
                                               <?= in_array('URGENT', $settings['email_priorities']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="email_urgent">
                                            <span class="badge bg-danger">Mendesak</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text">Pilih prioritas notifikasi yang akan dikirim via email</div>
                        </div>
                    </div>
                </div>

                <div class="card card-enhanced mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bell"></i> Jenis Notifikasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="workflow_notifications" 
                                           name="workflow_notifications" <?= $settings['workflow_notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="workflow_notifications">
                                        <i class="bi bi-arrow-right-circle text-primary"></i>
                                        <strong>Notifikasi Workflow</strong>
                                    </label>
                                    <div class="form-text">Persetujuan, penolakan, dan perubahan status surat</div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="system_notifications" 
                                           name="system_notifications" <?= $settings['system_notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="system_notifications">
                                        <i class="bi bi-info-circle text-info"></i>
                                        <strong>Notifikasi Sistem</strong>
                                    </label>
                                    <div class="form-text">Pengumuman sistem dan update aplikasi</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="reminder_notifications" 
                                           name="reminder_notifications" <?= $settings['reminder_notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="reminder_notifications">
                                        <i class="bi bi-clock text-warning"></i>
                                        <strong>Notifikasi Pengingat</strong>
                                    </label>
                                    <div class="form-text">Pengingat untuk surat yang akan mencapai deadline</div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="deadline_notifications" 
                                           name="deadline_notifications" <?= $settings['deadline_notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="deadline_notifications">
                                        <i class="bi bi-exclamation-triangle text-danger"></i>
                                        <strong>Notifikasi Deadline</strong>
                                    </label>
                                    <div class="form-text">Peringatan untuk surat yang melewati batas waktu</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-enhanced mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> Informasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="bi bi-envelope text-primary"></i> Email Terdaftar:</h6>
                                <p class="mb-0"><?= esc($user['email']) ?></p>
                                <small class="text-muted">
                                    Email notifikasi akan dikirim ke alamat ini
                                </small>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="bi bi-person text-info"></i> Role:</h6>
                                <p class="mb-0">
                                    <span class="badge bg-primary"><?= esc(ucwords(str_replace('_', ' ', $user['role']))) ?></span>
                                </p>
                                <small class="text-muted">
                                    Jenis notifikasi disesuaikan dengan role Anda
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= base_url('notifications') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('email_enabled').addEventListener('change', function() {
    const emailSettings = document.getElementById('emailSettings');
    if (this.checked) {
        emailSettings.classList.remove('d-none');
    } else {
        emailSettings.classList.add('d-none');
    }
});

// Validate form before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const emailEnabled = document.getElementById('email_enabled').checked;
    const priorities = document.querySelectorAll('input[name="email_priorities[]"]:checked');
    
    if (emailEnabled && priorities.length === 0) {
        e.preventDefault();
        SuratNotification.warning('Prioritas Diperlukan!', 'Harap pilih setidaknya satu prioritas untuk notifikasi email.');
        return false;
    }
});
</script>
<?= $this->endSection() ?>