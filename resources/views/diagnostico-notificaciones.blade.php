<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnóstico de Notificaciones Push - ClicIdea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .log-entry { margin-bottom: 5px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>🔔 Diagnóstico de Notificaciones Push</h4>
                    <p class="mb-0">Verificación de permisos y configuración para notificaciones push</p>
                </div>
                
                <div class="card-body">
                    <!-- Estados de permisos -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5>📊 Estado Actual</h5>
                                </div>
                                <div class="card-body">
                                    <div id="status-display">
                                        <p>📡 Cargando información...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5>🧪 Pruebas</h5>
                                </div>
                                <div class="card-body">
                                    <button id="test-basic-notification" class="btn btn-primary btn-sm mb-2 d-block">
                                        🔔 Probar Notificación Básica
                                    </button>
                                    <button id="test-push-notification" class="btn btn-info btn-sm mb-2 d-block">
                                        📨 Probar Push Notification
                                    </button>
                                    <button id="test-incidencia-notification" class="btn btn-success btn-sm mb-2 d-block">
                                        🎯 Probar Notificación de Incidencia
                                    </button>
                                    <button id="request-permissions" class="btn btn-warning btn-sm mb-2 d-block">
                                        🔒 Solicitar Permisos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Log de eventos -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5>📝 Log de Eventos</h5>
                            <button id="clear-log" class="btn btn-outline-secondary btn-sm">Limpiar</button>
                        </div>
                        <div class="card-body">
                            <div id="event-log" style="height: 300px; overflow-y: auto; background: #1e1e1e; color: #fff; padding: 15px; font-family: monospace; font-size: 12px;">
                                <div class="log-entry">🚀 Sistema de diagnóstico inicializado...</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información del servidor -->
                    <div class="mt-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5>🌐 Información del Servidor</h5>
                                <p><strong>URL:</strong> <span id="current-url">{{ url('/') }}</span></p>
                                <p><strong>Protocolo:</strong> <span id="protocol">{{ request()->secure() ? 'HTTPS ✅' : 'HTTP ⚠️' }}</span></p>
                                <p><strong>Dominio:</strong> <span id="domain">{{ request()->getHost() }}</span></p>
                                <p class="mb-0"><strong>Timestamp:</strong> <span id="timestamp">{{ now()->format('Y-m-d H:i:s') }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de diagnóstico -->
<script>
class NotificationDiagnostic {
    constructor() {
        this.logContainer = document.getElementById('event-log');
        this.statusContainer = document.getElementById('status-display');
        this.initializeEventListeners();
        this.checkInitialStatus();
    }
    
    log(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const icons = {
            info: 'ℹ️',
            success: '✅',
            error: '❌',
            warning: '⚠️',
            debug: '🔍'
        };
        
        const logEntry = document.createElement('div');
        logEntry.className = 'log-entry';
        logEntry.style.color = type === 'error' ? '#ff6b6b' : 
                               type === 'success' ? '#51cf66' : 
                               type === 'warning' ? '#ffd43b' : '#74c0fc';
        logEntry.innerHTML = `[${timestamp}] ${icons[type] || 'ℹ️'} ${message}`;
        
        this.logContainer.appendChild(logEntry);
        this.logContainer.scrollTop = this.logContainer.scrollHeight;
    }
    
    updateStatus(status) {
        const statusHtml = `
            <div class="mb-3">
                <strong>🔔 Permisos de Notificación:</strong> 
                <span class="badge bg-${status.notifications === 'granted' ? 'success' : status.notifications === 'denied' ? 'danger' : 'warning'}">
                    ${status.notifications}
                </span>
            </div>
            <div class="mb-3">
                <strong>👷 Service Worker:</strong> 
                <span class="badge bg-${status.serviceWorker ? 'success' : 'danger'}">
                    ${status.serviceWorker ? 'Disponible' : 'No disponible'}
                </span>
            </div>
            <div class="mb-3">
                <strong>📡 Push API:</strong> 
                <span class="badge bg-${status.pushSupported ? 'success' : 'danger'}">
                    ${status.pushSupported ? 'Soportado' : 'No soportado'}
                </span>
            </div>
            <div class="mb-3">
                <strong>🔒 HTTPS:</strong> 
                <span class="badge bg-${status.https ? 'success' : 'warning'}">
                    ${status.https ? 'Seguro' : 'No seguro'}
                </span>
            </div>
            <div class="mb-0">
                <strong>📱 Navegador:</strong> 
                <span class="badge bg-info">${navigator.userAgent.split(') ')[0].split(' (')[0]}</span>
            </div>
        `;
        
        this.statusContainer.innerHTML = statusHtml;
    }
    
    async checkInitialStatus() {
        this.log('🔍 Verificando estado inicial del sistema...');
        
        const status = {
            notifications: Notification.permission,
            serviceWorker: 'serviceWorker' in navigator,
            pushSupported: 'PushManager' in window,
            https: location.protocol === 'https:'
        };
        
        this.updateStatus(status);
        this.log(`📊 Estado: Notificaciones=${status.notifications}, SW=${status.serviceWorker}, Push=${status.pushSupported}, HTTPS=${status.https}`);
        
        // Verificar Service Worker registrado
        if (status.serviceWorker) {
            try {
                const registration = await navigator.serviceWorker.getRegistration();
                if (registration) {
                    this.log('✅ Service Worker está registrado', 'success');
                    this.log(`📍 Scope: ${registration.scope}`, 'debug');
                } else {
                    this.log('⚠️ Service Worker no está registrado', 'warning');
                }
            } catch (error) {
                this.log(`❌ Error verificando Service Worker: ${error.message}`, 'error');
            }
        }
    }
    
    initializeEventListeners() {
        // Limpiar log
        document.getElementById('clear-log').addEventListener('click', () => {
            this.logContainer.innerHTML = '<div class="log-entry">🧹 Log limpiado...</div>';
        });
        
        // Solicitar permisos
        document.getElementById('request-permissions').addEventListener('click', async () => {
            this.log('🔒 Solicitando permisos de notificaciones...');
            
            if (!('Notification' in window)) {
                this.log('❌ Este navegador no soporta notificaciones', 'error');
                return;
            }
            
            try {
                const permission = await Notification.requestPermission();
                this.log(`📋 Resultado: ${permission}`, permission === 'granted' ? 'success' : 'warning');
                this.checkInitialStatus(); // Actualizar estado
            } catch (error) {
                this.log(`❌ Error solicitando permisos: ${error.message}`, 'error');
            }
        });
        
        // Probar notificación básica
        document.getElementById('test-basic-notification').addEventListener('click', async () => {
            this.log('🔔 Enviando notificación básica...');
            
            if (Notification.permission !== 'granted') {
                this.log('❌ Permisos no concedidos', 'error');
                return;
            }
            
            try {
                const notification = new Notification('🧪 Prueba Básica', {
                    body: 'Esta es una notificación de prueba básica',
                    icon: '/images/lateral01.png'
                });
                
                this.log('✅ Notificación básica enviada', 'success');
                
                notification.onclick = () => {
                    this.log('👆 Notificación básica clickeada', 'info');
                    notification.close();
                };
            } catch (error) {
                this.log(`❌ Error enviando notificación básica: ${error.message}`, 'error');
            }
        });
        
        // Probar push notification
        document.getElementById('test-push-notification').addEventListener('click', async () => {
            this.log('📨 Enviando push notification...');
            
            try {
                const registration = await navigator.serviceWorker.getRegistration();
                
                if (!registration) {
                    this.log('❌ Service Worker no está registrado', 'error');
                    return;
                }
                
                await registration.showNotification('🧪 Push de Prueba', {
                    body: 'Esta es una push notification de prueba',
                    icon: '/images/lateral01.png',
                    badge: '/images/lateral01.png',
                    tag: 'test-push',
                    requireInteraction: true,
                    actions: [
                        { action: 'view', title: 'Ver' },
                        { action: 'close', title: 'Cerrar' }
                    ]
                });
                
                this.log('✅ Push notification enviada', 'success');
            } catch (error) {
                this.log(`❌ Error enviando push notification: ${error.message}`, 'error');
            }
        });
        
        // Probar notificación de incidencia
        document.getElementById('test-incidencia-notification').addEventListener('click', async () => {
            this.log('🎯 Ejecutando comando de prueba de incidencia...');
            
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
                    this.log('✅ Comando de prueba ejecutado correctamente', 'success');
                    this.log(`📊 Resultado: ${JSON.stringify(data)}`, 'debug');
                } else {
                    this.log(`❌ Error en comando de prueba: ${data.error || 'Error desconocido'}`, 'error');
                }
            } catch (error) {
                this.log(`❌ Error ejecutando comando: ${error.message}`, 'error');
            }
        });
    }
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    new NotificationDiagnostic();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>