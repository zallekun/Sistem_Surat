/**
 * Modern Notification System for Sistem Surat
 * Uses SweetAlert2 for beautiful popups and custom toast notifications
 */

// Configuration
const NotificationConfig = {
    position: 'top-end',
    timer: 5000,
    timerProgressBar: true,
    showCloseButton: true,
    toast: true,
    showConfirmButton: false
};

/**
 * Notification Class for handling all types of notifications
 */
class SuratNotification {
    
    /**
     * Success notification
     */
    static success(title, message = '') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                ...NotificationConfig,
                icon: 'success',
                title: title,
                text: message,
                background: '#d4edda',
                color: '#155724'
            });
        } else {
            this.fallbackNotification('success', title, message);
        }
    }

    /**
     * Error notification
     */
    static error(title, message = '') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                ...NotificationConfig,
                icon: 'error',
                title: title,
                text: message,
                background: '#f8d7da',
                color: '#721c24',
                timer: 7000 // Longer for errors
            });
        } else {
            this.fallbackNotification('error', title, message);
        }
    }

    /**
     * Warning notification
     */
    static warning(title, message = '') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                ...NotificationConfig,
                icon: 'warning',
                title: title,
                text: message,
                background: '#fff3cd',
                color: '#856404'
            });
        } else {
            this.fallbackNotification('warning', title, message);
        }
    }

    /**
     * Info notification
     */
    static info(title, message = '') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                ...NotificationConfig,
                icon: 'info',
                title: title,
                text: message,
                background: '#d1ecf1',
                color: '#0c5460'
            });
        } else {
            this.fallbackNotification('info', title, message);
        }
    }

    /**
     * Confirmation dialog
     */
    static async confirm(title, message, confirmText = 'Ya, Lanjutkan', cancelText = 'Batal') {
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'swal-wide'
                }
            });
            return result.isConfirmed;
        } else {
            return confirm(`${title}\n\n${message}`);
        }
    }

    /**
     * Delete confirmation with danger styling
     */
    static async confirmDelete(title = 'Hapus Data?', message = 'Data yang dihapus tidak dapat dikembalikan!') {
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash3"></i> Ya, Hapus!',
                cancelButtonText: '<i class="bi bi-x-circle"></i> Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn-delete-confirm',
                    popup: 'swal-delete'
                }
            });
            return result.isConfirmed;
        } else {
            return confirm(`${title}\n\n${message}`);
        }
    }

    /**
     * Loading notification
     */
    static loading(title = 'Memproses...', message = 'Harap tunggu sebentar') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                showCancelButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        } else {
            console.log(`Loading: ${title} - ${message}`);
        }
    }

    /**
     * Close loading
     */
    static closeLoading() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }

    /**
     * Bulk action confirmation
     */
    static async confirmBulkAction(action, count, itemType = 'item') {
        const title = `${action} ${count} ${itemType}?`;
        const message = `Apakah Anda yakin ingin ${action.toLowerCase()} ${count} ${itemType} yang dipilih?`;
        
        return await this.confirm(title, message, `Ya, ${action}!`, 'Batal');
    }

    /**
     * Form validation error
     */
    static validationError(errors) {
        let errorList = '';
        if (Array.isArray(errors)) {
            errorList = errors.map(error => `• ${error}`).join('<br>');
        } else if (typeof errors === 'object') {
            errorList = Object.values(errors).map(error => `• ${error}`).join('<br>');
        } else {
            errorList = errors;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errorList,
                confirmButtonText: 'Perbaiki',
                customClass: {
                    popup: 'swal-validation-error'
                }
            });
        } else {
            alert(`Validasi Gagal:\n${errorList.replace(/<br>/g, '\n').replace(/•/g, '-')}`);
        }
    }

    /**
     * Fallback notification using browser's built-in systems
     */
    static fallbackNotification(type, title, message) {
        // Try to create a custom styled notification div
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="me-2">
                    <i class="bi ${this.getIcon(type)}" style="font-size: 1.2rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <strong>${title}</strong>
                    ${message ? `<br><small>${message}</small>` : ''}
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    /**
     * Get Bootstrap icon based on type
     */
    static getIcon(type) {
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };
        return icons[type] || 'bi-info-circle-fill';
    }
}

// Make it globally available
window.SuratNotification = SuratNotification;

// Backward compatibility aliases
window.showSuccess = (title, message) => SuratNotification.success(title, message);
window.showError = (title, message) => SuratNotification.error(title, message);
window.showWarning = (title, message) => SuratNotification.warning(title, message);
window.showInfo = (title, message) => SuratNotification.info(title, message);
window.showConfirm = (title, message) => SuratNotification.confirm(title, message);

// Global utility functions
window.confirmLogout = async function(event) {
    event.preventDefault();
    
    const confirmed = await SuratNotification.confirm(
        'Logout dari Sistem?', 
        'Apakah Anda yakin ingin keluar dari sistem?',
        'Ya, Logout',
        'Batal'
    );
    
    if (confirmed) {
        SuratNotification.loading('Logging out...', 'Mengakhiri sesi Anda');
        window.location.href = (window.BASE_URL || '') + 'logout';
    }
};

window.confirmDelete = async function(event, message = 'Data yang dihapus tidak dapat dikembalikan!') {
    event.preventDefault();
    
    const confirmed = await SuratNotification.confirmDelete('Hapus Data?', message);
    
    if (confirmed) {
        // If the event target has a form, submit it, otherwise navigate to href
        if (event.target.closest('form')) {
            SuratNotification.loading('Menghapus...', 'Harap tunggu sebentar');
            event.target.closest('form').submit();
        } else if (event.target.href) {
            SuratNotification.loading('Menghapus...', 'Harap tunggu sebentar');
            window.location.href = event.target.href;
        }
    }
    
    return confirmed;
};

window.confirmSubmit = async function(event, title = 'Konfirmasi', message = 'Apakah Anda yakin?') {
    event.preventDefault();
    
    const confirmed = await SuratNotification.confirm(title, message);
    
    if (confirmed && event.target.closest('form')) {
        SuratNotification.loading('Memproses...', 'Harap tunggu sebentar');
        event.target.closest('form').submit();
    }
    
    return confirmed;
};

// Auto-show flash messages if they exist
document.addEventListener('DOMContentLoaded', function() {
    // Check for flash messages and convert them to notifications
    const successAlert = document.querySelector('.alert-success');
    const errorAlert = document.querySelector('.alert-danger');
    const warningAlert = document.querySelector('.alert-warning');
    const infoAlert = document.querySelector('.alert-info');

    if (successAlert) {
        const text = successAlert.textContent.replace(/×/g, '').trim();
        SuratNotification.success('Berhasil!', text);
        successAlert.style.display = 'none';
    }

    if (errorAlert) {
        const text = errorAlert.textContent.replace(/×/g, '').trim();
        SuratNotification.error('Error!', text);
        errorAlert.style.display = 'none';
    }

    if (warningAlert) {
        const text = warningAlert.textContent.replace(/×/g, '').trim();
        SuratNotification.warning('Peringatan!', text);
        warningAlert.style.display = 'none';
    }

    if (infoAlert) {
        const text = infoAlert.textContent.replace(/×/g, '').trim();
        SuratNotification.info('Informasi', text);
        infoAlert.style.display = 'none';
    }
});