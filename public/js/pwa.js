class SAARSinistrePWA {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.isOnline = navigator.onLine;
        this.updateAvailable = false;
        this.registration = null;
        
        this.init();
    }
    
    async init() {
        // Vérifie si l'app est déjà installée
        this.checkInstallationStatus();
        
        // Écoute les événements d'installation
        this.setupInstallListeners();
        
        // Écoute les changements de connectivité
        this.setupConnectivityListeners();
        
        // Initialise le service worker
        await this.initServiceWorker();
        
        // Affiche l'interface PWA
        this.showPWAInterface();
    }
    
    checkInstallationStatus() {
        // Vérifie si l'app est en mode standalone (installée)
        this.isInstalled = window.matchMedia('(display-mode: standalone)').matches ||
                           window.navigator.standalone === true;
        
        // Vérifie si l'app est installée via l'API
        if ('getInstalledRelatedApps' in navigator) {
            navigator.getInstalledRelatedApps().then(relatedApps => {
                this.isInstalled = relatedApps.length > 0;
            });
        }
    }
    
    setupInstallListeners() {
        // Écoute l'événement beforeinstallprompt
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('PWA: Événement d\'installation détecté');
            e.preventDefault();
            this.deferredPrompt = e;
            
            // Affiche le bouton d'installation
            this.showInstallButton();
        });
        
        // Écoute l'événement appinstalled
        window.addEventListener('appinstalled', (e) => {
            console.log('PWA: Application installée avec succès');
            this.isInstalled = true;
            this.hideInstallButton();
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
                this.registration = await navigator.serviceWorker.register('/sw.js');
                console.log('PWA: Service Worker enregistré:', this.registration);
                
                // Écoute les mises à jour du service worker
                this.registration.addEventListener('updatefound', () => {
                    const newWorker = this.registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.updateAvailable = true;
                            this.showUpdateNotification();
                        }
                    });
                });
                
                // Écoute les messages du service worker
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data && event.data.type === 'UPDATE_AVAILABLE') {
                        this.updateAvailable = true;
                        this.showUpdateNotification();
                    }
                });
                
            } catch (error) {
                console.error('PWA: Erreur lors de l\'enregistrement du Service Worker:', error);
            }
        }
    }
    
    showPWAInterface() {
        // Crée la barre PWA
        this.createPWABar();
        
        // Crée le bouton d'installation
        this.createInstallButton();
        
        // Crée l'indicateur offline
        this.createOfflineIndicator();
        
        // Crée la notification de mise à jour
        this.createUpdateNotification();
    }
    
    createPWABar() {
        if (this.isInstalled) return;
        
        const pwaBar = document.createElement('div');
        pwaBar.id = 'pwa-bar';
        pwaBar.className = 'fixed top-0 left-0 right-0 bg-gradient-to-r from-red-600 to-blue-600 text-white p-3 z-50 transform transition-transform duration-300';
        pwaBar.style.transform = 'translateY(-100%)';
        
        pwaBar.innerHTML = `
            <div class="flex items-center justify-between max-w-7xl mx-auto">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Installez SAARSinistre pour une meilleure expérience</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="pwa-install-btn" class="bg-white text-red-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                        Installer
                    </button>
                    <button id="pwa-dismiss-btn" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(pwaBar);
        
        // Affiche la barre après un délai
        setTimeout(() => {
            pwaBar.style.transform = 'translateY(0)';
        }, 2000);
        
        // Gère les clics
        document.getElementById('pwa-install-btn').addEventListener('click', () => {
            this.installApp();
        });
        
        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
            this.hidePWABar();
        });
    }
    
    createInstallButton() {
        if (this.isInstalled) return;
        
        const installBtn = document.createElement('div');
        installBtn.id = 'pwa-install-fab';
        installBtn.className = 'fixed bottom-6 right-6 z-40 hidden';
        installBtn.innerHTML = `
            <button class="bg-gradient-to-r from-red-600 to-blue-600 text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                </svg>
            </button>
        `;
        
        document.body.appendChild(installBtn);
        
        installBtn.querySelector('button').addEventListener('click', () => {
            this.installApp();
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
        
        // Gérer les clics
        document.getElementById('pwa-update-btn').addEventListener('click', () => {
            this.updateApp();
        });
        
        document.getElementById('pwa-update-dismiss-btn').addEventListener('click', () => {
            this.hideUpdateNotification();
        });
    }
    
    showInstallButton() {
        const installBtn = document.getElementById('pwa-install-fab');
        if (installBtn) {
            installBtn.classList.remove('hidden');
        }
    }
    
    hideInstallButton() {
        const installBtn = document.getElementById('pwa-install-fab');
        if (installBtn) {
            installBtn.classList.add('hidden');
        }
    }
    
    hidePWABar() {
        const pwaBar = document.getElementById('pwa-bar');
        if (pwaBar) {
            pwaBar.style.transform = 'translateY(-100%)';
            setTimeout(() => pwaBar.remove(), 300);
        }
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
            console.log('PWA: Aucune invite d\'installation disponible');
            return;
        }
        
        try {
            // Afficher l'invite d'installation
            this.deferredPrompt.prompt();
            
            // Attendre la réponse de l'utilisateur
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('PWA: Installation acceptée par l\'utilisateur');
                this.showInstallationSuccess();
            } else {
                console.log('PWA: Installation refusée par l\'utilisateur');
            }
            
            // Réinitialiser l'invite
            this.deferredPrompt = null;
            
        } catch (error) {
            console.error('PWA: Erreur lors de l\'installation:', error);
        }
    }
    
    showInstallationSuccess() {
        // Crée une notification de succès
        const successNotification = document.createElement('div');
        successNotification.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white p-6 rounded-lg shadow-xl z-50';
        successNotification.innerHTML = `
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-xl font-bold mb-2">Installation réussie !</h3>
                <p class="text-green-100">SAAR Sinistre est maintenant installé sur votre appareil.</p>
            </div>
        `;
        
        document.body.appendChild(successNotification);
        
        // Supprime après 3 secondes
        setTimeout(() => {
            successNotification.remove();
        }, 3000);
        
        // Masque tous les éléments d'installation
        this.hideInstallButton();
        this.hidePWABar();
    }
    
    async updateApp() {
        if (this.registration && this.registration.waiting) {
            // Envoie un message au service worker pour forcer la mise à jour
            this.registration.waiting.postMessage({ type: 'SKIP_WAITING' });
            
            // Recharge la page après la mise à jour
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
    
    // Méthodes utilitaires pour le débogage
    static isSupported() {
        return 'serviceWorker' in navigator && 'PushManager' in window;
    }
    
    static getInstallStatus() {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true;
    }
}

// Initialise la PWA quand le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    if (SAARSinistrePWA.isSupported()) {
        window.saarSinistrePWA = new SAARSinistrePWA();
    } else {
        console.log('PWA: Fonctionnalités PWA non supportées par ce navigateur');
    }
});

// Expose la classe globalement pour le débogage
window.SAARSinistrePWA = SAARSinistrePWA; 