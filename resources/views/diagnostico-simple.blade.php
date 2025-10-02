<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnóstico Push - ClicIdea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>🔔 Diagnóstico de Notificaciones Push</h3>
                    <p class="mb-0">Servidor: {{ request()->getHost() }} | Protocolo: {{ request()->secure() ? 'HTTPS ✅' : 'HTTP ⚠️' }}</p>
                </div>
                
                <div class="card-body">
                    <!-- Información básica -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>📊 Estado del Sistema</h5>
                            <div id="status-info" class="alert alert-info">
                                <div>🔍 Verificando permisos...</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>🧪 Pruebas Disponibles</h5>
                            <div class="d-grid gap-2">
                                <button id="btn-permisos" class="btn btn-warning btn-sm">🔒 Solicitar Permisos</button>
                                <button id="btn-basico" class="btn btn-primary btn-sm">🔔 Prueba Básica</button>
                                <button id="btn-push" class="btn btn-info btn-sm">📨 Prueba Push</button>
                                <button id="btn-incidencia" class="btn btn-success btn-sm">🎯 Prueba Incidencia</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Log de eventos -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h6>📝 Log de Eventos</h6>
                            <button id="btn-limpiar" class="btn btn-outline-secondary btn-sm">🧹 Limpiar</button>
                        </div>
                        <div class="card-body">
                            <div id="log-container" style="height: 250px; overflow-y: auto; background: #1a1a1a; color: #00ff00; padding: 10px; font-family: monospace; font-size: 12px; border-radius: 4px;">
                                <div>🚀 Sistema iniciado...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para agregar logs
function addLog(message, type = 'info') {
    const container = document.getElementById('log-container');
    const time = new Date().toLocaleTimeString();
    const colors = {
        info: '#00ff00',
        error: '#ff4444',
        warning: '#ffaa00',
        success: '#44ff44'
    };
    
    const logDiv = document.createElement('div');
    logDiv.style.color = colors[type] || '#00ff00';
    logDiv.innerHTML = `[${time}] ${message}`;
    
    container.appendChild(logDiv);
    container.scrollTop = container.scrollHeight;
}

// Función para actualizar estado
function updateStatus() {
    const statusDiv = document.getElementById('status-info');
    
    const notifications = Notification.permission;
    const serviceWorker = 'serviceWorker' in navigator;
    const pushSupported = 'PushManager' in window;
    const https = location.protocol === 'https:';
    
    statusDiv.innerHTML = `
        <div><strong>🔔 Notificaciones:</strong> <span class="badge bg-${notifications === 'granted' ? 'success' : notifications === 'denied' ? 'danger' : 'warning'}">${notifications}</span></div>
        <div><strong>👷 Service Worker:</strong> <span class="badge bg-${serviceWorker ? 'success' : 'danger'}">${serviceWorker ? 'Disponible' : 'No disponible'}</span></div>
        <div><strong>📡 Push API:</strong> <span class="badge bg-${pushSupported ? 'success' : 'danger'}">${pushSupported ? 'Soportado' : 'No soportado'}</span></div>
        <div><strong>🔒 HTTPS:</strong> <span class="badge bg-${https ? 'success' : 'warning'}">${https ? 'Seguro' : 'No seguro'}</span></div>
    `;
    
    addLog(`📊 Estado: Notificaciones=${notifications}, SW=${serviceWorker}, Push=${pushSupported}, HTTPS=${https}`);
}

