// PWA Installation Handler
class PWAInstaller {
    constructor() {
        this.deferredPrompt = null;
        this.init();
    }

    init() {
        // Escuchar el evento beforeinstallprompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
        });

        // Escuchar cuando la app es instalada
        window.addEventListener('appinstalled', () => {
            this.hideInstallButton();
            this.deferredPrompt = null;
        });

        // Verificar si ya está instalada
        this.checkIfInstalled();
    }

    showInstallButton() {
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'block';
            installBtn.onclick = () => this.installPWA();
        }
    }

    hideInstallButton() {
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'none';
        }
    }

    async installPWA() {
        if (!this.deferredPrompt) {
            return;
        }

        try {
            // Mostrar el prompt de instalación
            this.deferredPrompt.prompt();
            
            // Esperar la respuesta del usuario
            const { outcome } = await this.deferredPrompt.userChoice;
            
            this.deferredPrompt = null;
        } catch (error) {
            // Error manejado silenciosamente
        }
    }

    checkIfInstalled() {
        // Verificar si la app está corriendo en modo standalone
        if (window.matchMedia('(display-mode: standalone)').matches || 
            window.navigator.standalone === true) {
            this.hideInstallButton();
        }
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new PWAInstaller());
} else {
    new PWAInstaller();
}
