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
document.addEventListener('DOMContentLoaded', function() {
    addLog('🔍 Verificando estado del sistema...');
    updateStatus();
    
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
        addLog('📨 Enviando push notification...');
        
        try {
            const registration = await navigator.serviceWorker.getRegistration();
            
            if (!registration) {
                addLog('❌ Service Worker no registrado', 'error');
                return;
            }
            
            await registration.showNotification('🧪 Push de Prueba', {
                body: 'Si ves esto, las push notifications funcionan',
                icon: '/images/lateral01.png',
                badge: '/images/lateral01.png',
                tag: 'test-push',
                requireInteraction: true
            });
            
            addLog('✅ Push notification enviada', 'success');
        } catch (error) {
            addLog(`❌ Error: ${error.message}`, 'error');
        }
    });
    
    // Prueba incidencia
    document.getElementById('btn-incidencia').addEventListener('click', async function() {
        addLog('🎯 Ejecutando test de incidencia...');
        
        try {
            const response = await fetch('{{ route("test.notificacion-asignacion") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (response.ok) {
                addLog('✅ Test de incidencia ejecutado', 'success');
                addLog(`📊 Resultado: ${JSON.stringify(data).substring(0, 100)}...`);
            } else {
                addLog(`❌ Error en test: ${data.error || 'Error desconocido'}`, 'error');
            }
        } catch (error) {
            addLog(`❌ Error de conexión: ${error.message}`, 'error');
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>