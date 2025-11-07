class SAARCISinistresPWA {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.isOnline = navigator.onLine;
        this.updateAvailable = false;
        this.registration = null;
        
        this.init();
    }
    
    async init() {
        this.checkInstallationStatus();
        
        // Vérification immédiate de l'installation
        this.immediateInstallationCheck();
        
        this.setupInstallListeners();
        
        this.setupConnectivityListeners();
        
        await this.initServiceWorker();
        
        this.showPWAInterface();
    }
    
    immediateInstallationCheck() {
        const wasAccepted = localStorage.getItem('pwa-installation-accepted') === 'true';
        if (wasAccepted) {
            this.isInstalled = true;
            return;
        }
        
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
        if (isStandalone) {
            this.isInstalled = true;
            localStorage.setItem('pwa-installation-accepted', 'true');
            return;
        }
        
        setTimeout(() => {
            if (!this.deferredPrompt && !this.isInstalled) {
                this.isInstalled = true;
                localStorage.setItem('pwa-installation-accepted', 'true');
                localStorage.removeItem('pwa-installation-dismissed');
                localStorage.removeItem('pwa-installation-dismissed-timestamp');
                
                this.hidePWABarAfterInstallation();
            }
        }, 3000);
    }
    
    checkInstallationStatus() {
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
        const isIOSStandalone = window.navigator.standalone === true;
        const wasAccepted = localStorage.getItem('pwa-installation-accepted') === 'true';
        const wasDismissed = localStorage.getItem('pwa-installation-dismissed') === 'true';
        const dismissedTimestamp = localStorage.getItem('pwa-installation-dismissed-timestamp');
        
        // Détection plus robuste de l'installation
        const hasOpenInAppButton = this.checkForOpenInAppButton();
        const reallyInstalled = isStandalone || isIOSStandalone || hasOpenInAppButton;
        
        if (wasAccepted && !reallyInstalled) {
            localStorage.removeItem('pwa-installation-accepted');
            localStorage.removeItem('pwa-installation-dismissed');
            localStorage.removeItem('pwa-installation-dismissed-timestamp');
            this.isInstalled = false;
            return;
        }
        
        if (wasDismissed && dismissedTimestamp) {
            const now = Date.now();
            const dismissedTime = parseInt(dismissedTimestamp);
            const oneDayInMs = 24 * 60 * 60 * 1000;
            
            if (now - dismissedTime > oneDayInMs) {
                localStorage.removeItem('pwa-installation-dismissed');
                localStorage.removeItem('pwa-installation-dismissed-timestamp');
                this.isInstalled = false;
                return;
            }
        }
        
        if (reallyInstalled && !wasAccepted) {
            localStorage.setItem('pwa-installation-accepted', 'true');
            localStorage.removeItem('pwa-installation-dismissed');
            localStorage.removeItem('pwa-installation-dismissed-timestamp');
        }
        
        this.isInstalled = reallyInstalled || wasAccepted;
        
        if ('getInstalledRelatedApps' in navigator) {
            navigator.getInstalledRelatedApps().then(relatedApps => {
                if (relatedApps.length > 0) {
                    this.isInstalled = true;
                    localStorage.setItem('pwa-installation-accepted', 'true');
                }
            });
        }
        
    }
    
    checkForOpenInAppButton() {
        try {
            
            const installButton = document.querySelector('[data-testid="install-button"], [aria-label*="install"], [aria-label*="Installer"]');
            if (installButton) {
                return true;
            }
            
            const openInAppElements = document.querySelectorAll('[aria-label*="Ouvrir"], [aria-label*="Open"], [title*="Ouvrir"], [title*="Open"]');
            if (openInAppElements.length > 0) {
                return true;
            }
            
            if (!this.deferredPrompt && window.matchMedia('(display-mode: browser)').matches) {
               
                setTimeout(() => {
                    if (!this.deferredPrompt) {
                        this.isInstalled = true;
                        localStorage.setItem('pwa-installation-accepted', 'true');
                        this.hidePWABarAfterInstallation();
                    }
                }, 2000);
            }
            
            return false;
        } catch (error) {
            return false;
        }
    }
    
    setupInstallListeners() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            
            const wasDismissed = localStorage.getItem('pwa-installation-dismissed') === 'true';
            const wasAccepted = localStorage.getItem('pwa-installation-accepted') === 'true';
            
            const dismissalExpired = this.checkDismissalExpiration();
            const canShow = (!wasDismissed || dismissalExpired) && !wasAccepted;
            if (canShow) {
                // Bannière d'installation affichée
            } else {
                // Installation déjà gérée par l'utilisateur - bannière non affichée
            }
        });
        
        window.addEventListener('appinstalled', (e) => {
            this.isInstalled = true;
            
            localStorage.setItem('pwa-installation-accepted', 'true');
            localStorage.removeItem('pwa-installation-dismissed');
            localStorage.removeItem('pwa-installation-dismissed-timestamp');
            
            this.showInstallationSuccess();
        });
    }
    
    setupConnectivityListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.hideOfflineIndicator();
            this.checkForUpdates();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.showOfflineIndicator();
        });
    }
    
    async initServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                // Enregistrement avec vérification régulière des mises à jour
                this.registration = await navigator.serviceWorker.register('/sw.js', {
                    updateViaCache: 'none' 
                });
                
                this.registration.addEventListener('updatefound', () => {
                    const newWorker = this.registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.updateAvailable = true;
                            this.showUpdateNotification();
                        }
                    });
                });
                
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data && event.data.type === 'UPDATE_AVAILABLE') {
                        this.updateAvailable = true;
                        this.showUpdateNotification();
                    }
                });
                
                // Vérifier les mises à jour toutes les 30 minutes
                setInterval(() => {
                    this.checkForUpdates();
                }, 30 * 60 * 1000);
                
                // Vérifier quand la page redevient visible
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) {
                        this.checkForUpdates();
                    }
                });
                
            } catch (error) {
                console.error('PWA: Erreur lors de l\'enregistrement du Service Worker:', error);
            }
        }
    }
    
    showPWAInterface() {
        
        if (!this.isInstalled) {
            this.createPWABar();
        
            if (this.isIOS() && !this.deferredPrompt) {
                const wasDismissed = localStorage.getItem('pwa-installation-dismissed') === 'true';
                const wasAccepted = localStorage.getItem('pwa-installation-accepted') === 'true';
                const dismissalExpired = this.checkDismissalExpiration();
                const canShow = (!wasDismissed || dismissalExpired) && !wasAccepted;
                if (canShow) {
                    // Bannière iOS affichée
                }
            }
        }
        
        
        this.createOfflineIndicator();
        this.createUpdateNotification();
    }
    
    createPWABar() {
        if (this.isInstalled) return;
        
        const pwaBar = document.createElement('div');
        pwaBar.id = 'pwa-bar';
        pwaBar.className = 'fixed top-0 left-0 right-0 bg-gradient-to-r from-green-500 via-green-600 to-green-700 text-white p-4 z-50 transform transition-all duration-500 shadow-lg';
        pwaBar.style.transform = 'translateY(-100%)';
        
        pwaBar.innerHTML = `
            <div class="flex items-center justify-between max-w-7xl mx-auto">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">Installez SAARCISinistres</h3>
                        <p class="text-green-100 text-sm">Accès rapide, notifications et expérience optimisée</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="pwa-install-btn" class="bg-white text-green-700 px-6 py-2 rounded-lg font-semibold hover:bg-green-50 transition-all duration-200 transform hover:scale-105 shadow-md">
                        <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        Installer
                    </button>
                    <button id="pwa-dismiss-btn" class="text-white/70 hover:text-white transition-colors p-2 rounded-full hover:bg-white/10">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(pwaBar);
        
        setTimeout(() => {
            pwaBar.style.transform = 'translateY(0)';
        }, 1500);
        
        document.getElementById('pwa-install-btn').addEventListener('click', () => {
            this.installApp();
        });
        
        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
            this.hidePWABar();
        });
    }
    
    
    createOfflineIndicator() {
        const offlineIndicator = document.createElement('div');
        offlineIndicator.id = 'pwa-offline-indicator';
        offlineIndicator.className = 'fixed top-0 left-0 right-0 bg-red-600 text-white p-2 text-center z-50 transform transition-transform duration-300';
        offlineIndicator.style.transform = 'translateY(-100%)';
        offlineIndicator.innerHTML = `
            <div class="flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Vous êtes hors ligne. Certaines fonctionnalités peuvent être limitées.</span>
            </div>
        `;
        
        document.body.appendChild(offlineIndicator);
    }
    
    createUpdateNotification() {
        const updateNotification = document.createElement('div');
        updateNotification.id = 'pwa-update-notification';
        updateNotification.className = 'fixed bottom-6 left-6 right-6 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300';
        updateNotification.style.transform = 'translateY(100%)';
        updateNotification.style.display = 'none';
        
        updateNotification.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Une nouvelle version est disponible</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="pwa-update-btn" class="bg-white text-blue-600 px-4 py-2 rounded font-medium hover:bg-gray-100 transition-colors">
                        Mettre à jour
                    </button>
                    <button id="pwa-update-dismiss-btn" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(updateNotification);
        
        document.getElementById('pwa-update-btn').addEventListener('click', () => {
            this.updateApp();
        });
        
        document.getElementById('pwa-update-dismiss-btn').addEventListener('click', () => {
            this.hideUpdateNotification();
        });
    }
    
    
    hidePWABar() {
        const pwaBar = document.getElementById('pwa-bar');
        if (pwaBar) {
            pwaBar.style.transform = 'translateY(-100%)';
            setTimeout(() => pwaBar.remove(), 300);
        }
        
        localStorage.setItem('pwa-installation-dismissed', 'true');
        localStorage.setItem('pwa-installation-dismissed-timestamp', Date.now().toString());
    }
    
    hidePWABarAfterInstallation() {
        const pwaBar = document.getElementById('pwa-bar');
        if (pwaBar) {
            pwaBar.style.transform = 'translateY(-100%)';
            pwaBar.style.opacity = '0';
            setTimeout(() => {
                if (pwaBar.parentNode) {
                    pwaBar.remove();
                }
            }, 300);
        }
        
        this.isInstalled = true;
    }
    
    showOfflineIndicator() {
        const offlineIndicator = document.getElementById('pwa-offline-indicator');
        if (offlineIndicator) {
            offlineIndicator.style.transform = 'translateY(0)';
        }
    }
    
    hideOfflineIndicator() {
        const offlineIndicator = document.getElementById('pwa-offline-indicator');
        if (offlineIndicator) {
            offlineIndicator.style.transform = 'translateY(-100%)';
        }
    }
    
    showUpdateNotification() {
        const updateNotification = document.getElementById('pwa-update-notification');
        if (updateNotification) {
            updateNotification.style.display = 'block';
            setTimeout(() => {
                updateNotification.style.transform = 'translateY(0)';
            }, 100);
        }
    }
    
    hideUpdateNotification() {
        const updateNotification = document.getElementById('pwa-update-notification');
        if (updateNotification) {
            updateNotification.style.transform = 'translateY(100%)';
            setTimeout(() => {
                updateNotification.style.display = 'none';
            }, 300);
        }
    }
    
    async installApp() {
        if (!this.deferredPrompt) {
            if (this.isIOS()) {
                this.showIOSInstallInstructions();
                return;
            }
            return;
        }
        
        try {
            this.deferredPrompt.prompt();
            
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                this.showInstallationSuccess();
            }
            
            this.deferredPrompt = null;
            
        } catch (error) {
            console.error('PWA: Erreur lors de l\'installation:', error);
        }
    }
    
    showInstallationSuccess() {
        const successNotification = document.createElement('div');
        successNotification.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white p-6 rounded-lg shadow-xl z-50';
        successNotification.innerHTML = `
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-xl font-bold mb-2">Installation réussie !</h3>
                <p class="text-green-100">SAARCISinistres est maintenant installé sur votre appareil.</p>
            </div>
        `;
        
        document.body.appendChild(successNotification);
        
        setTimeout(() => {
            successNotification.remove();
        }, 3000);
        
        this.isInstalled = true;
        localStorage.setItem('pwa-installation-accepted', 'true');
        localStorage.removeItem('pwa-installation-dismissed');
        localStorage.removeItem('pwa-installation-dismissed-timestamp');
        
        this.hidePWABarAfterInstallation();
    }
    
    async updateApp() {
        if (this.registration && this.registration.waiting) {
            this.registration.waiting.postMessage({ type: 'SKIP_WAITING' });
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }
    
    async checkForUpdates() {
        if (this.registration) {
            try {
                await this.registration.update();
            } catch (error) {
                console.error('PWA: Erreur lors de la vérification des mises à jour:', error);
            }
        }
    }
    
    static isSupported() {
        // Ne pas exiger PushManager pour supporter l'UI PWA
        return 'serviceWorker' in navigator;
    }
    
    static getInstallStatus() {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true;
    }

    isIOS() {
        const ua = window.navigator.userAgent;
        const iOS = /iphone|ipad|ipod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        return iOS;
    }

    showIOSInstallInstructions() {
        // Crée une modale avec instructions iOS: Partager -> Ajouter à l'écran d'accueil
        const existing = document.getElementById('pwa-ios-instructions');
        if (existing) return;
        const modal = document.createElement('div');
        modal.id = 'pwa-ios-instructions';
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/50';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Installer l'application</h3>
                    <button id="pwa-ios-instructions-close" class="text-gray-500 hover:text-gray-700">✕</button>
                </div>
                <div class="space-y-3 text-gray-700">
                    <p>Sur iPhone, l'installation passe par le menu <strong>Partager</strong> de Safari/Chrome.</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Appuyez sur l'icône <strong>Partager</strong> en bas de l'écran.</li>
                        <li>Sélectionnez <strong>Ajouter à l'écran d'accueil</strong>.</li>
                        <li>Confirmez en appuyant sur <strong>Ajouter</strong>.</li>
                    </ol>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button id="pwa-ios-instructions-dismiss" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200">Ne plus afficher aujourd'hui</button>
                    <button id="pwa-ios-instructions-ok" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">OK</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        const close = () => {
            modal.remove();
        };
        document.getElementById('pwa-ios-instructions-close').addEventListener('click', close);
        document.getElementById('pwa-ios-instructions-ok').addEventListener('click', close);
        document.getElementById('pwa-ios-instructions-dismiss').addEventListener('click', () => {
            localStorage.setItem('pwa-installation-dismissed', 'true');
            localStorage.setItem('pwa-installation-dismissed-timestamp', Date.now().toString());
            close();
        });
    }
    
    checkDismissalExpiration() {
        const wasDismissed = localStorage.getItem('pwa-installation-dismissed') === 'true';
        const dismissedTimestamp = localStorage.getItem('pwa-installation-dismissed-timestamp');
        
        if (wasDismissed && dismissedTimestamp) {
            const now = Date.now();
            const dismissedTime = parseInt(dismissedTimestamp);
            const oneDayInMs = 24 * 60 * 60 * 1000;
            
            if (now - dismissedTime > oneDayInMs) {
                localStorage.removeItem('pwa-installation-dismissed');
                localStorage.removeItem('pwa-installation-dismissed-timestamp');
                return true;
            }
        }
        
        return false;
    }
    
    resetInstallationState() {
        localStorage.removeItem('pwa-installation-dismissed');
        localStorage.removeItem('pwa-installation-dismissed-timestamp');
        localStorage.removeItem('pwa-installation-accepted');
        this.isInstalled = false;
        this.checkInstallationStatus();
    }
    
    forceResetInstallationState() {
        localStorage.removeItem('pwa-installation-dismissed');
        localStorage.removeItem('pwa-installation-dismissed-timestamp');
        localStorage.removeItem('pwa-installation-accepted');
        this.isInstalled = false;
        this.checkInstallationStatus();
    }
    
    getInstallationState() {
        const dismissedTimestamp = localStorage.getItem('pwa-installation-dismissed-timestamp');
        const dismissedTime = dismissedTimestamp ? parseInt(dismissedTimestamp) : null;
        const now = Date.now();
        const oneDayInMs = 24 * 60 * 60 * 1000;
        
        return {
            isInstalled: this.isInstalled,
            wasDismissed: localStorage.getItem('pwa-installation-dismissed') === 'true',
            wasAccepted: localStorage.getItem('pwa-installation-accepted') === 'true',
            isStandalone: window.matchMedia('(display-mode: standalone)').matches,
            isIOSStandalone: window.navigator.standalone === true,
            dismissedTimestamp: dismissedTime,
            dismissedTimeRemaining: dismissedTime ? Math.max(0, oneDayInMs - (now - dismissedTime)) : 0,
            dismissalExpired: this.checkDismissalExpiration()
        };
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (SAARCISinistresPWA.isSupported()) {
        window.saarSinistrePWA = new SAARCISinistresPWA();
    }
});

window.SAARCISinistresPWA = SAARCISinistresPWA; 