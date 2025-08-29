<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico PWA - ClicIdea</title>
    <x-pwa-meta />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">🔧 Diagnóstico PWA</h1>
            <p class="text-gray-600 mb-6">Verifica el estado de tu Progressive Web App</p>
            
            <div id="diagnostics" class="space-y-4">
                <!-- Los resultados se cargarán aquí -->
            </div>
            
            <div class="mt-6 flex gap-4">
                <button id="runDiagnostics" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                    🔍 Ejecutar Diagnóstico
                </button>
                <button id="forceInstallPrompt" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                    📱 Forzar Banner Instalación
                </button>
                <button id="clearCache" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600">
                    🗑️ Limpiar Cache
                </button>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">📝 Log de Eventos</h2>
            <div id="eventLog" class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm h-64 overflow-y-auto">
                <!-- Los logs aparecerán aquí -->
            </div>
        </div>
    </div>

    <script>
        const diagnosticsContainer = document.getElementById('diagnostics');
        const eventLog = document.getElementById('eventLog');
        let originalConsoleLog = console.log;
        let originalConsoleError = console.error;
        
        // Interceptar console.log para mostrarlo en el log
        console.log = function(...args) {
            originalConsoleLog.apply(console, arguments);
            logEvent('LOG', args.join(' '));
        };
        
        console.error = function(...args) {
            originalConsoleError.apply(console, arguments);
            logEvent('ERROR', args.join(' '));
        };
        
        function logEvent(type, message) {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.innerHTML = `<span class="text-gray-400">[${timestamp}]</span> <span class="${type === 'ERROR' ? 'text-red-400' : 'text-green-400'}">${type}:</span> ${message}`;
            eventLog.appendChild(logEntry);
            eventLog.scrollTop = eventLog.scrollHeight;
        }
        
        async function runDiagnostics() {
            diagnosticsContainer.innerHTML = '';
            logEvent('INFO', 'Iniciando diagnóstico PWA...');
            
            const checks = [
                {
                    name: 'Service Worker',
                    test: () => 'serviceWorker' in navigator,
                    description: 'Soporte para Service Workers'
                },
                {
                    name: 'Manifest',
                    test: async () => {
                        try {
                            const response = await fetch('/manifest.json');
                            return response.ok;
                        } catch (e) {
                            return false;
                        }
                    },
                    description: 'Archivo manifest.json accesible'
                },
                {
                    name: 'SW Registrado',
                    test: async () => {
                        if ('serviceWorker' in navigator) {
                            const registration = await navigator.serviceWorker.getRegistration();
                            return !!registration;
                        }
                        return false;
                    },
                    description: 'Service Worker registrado'
                },
                {
                    name: 'HTTPS',
                    test: () => location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname.endsWith('.test'),
                    description: 'Conexión segura (HTTPS/localhost)'
                },
                {
                    name: 'Icons',
                    test: async () => {
                        try {
                            const response = await fetch('/images/icons/icon-192x192.svg');
                            return response.ok;
                        } catch (e) {
                            return false;
                        }
                    },
                    description: 'Iconos PWA accesibles'
                },
                {
                    name: 'Display Standalone',
                    test: () => window.matchMedia('(display-mode: standalone)').matches,
                    description: 'Ejecutándose como PWA instalada'
                },
                {
                    name: 'beforeinstallprompt',
                    test: () => window.deferredPrompt !== undefined || localStorage.getItem('pwa-prompt-shown') === 'true',
                    description: 'Banner de instalación disponible'
                }
            ];
            
            for (const check of checks) {
                const result = await check.test();
                const checkElement = document.createElement('div');
                checkElement.className = `flex items-center justify-between p-4 rounded-lg ${result ? 'bg-green-100 border border-green-300' : 'bg-red-100 border border-red-300'}`;
                checkElement.innerHTML = `
                    <div>
                        <div class="font-semibold ${result ? 'text-green-800' : 'text-red-800'}">${check.name}</div>
                        <div class="text-sm ${result ? 'text-green-600' : 'text-red-600'}">${check.description}</div>
                    </div>
                    <div class="text-2xl">${result ? '✅' : '❌'}</div>
                `;
                diagnosticsContainer.appendChild(checkElement);
                
                logEvent('CHECK', `${check.name}: ${result ? 'PASS' : 'FAIL'}`);
            }
            
            // Información adicional
            const infoElement = document.createElement('div');
            infoElement.className = 'bg-blue-100 border border-blue-300 p-4 rounded-lg';
            infoElement.innerHTML = `
                <div class="font-semibold text-blue-800">Información del Navegador</div>
                <div class="text-sm text-blue-600 mt-2">
                    <div><strong>User Agent:</strong> ${navigator.userAgent}</div>
                    <div><strong>URL:</strong> ${location.href}</div>
                    <div><strong>Protocolo:</strong> ${location.protocol}</div>
                    <div><strong>Host:</strong> ${location.host}</div>
                </div>
            `;
            diagnosticsContainer.appendChild(infoElement);
            
            logEvent('INFO', 'Diagnóstico completado');
        }
        
        function forceInstallPrompt() {
            logEvent('INFO', 'Intentando forzar banner de instalación...');
            
            // Simular el evento beforeinstallprompt si no existe
            if (!window.deferredPrompt) {
                logEvent('INFO', 'Creando evento simulado...');
                window.dispatchEvent(new Event('beforeinstallprompt'));
            } else {
                logEvent('INFO', 'Mostrando prompt existente...');
                window.deferredPrompt.prompt();
            }
        }
        
        async function clearCache() {
            logEvent('INFO', 'Limpiando cache...');
            
            if ('serviceWorker' in navigator) {
                const registration = await navigator.serviceWorker.getRegistration();
                if (registration) {
                    await registration.unregister();
                    logEvent('INFO', 'Service Worker desregistrado');
                }
            }
            
            if ('caches' in window) {
                const cacheNames = await caches.keys();
                await Promise.all(cacheNames.map(name => caches.delete(name)));
                logEvent('INFO', `${cacheNames.length} caches eliminados`);
            }
            
            localStorage.clear();
            sessionStorage.clear();
            logEvent('INFO', 'Storage limpiado');
            
            alert('Cache limpiado. Recarga la página para reregistrar el SW.');
        }
        
        // Event listeners
        document.getElementById('runDiagnostics').addEventListener('click', runDiagnostics);
        document.getElementById('forceInstallPrompt').addEventListener('click', forceInstallPrompt);
        document.getElementById('clearCache').addEventListener('click', clearCache);
        
        // Ejecutar diagnóstico automáticamente
        window.addEventListener('load', () => {
            setTimeout(runDiagnostics, 1000);
        });
        
        logEvent('INFO', 'Página de diagnóstico cargada');
    </script>
</body>
</html>
