/**
 * PWA (Progressive Web App) Integration
 * Handles installation, offline detection, and mobile optimizations
 */

class PWAManager {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.isOnline = navigator.onLine;
        
        this.init();
    }

    init() {
        this.registerServiceWorker();
        this.setupInstallPrompt();
        this.setupOfflineDetection();
        this.setupMobileOptimizations();
        this.setupPushNotifications();
    }

    // Service Worker Registration
    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js', {
                    scope: '/'
                });

                console.log('Service Worker registered successfully:', registration);

                // Handle updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateAvailable();
                        }
                    });
                });

                // Handle messages from service worker
                navigator.serviceWorker.addEventListener('message', (event) => {
                    this.handleServiceWorkerMessage(event.data);
                });

            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        }
    }

    // PWA Install Prompt
    setupInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallPrompt();
        });

        window.addEventListener('appinstalled', () => {
            this.isInstalled = true;
            this.hideInstallPrompt();
            this.showToast('App installed successfully!', 'success');
        });

        // Check if already installed
        if (window.matchMedia('(display-mode: standalone)').matches || 
            window.navigator.standalone === true) {
            this.isInstalled = true;
        }
    }

    showInstallPrompt() {
        if (this.isInstalled) return;

        const promptHtml = `
            <div id="pwa-install-prompt" class="pwa-install-prompt">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>📱 Install UNJANI Surat</strong>
                        <div class="small">Get the full app experience!</div>
                    </div>
                    <div>
                        <button class="btn btn-sm me-2" onclick="pwaManager.installApp()">
                            Install
                        </button>
                        <button class="btn btn-sm" onclick="pwaManager.hideInstallPrompt()">
                            ✕
                        </button>
                    </div>
                </div>
            </div>
        `;

        if (!document.getElementById('pwa-install-prompt')) {
            document.body.insertAdjacentHTML('beforeend', promptHtml);
            setTimeout(() => {
                const prompt = document.getElementById('pwa-install-prompt');
                if (prompt) prompt.classList.add('show');
            }, 2000);
        }
    }

    async installApp() {
        if (!this.deferredPrompt) return;

        const result = await this.deferredPrompt.prompt();
        console.log('Install prompt result:', result);

        this.deferredPrompt = null;
        this.hideInstallPrompt();
    }

    hideInstallPrompt() {
        const prompt = document.getElementById('pwa-install-prompt');
        if (prompt) {
            prompt.classList.remove('show');
            setTimeout(() => prompt.remove(), 300);
        }
    }

    // Offline Detection
    setupOfflineDetection() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.hideOfflineIndicator();
            this.showToast('✅ Back online!', 'success');
            this.syncOfflineActions();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.showOfflineIndicator();
            this.showToast('📡 You are offline', 'warning');
        });

        // Initial state
        if (!this.isOnline) {
            this.showOfflineIndicator();
        }
    }

    showOfflineIndicator() {
        if (document.getElementById('offline-indicator')) return;

        const indicatorHtml = `
            <div id="offline-indicator" class="offline-indicator show">
                📡 You are currently offline - Limited functionality available
            </div>
        `;

        document.body.insertAdjacentHTML('afterbegin', indicatorHtml);
    }

    hideOfflineIndicator() {
        const indicator = document.getElementById('offline-indicator');
        if (indicator) {
            indicator.classList.remove('show');
            setTimeout(() => indicator.remove(), 300);
        }
    }

    // Mobile Optimizations
    setupMobileOptimizations() {
        if (this.isMobile()) {
            this.setupMobileNavigation();
            this.setupTouchGestures();
            this.setupBottomNavigation();
            this.preventZoom();
        }
    }

    setupMobileNavigation() {
        // Add mobile header if not exists
        if (!document.querySelector('.mobile-header')) {
            const header = `
                <div class="mobile-header mobile-only">
                    <button class="mobile-menu-btn" onclick="pwaManager.toggleSidebar()">
                        <i class="bi bi-list"></i>
                    </button>
                    <h6 class="mb-0">UNJANI Surat</h6>
                    <div style="width: 40px;"></div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('afterbegin', header);
        }

        // Add overlay for sidebar
        if (!document.querySelector('.sidebar-overlay')) {
            const overlay = `<div class="sidebar-overlay" onclick="pwaManager.closeSidebar()"></div>`;
            document.body.insertAdjacentHTML('beforeend', overlay);
        }
    }

    toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    }

    closeSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    }

    setupBottomNavigation() {
        const currentPath = window.location.pathname;
        const navItems = [
            { path: '/dashboard', icon: 'bi-speedometer2', label: 'Home' },
            { path: '/search', icon: 'bi-search', label: 'Search' },
            { path: '/surat', icon: 'bi-envelope', label: 'Surat' },
            { path: '/analytics', icon: 'bi-graph-up', label: 'Analytics' }
        ];

        const navHtml = `
            <div class="bottom-nav mobile-only safe-bottom">
                ${navItems.map(item => `
                    <a href="${item.path}" class="bottom-nav-item ${currentPath === item.path ? 'active' : ''}">
                        <i class="${item.icon}"></i>
                        <span>${item.label}</span>
                    </a>
                `).join('')}
            </div>
        `;

        if (!document.querySelector('.bottom-nav')) {
            document.body.insertAdjacentHTML('beforeend', navHtml);
        }
    }

    setupTouchGestures() {
        let startX = 0;
        let startY = 0;

        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });

        document.addEventListener('touchmove', (e) => {
            if (!startX || !startY) return;

            const diffX = startX - e.touches[0].clientX;
            const diffY = startY - e.touches[0].clientY;

            // Swipe right to open sidebar
            if (Math.abs(diffX) > Math.abs(diffY) && diffX < -50 && startX < 50) {
                this.toggleSidebar();
            }

            // Swipe left to close sidebar
            if (Math.abs(diffX) > Math.abs(diffY) && diffX > 50) {
                this.closeSidebar();
            }

            startX = 0;
            startY = 0;
        });
    }

    preventZoom() {
        // Prevent double-tap zoom on iOS
        let lastTouchEnd = 0;
        document.addEventListener('touchend', (e) => {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                e.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    }

    // Push Notifications
    async setupPushNotifications() {
        if ('Notification' in window && 'serviceWorker' in navigator && 'PushManager' in window) {
            try {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    this.subscribeToPush();
                }
            } catch (error) {
                console.error('Push notification setup failed:', error);
            }
        }
    }

    async subscribeToPush() {
        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlB64ToUint8Array(this.getVapidKey())
            });

            // Send subscription to server
            await fetch('/api/push-subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(subscription)
            });

        } catch (error) {
            console.error('Push subscription failed:', error);
        }
    }

    // Update Management
    showUpdateAvailable() {
        const updateHtml = `
            <div id="update-available" class="pwa-install-prompt show">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>🆕 Update Available</strong>
                        <div class="small">New version of the app is ready!</div>
                    </div>
                    <div>
                        <button class="btn btn-sm me-2" onclick="pwaManager.applyUpdate()">
                            Update
                        </button>
                        <button class="btn btn-sm" onclick="pwaManager.dismissUpdate()">
                            Later
                        </button>
                    </div>
                </div>
            </div>
        `;

        if (!document.getElementById('update-available')) {
            document.body.insertAdjacentHTML('beforeend', updateHtml);
        }
    }

    applyUpdate() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then((registrations) => {
                registrations.forEach((registration) => {
                    registration.waiting?.postMessage({ action: 'skipWaiting' });
                });
            });
            window.location.reload();
        }
    }

    dismissUpdate() {
        const updateBanner = document.getElementById('update-available');
        if (updateBanner) {
            updateBanner.classList.remove('show');
            setTimeout(() => updateBanner.remove(), 300);
        }
    }

    // Offline Actions Sync
    async syncOfflineActions() {
        if (!this.isOnline) return;

        try {
            // Trigger background sync
            const registration = await navigator.serviceWorker.ready;
            await registration.sync.register('background-sync-form');
        } catch (error) {
            console.error('Background sync failed:', error);
        }
    }

    // Utility Functions
    isMobile() {
        return window.innerWidth <= 768 || 
               /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    showToast(message, type = 'info') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
        }

        container.insertAdjacentHTML('beforeend', toastHtml);
        const toast = container.lastElementChild;
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    urlB64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    getVapidKey() {
        // This should be your VAPID public key from your server
        return 'BEl62iUYgUivxIkv69yViEuiBIa40HI80Y5UkdlZFJrZbZLy-00DIZyDN1UaYHWdU6FOqjQa8Gl8Xj';
    }

    handleServiceWorkerMessage(data) {
        if (data.type === 'CACHE_UPDATED') {
            this.showToast('Content updated and cached', 'success');
        }
    }
}

// Initialize PWA Manager
let pwaManager;
document.addEventListener('DOMContentLoaded', () => {
    pwaManager = new PWAManager();
});

// Global functions for template usage
window.pwaManager = null;
window.addEventListener('load', () => {
    if (!window.pwaManager) {
        window.pwaManager = new PWAManager();
    }
});