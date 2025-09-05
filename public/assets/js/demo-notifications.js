/**
 * Demo functions untuk menguji sistem notifikasi
 * Hanya untuk development/testing
 */

// Demo success notification
function demoSuccess() {
    SuratNotification.success('Operasi Berhasil!', 'Data telah berhasil disimpan ke database');
}

// Demo error notification
function demoError() {
    SuratNotification.error('Terjadi Kesalahan!', 'Tidak dapat terhubung ke server. Silakan coba lagi');
}

// Demo warning notification
function demoWarning() {
    SuratNotification.warning('Perhatian!', 'Beberapa field belum diisi dengan lengkap');
}

// Demo info notification
function demoInfo() {
    SuratNotification.info('Informasi Sistem', 'Maintenance server akan dilakukan pada pukul 02:00 WIB');
}

// Demo confirmation
async function demoConfirm() {
    const result = await SuratNotification.confirm(
        'Konfirmasi Aksi',
        'Apakah Anda yakin ingin melanjutkan proses ini?',
        'Ya, Lanjutkan',
        'Batal'
    );
    
    if (result) {
        SuratNotification.success('Dikonfirmasi!', 'Anda memilih untuk melanjutkan');
    } else {
        SuratNotification.info('Dibatalkan', 'Operasi telah dibatalkan');
    }
}

// Demo delete confirmation
async function demoDelete() {
    const result = await SuratNotification.confirmDelete(
        'Hapus Data Surat?',
        'Surat yang dihapus tidak dapat dikembalikan. Pastikan Anda yakin dengan keputusan ini.'
    );
    
    if (result) {
        SuratNotification.success('Terhapus!', 'Data surat berhasil dihapus');
    } else {
        SuratNotification.info('Aman', 'Data tidak jadi dihapus');
    }
}

// Demo bulk action
async function demoBulkAction() {
    const result = await SuratNotification.confirmBulkAction('Submit', 5, 'surat');
    
    if (result) {
        SuratNotification.loading('Memproses...', 'Mengirim 5 surat untuk review');
        
        // Simulate processing
        setTimeout(() => {
            SuratNotification.closeLoading();
            SuratNotification.success('Berhasil!', '5 surat telah dikirim untuk review');
        }, 2000);
    }
}

// Demo validation error
function demoValidationError() {
    const errors = [
        'Nama harus diisi minimal 3 karakter',
        'Email tidak valid',
        'Password minimal 6 karakter',
        'Nomor telepon harus berupa angka'
    ];
    
    SuratNotification.validationError(errors);
}

// Demo loading
function demoLoading() {
    SuratNotification.loading('Mengunggah File...', 'Harap tunggu, file sedang diproses');
    
    // Auto close after 3 seconds
    setTimeout(() => {
        SuratNotification.closeLoading();
        SuratNotification.success('Selesai!', 'File berhasil diunggah');
    }, 3000);
}

// Demo toast notifications sequence
function demoSequence() {
    SuratNotification.info('Memulai Proses...', 'Inisialisasi sistem');
    
    setTimeout(() => {
        SuratNotification.warning('Validasi...', 'Memeriksa data input');
    }, 1000);
    
    setTimeout(() => {
        SuratNotification.success('Validasi OK!', 'Semua data valid');
    }, 2000);
    
    setTimeout(() => {
        SuratNotification.info('Menyimpan...', 'Sedang menyimpan ke database');
    }, 3000);
    
    setTimeout(() => {
        SuratNotification.success('Selesai!', 'Proses berhasil diselesaikan');
    }, 4500);
}

// Make functions globally available for demo
window.demoNotifications = {
    success: demoSuccess,
    error: demoError,
    warning: demoWarning,
    info: demoInfo,
    confirm: demoConfirm,
    delete: demoDelete,
    bulkAction: demoBulkAction,
    validationError: demoValidationError,
    loading: demoLoading,
    sequence: demoSequence
};