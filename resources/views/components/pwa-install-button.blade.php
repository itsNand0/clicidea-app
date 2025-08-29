<!-- Componente de Botón de Instalación PWA -->
<div id="pwa-install-button" style="display: none;" class="fixed bottom-4 right-4 z-50">
    <button onclick="installPWA()" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg shadow-md transform transition hover:scale-105 flex items-center space-x-2 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Instalar</span>
    </button>
</div>

<script>
    // Función global para instalar PWA
    window.installPWA = function() {
        if (window.deferredPrompt) {
            console.log('🚀 PWA: Usando prompt nativo');
            window.deferredPrompt.prompt();
        } else {
            console.log('🚀 PWA: Redirigiendo a página de instalación');
            // Redirigir a página de instalación
            window.location.href = '/pwa/install';
        }
    };
    
    // Mostrar el botón solo si la PWA se puede instalar
    window.addEventListener('load', () => {
        setTimeout(() => {
            // Verificar si puede instalarse
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
            const isIOSStandalone = window.navigator.standalone;
            const hasPrompt = localStorage.getItem('pwa-prompt-available') === 'true' || window.deferredPrompt;
            
            // Solo mostrar si no está instalada y hay alguna forma de instalar
            if (!isStandalone && !isIOSStandalone && (hasPrompt || !isIOSStandalone)) {
                document.getElementById('pwa-install-button').style.display = 'block';
                console.log('🔵 PWA: Botón de instalación mostrado');
            } else {
                console.log('🔴 PWA: Botón no mostrado - Ya instalada o no disponible');
            }
        }, 1000);
    });
    
    // Detectar cuando el prompt esté disponible
    window.addEventListener('beforeinstallprompt', () => {
        localStorage.setItem('pwa-prompt-available', 'true');
        document.getElementById('pwa-install-button').style.display = 'block';
        console.log('🔵 PWA: Botón mostrado - Prompt disponible');
    });
    
    // Ocultar el botón cuando se instale
    window.addEventListener('appinstalled', () => {
        document.getElementById('pwa-install-button').style.display = 'none';
        localStorage.removeItem('pwa-prompt-available');
        console.log('✅ PWA: Botón oculto - App instalada');
    });
</script>
