<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">
<!-- Custom Notification Styling -->
<link rel="stylesheet" href="<?= base_url('assets/css/notifications.css') ?>">

<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
<!-- Custom Notification System -->
<script src="<?= base_url('assets/js/notifications.js') ?>"></script>

<script>
// Set base URL for JavaScript functions
window.BASE_URL = '<?= base_url() ?>';

// Configure SweetAlert2 defaults
document.addEventListener('DOMContentLoaded', function() {
    // Set global defaults for SweetAlert2
    Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary mx-2',
            cancelButton: 'btn btn-secondary mx-2'
        },
        buttonsStyling: false,
        reverseButtons: true,
        focusCancel: true
    });
    
    // Show any server-side flash messages
    <?php if (session()->getFlashdata('success')): ?>
        SuratNotification.success('Berhasil!', '<?= addslashes(session()->getFlashdata('success')) ?>');
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        SuratNotification.error('Error!', '<?= addslashes(session()->getFlashdata('error')) ?>');
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('warning')): ?>
        SuratNotification.warning('Peringatan!', '<?= addslashes(session()->getFlashdata('warning')) ?>');
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('info')): ?>
        SuratNotification.info('Informasi', '<?= addslashes(session()->getFlashdata('info')) ?>');
    <?php endif; ?>
});
</script>