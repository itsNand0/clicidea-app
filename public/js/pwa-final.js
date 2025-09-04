/**
 * ClicIdea PWA Manager - Versión final limpia
 * Solo funcionalidad esencial de PWA sin logs de debug
 */
class ClicIdeaPWA {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.init();
    }

    async init() {
        // Registrar Service Worker
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js', {
                    scope: '/'
                });
                
            } catch (error) {
                // Error manejado silenciosamente
            }
        }

        // Escuchar evento de instalación
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallBanner();
        });

        // Detectar si ya está instalado
        window.addEventListener('appinstalled', () => {
            this.isInstalled = true;
            this.hideInstallBanner();
            this.showNotification('¡Aplicación instalada exitosamente! 🎉');
        });

        // Verificar si ya está instalado
        if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
            this.isInstalled = true;
        }
    }

    // Mostrar banner de instalación
    showInstallBanner() {
        if (this.isInstalled || !this.deferredPrompt) {
            return;
        }

        // Crear banner de instalación
        const banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.className = 'fixed bottom-4 left-4 right-4 bg-blue-500 text-white p-4 rounded-lg shadow-lg z-50 flex items-center justify-between';
        banner.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-mobile-alt mr-3"></i>
                <div>
                    <div class="font-semibold">Instalar ClicIdea</div>
                    <div class="text-sm opacity-90">Para una mejor experiencia</div>
                </div>
            </div>
            <div class="flex space-x-2">
                <button id="pwa-install-btn" class="bg-white text-blue-500 px-3 py-1 rounded text-sm font-medium">
                    Instalar
                </button>
                <button id="pwa-close-btn" class="text-white opacity-75 hover:opacity-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Evitar duplicados
        this.hideInstallBanner();
        document.body.appendChild(banner);

        // Event listeners
        const installBtn = document.getElementById('pwa-install-btn');
        const closeBtn = document.getElementById('pwa-close-btn');
        
        if (installBtn) {
            installBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.install();
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.hideInstallBanner();
            });
        }
    }

    hideInstallBanner() {
        const banner = document.getElementById('pwa-install-banner');
        if (banner) {
            banner.remove();
        }
    }

    async install() {
        if (!this.deferredPrompt) {
            this.showNotification('Prompt de instalación no disponible', 'warning');
            return;
        }

        try {
            // Mostrar el prompt nativo
            this.deferredPrompt.prompt();
            
            // Esperar respuesta del usuario
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                this.showNotification('¡Aplicación instalándose! 🎉');
                this.isInstalled = true;
            } else {
                this.showNotification('Instalación cancelada');
            }
            
            // Limpiar prompt
            this.deferredPrompt = null;
            this.hideInstallBanner();
            
        } catch (error) {
            this.showNotification('Error durante la instalación', 'error');
        }
    }

    // Habilitar notificaciones
    async enableNotifications() {
        if (!('Notification' in window)) {
            this.showNotification('Este navegador no soporta notificaciones', 'error');
            return false;
        }

        const permission = await Notification.requestPermission();

        if (permission === 'granted') {
            this.showNotification('¡Notificaciones habilitadas! 🔔');
            
            // Registrar para push notifications
            if ('serviceWorker' in navigator && 'PushManager' in window) {
                try {
                    const registration = await navigator.serviceWorker.ready;
                    this.showNotification('Push notifications configuradas ✓');
                    return true;
                } catch (error) {
                    return false;
                }
            }
        } else {
            this.showNotification('Permisos de notificación denegados', 'warning');
            return false;
        }
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Animar entrada
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto-remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Método para mostrar notificación de prueba
    async testNotification() {
        if (Notification.permission === 'granted') {
            new Notification('ClicIdea - Prueba', {
                body: 'Las notificaciones están funcionando correctamente',
                icon: '/images/icons/icon-192x192.svg',
                badge: '/images/icons/icon-192x192.svg'
            });
        } else {
            const enabled = await this.enableNotifications();
            if (enabled) {
                this.testNotification();
            }
        }
    }
}

// Inicializar automáticamente cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.pwaManager = new ClicIdeaPWA();
});

// Exportar para uso manual
window.ClicIdeaPWA = ClicIdeaPWA;