// Event listeners
document.addEventListener('DOMContentLoaded', async function() {
    addLog('🔍 Verificando estado del sistema...');
    updateStatus();
    
    // Verificar Service Worker automáticamente
    if ('serviceWorker' in navigator) {
        try {
            addLog('🔍 Verificando Service Worker...');
            
            let registration = await navigator.serviceWorker.getRegistration();
            
            if (registration) {
                addLog('✅ Service Worker encontrado', 'success');
                addLog(`📍 Scope: ${registration.scope}`, 'info');
                addLog(`📍 Estado: ${registration.active ? 'Activo' : 'Inactivo'}`, 'info');
            } else {
                addLog('⚠️ Service Worker no está registrado', 'warning');
                addLog('🔧 Intentando registrar automáticamente...', 'info');
                
                try {
                    registration = await navigator.serviceWorker.register('/sw.js');
                    addLog('✅ Service Worker registrado automáticamente', 'success');
                    
                    // Esperar a que esté listo
                    await navigator.serviceWorker.ready;
                    addLog('✅ Service Worker está listo para usar', 'success');
                } catch (error) {
                    addLog(`❌ Error registrando Service Worker: ${error.message}`, 'error');
                    
                    // Verificar si el archivo existe
                    try {
                        const response = await fetch('/sw.js');
                        if (response.ok) {
                            addLog('✅ Archivo /sw.js existe y es accesible', 'info');
                            addLog('⚠️ Puede ser un problema de permisos o HTTPS', 'warning');
                        } else {
                            addLog(`❌ Archivo /sw.js retorna error HTTP: ${response.status}`, 'error');
                        }
                    } catch (fetchError) {
                        addLog(`❌ No se puede acceder a /sw.js: ${fetchError.message}`, 'error');
                    }
                }
            }
        } catch (error) {
            addLog(`❌ Error verificando Service Worker: ${error.message}`, 'error');
        }
    } else {
        addLog('❌ Service Workers no soportados en este navegador', 'error');
    }
    
    // Limpiar log
    document.getElementById('btn-limpiar').addEventListener('click', function() {
        document.getElementById('log-container').innerHTML = '<div>🧹 Log limpiado...</div>';
    });
    
    // Solicitar permisos
    document.getElementById('btn-permisos').addEventListener('click', async function() {
        addLog('🔒 Solicitando permisos...');
        
        if (!('Notification' in window)) {
            addLog('❌ Notificaciones no soportadas', 'error');
            return;
        }
        
        try {
            const permission = await Notification.requestPermission();
            addLog(`📋 Resultado: ${permission}`, permission === 'granted' ? 'success' : 'warning');
            updateStatus();
        } catch (error) {
            addLog(`❌ Error: ${error.message}`, 'error');
        }
    });
    
    // Prueba básica
    document.getElementById('btn-basico').addEventListener('click', function() {
        addLog('🔔 Enviando notificación básica...');
        
        if (Notification.permission !== 'granted') {
            addLog('❌ Permisos no concedidos', 'error');
            return;
        }
        
        try {
            const notification = new Notification('🧪 Prueba Básica', {
                body: 'Si ves esto, las notificaciones básicas funcionan',
                icon: '/images/lateral01.png'
            });
            
            addLog('✅ Notificación básica enviada', 'success');
            
            notification.onclick = function() {
                addLog('👆 Notificación clickeada', 'info');
                notification.close();
            };
        } catch (error) {
            addLog(`❌ Error: ${error.message}`, 'error');
        }
    });
    
    // Prueba push
    document.getElementById('btn-push').addEventListener('click', async function() {
        addLog('📨 Iniciando diagnóstico detallado de push notification...');
        
        if (Notification.permission !== 'granted') {
            addLog('❌ Permisos de notificación no concedidos', 'error');
            return;
        }
        
        try {
            // Verificar si Service Worker está soportado
            if (!('serviceWorker' in navigator)) {
                addLog('❌ Service Worker no soportado en este navegador', 'error');
                return;
            }
            
            addLog('🔍 Obteniendo registration del Service Worker...');
            
            const registration = await navigator.serviceWorker.getRegistration();
            
            if (!registration) {
                addLog('❌ No se pudo obtener registration del Service Worker', 'error');
                return;
            }
            
            addLog('✅ Registration obtenido correctamente', 'success');
            addLog(`📍 Scope: ${registration.scope}`, 'info');
            
            // Verificar el estado del Service Worker
            if (registration.installing) {
                addLog('⏳ Service Worker instalándose...', 'warning');
            } else if (registration.waiting) {
                addLog('⏳ Service Worker esperando...', 'warning');
            } else if (registration.active) {
                addLog('✅ Service Worker activo', 'success');
            } else {
                addLog('❌ Service Worker en estado desconocido', 'error');
            }
            
            // Verificar que showNotification esté disponible
            if (typeof registration.showNotification !== 'function') {
                addLog('❌ showNotification no está disponible en registration', 'error');
                return;
            }
            
            addLog('🔍 Método showNotification está disponible', 'success');
            
            // Intentar mostrar notificación con configuración mínima primero
            addLog('📨 Enviando notificación con configuración mínima...', 'info');
            
            try {
                await registration.showNotification('Test Mínimo');
                addLog('✅ Notificación mínima enviada exitosamente', 'success');
            } catch (minError) {
                addLog(`❌ Error en notificación mínima: ${minError.message}`, 'error');
                addLog(`� Stack: ${minError.stack}`, 'error');
                return;
            }
            
            // Si la mínima funcionó, probar con configuración completa
            addLog('📨 Enviando notificación con configuración completa...', 'info');
            
            const notificationOptions = {
                body: 'Esta es una notificación de prueba con configuración completa',
                icon: '/images/lateral01.png',
                badge: '/images/lateral01.png',
                tag: 'test-push-complete',
                requireInteraction: false, // Cambiar a false para ver si es problema de requireInteraction
                data: {
                    test: true,
                    timestamp: Date.now()
                },
                actions: [
                    { action: 'view', title: 'Ver' },
                    { action: 'close', title: 'Cerrar' }
                ]
            };
            
            addLog(`🔍 Opciones: ${JSON.stringify(notificationOptions, null, 2)}`, 'info');
            
            await registration.showNotification('🧪 Push Completo', notificationOptions);
            
            addLog('✅ Notificación completa enviada exitosamente', 'success');
            addLog('🎉 ¡Push notifications están funcionando!', 'success');
            
        } catch (error) {
            addLog(`❌ Error general en push notification: ${error.message}`, 'error');
            addLog(`🔍 Error name: ${error.name}`, 'error');
            addLog(`🔍 Stack trace: ${error.stack}`, 'error');
            
            // Información adicional del navegador
            addLog(`🔍 User Agent: ${navigator.userAgent}`, 'info');
            addLog(`🔍 Platform: ${navigator.platform}`, 'info');
        }
    });
    
    // Prueba incidencia
    document.getElementById('btn-incidencia').addEventListener('click', async function() {
        addLog('🎯 Iniciando test detallado de notificación de incidencia...');
        
        try {
            // Verificar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                addLog('❌ CSRF token no encontrado en la página', 'error');
                return;
            }
            
            addLog('✅ CSRF token encontrado', 'success');
            addLog(`🔍 Token: ${csrfToken.getAttribute('content').substring(0, 10)}...`, 'info');
            
            // Verificar la URL del endpoint
            const testUrl = '{{ route("test.notificacion-asignacion") }}';
            addLog(`🔍 URL del test: ${testUrl}`, 'info');
            
            addLog('📨 Enviando petición al servidor...', 'info');
            
            const response = await fetch(testUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    test: true,
                    timestamp: Date.now()
                })
            });
            
            addLog(`📊 Respuesta HTTP: ${response.status} ${response.statusText}`, 'info');
            
            if (!response.ok) {
                addLog(`❌ Error HTTP: ${response.status}`, 'error');
                
                // Intentar leer el cuerpo del error
                try {
                    const errorText = await response.text();
                    addLog(`🔍 Cuerpo del error: ${errorText.substring(0, 200)}...`, 'error');
                } catch (readError) {
                    addLog(`❌ No se pudo leer el cuerpo del error: ${readError.message}`, 'error');
                }
                return;
            }
            
            const responseData = await response.json();
            
            addLog('✅ Respuesta del servidor recibida', 'success');
            addLog(`📊 Datos de respuesta:`, 'info');
            addLog(`   - success: ${responseData.success}`, 'info');
            addLog(`   - message: ${responseData.message || 'N/A'}`, 'info');
            
            if (responseData.output) {
                addLog(`📋 Output del comando:`, 'info');
                addLog(`${responseData.output}`, 'info');
            }
            
            if (responseData.success) {
                addLog('🎉 Test de incidencia ejecutado exitosamente', 'success');
                addLog('⏳ Esperando notificación push...', 'info');
                
                // Timeout para verificar si llega la notificación
                setTimeout(() => {
                    addLog('🔍 Si no has visto una notificación push, puede ser un problema del WebPushChannel', 'warning');
                }, 3000);
            } else {
                addLog(`❌ Error en test de incidencia: ${responseData.error || 'Error desconocido'}`, 'error');
            }
            
        } catch (error) {
            addLog(`❌ Error de conexión o JavaScript: ${error.message}`, 'error');
            addLog(`🔍 Error name: ${error.name}`, 'error');
            addLog(`🔍 Stack: ${error.stack}`, 'error');
            
            // Verificar conectividad básica
            try {
                const pingResponse = await fetch('/', {method: 'HEAD'});
                addLog(`🔍 Conectividad básica: ${pingResponse.status}`, 'info');
            } catch (pingError) {
                addLog(`❌ Problema de conectividad: ${pingError.message}`, 'error');
            }
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/pwa-final.js"></script>
</body>
</html>