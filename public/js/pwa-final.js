/**
 * Eglobal PWA Manager - Versión final limpia
 * Solo funcionalidad esencial de PWA sin botones de debug
 */
class ClicIdeaPWA {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.init();
    }

    async init() {
        console.log('🚀 Iniciando Eglobal PWA...');
        
        // Registrar Service Worker
        if ('serviceWorker' in navigator) {
            try {
                console.log('🔄 Registrando Service Worker...');
                const registration = await navigator.serviceWorker.register('/sw.js', {
                    scope: '/'
                });
                console.log('✅ Service Worker registrado exitosamente:', registration);
                
            } catch (error) {
                console.error('❌ Error registrando Service Worker:', error);
            }
        } else {
            console.warn('⚠️ Service Worker no disponible');
        }

        // Escuchar evento de instalación
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('🎯 beforeinstallprompt event fired!');
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
            console.log('✅ App ya está instalada (standalone mode)');
        }
    }

    // Mostrar banner de instalación
    showInstallBanner() {
        console.log('🎯 Mostrando banner de instalación...');
        
        if (this.isInstalled || !this.deferredPrompt) {
            console.log('❌ No se muestra banner - App ya instalada o no hay prompt');
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
                    <div class="font-semibold">Instalar Eglobal</div>
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
        
        console.log('✅ Banner creado y agregado al DOM');

        // Event listeners
        const installBtn = document.getElementById('pwa-install-btn');
        const closeBtn = document.getElementById('pwa-close-btn');
        
        if (installBtn) {
            installBtn.addEventListener('click', (e) => {
                console.log('🔘 Click en botón instalar');
                e.preventDefault();
                this.install();
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                console.log('🔘 Click en botón cerrar');
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
        console.log('🔄 Intentando instalar PWA...');
        
        if (!this.deferredPrompt) {
            console.log('❌ No hay prompt disponible');
            this.showNotification('Prompt de instalación no disponible', 'warning');
            return;
        }

        try {
            // Mostrar el prompt nativo
            this.deferredPrompt.prompt();
            
            // Esperar respuesta del usuario
            const { outcome } = await this.deferredPrompt.userChoice;
            console.log(`Usuario ${outcome} la instalación`);
            
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
            console.error('Error durante instalación:', error);
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
                    console.error('Error configurando push:', error);
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
            new Notification('Eglobal - Prueba', {
                body: 'Las notificaciones están funcionando correctamente',
                icon: '/images/icons/Eglobal1.jpeg',
                badge: '/images/icons/Eglobal1.jpeg'
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
