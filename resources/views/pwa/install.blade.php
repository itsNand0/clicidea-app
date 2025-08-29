<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalar ClicIdea App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#3b82f6">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-mobile-alt text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">ClicIdea PWA</h1>
                <p class="text-gray-600 mt-2">Instala nuestra aplicación para una mejor experiencia</p>
            </div>

            <div class="space-y-4">
                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                    <i class="fas fa-check text-green-500 mr-3"></i>
                    <span class="text-sm text-green-700">Funciona sin conexión</span>
                </div>
                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                    <i class="fas fa-bell text-green-500 mr-3"></i>
                    <span class="text-sm text-green-700">Notificaciones push</span>
                </div>
                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                    <i class="fas fa-rocket text-green-500 mr-3"></i>
                    <span class="text-sm text-green-700">Carga más rápida</span>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <button id="installBtn" class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-600 transition-colors" style="display: none;">
                    Instalar Aplicación
                </button>
                
                <button id="enableNotifications" class="w-full bg-green-500 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-600 transition-colors">
                    Habilitar Notificaciones
                </button>

                <a href="/" class="block w-full text-center bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Continuar en el navegador
                </a>
            </div>

            <div id="status" class="mt-4 text-sm text-center text-gray-600"></div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
    <script>
        let deferredPrompt;
        const installBtn = document.getElementById('installBtn');
        const enableNotificationsBtn = document.getElementById('enableNotifications');
        const status = document.getElementById('status');

        // Registrar Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('SW registrado:', registration);
                    status.textContent = 'Service Worker registrado ✓';
                })
                .catch(error => {
                    console.error('Error registrando SW:', error);
                    status.textContent = 'Error registrando Service Worker ✗';
                });
        }

        // Evento de instalación PWA
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installBtn.style.display = 'block';
        });

        // Manejar click de instalación
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                status.textContent = 'Aplicación instalada ✓';
            } else {
                status.textContent = 'Instalación cancelada';
            }
            
            deferredPrompt = null;
            installBtn.style.display = 'none';
        });

        // Habilitar notificaciones
        enableNotificationsBtn.addEventListener('click', async () => {
            if (!('Notification' in window)) {
                status.textContent = 'Este navegador no soporta notificaciones';
                return;
            }

            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                status.textContent = 'Notificaciones habilitadas ✓';
                
                // Registrar token para push notifications
                if ('serviceWorker' in navigator && 'PushManager' in window) {
                    try {
                        const registration = await navigator.serviceWorker.ready;
                        const subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            // applicationServerKey: urlBase64ToUint8Array('YOUR_VAPID_PUBLIC_KEY') // Comentado hasta configurar VAPID
                        });
                        
                        // Enviar subscription al servidor
                        await fetch('/pwa/register-push-token', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                token: JSON.stringify(subscription)
                            })
                        });
                        
                        status.textContent = 'Push notifications configuradas ✓';
                    } catch (error) {
                        console.error('Error configurando push:', error);
                        status.textContent = 'Error configurando push notifications';
                    }
                }
            } else {
                status.textContent = 'Permisos de notificación denegados';
            }
        });

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    </script>
</body>
</html>
