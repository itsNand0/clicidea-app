// 🔔 SCRIPT PARA VERIFICAR Y SOLICITAR PERMISOS DE NOTIFICACIONES
class NotificationPermissionManager {
    
    static async checkAndRequestPermissions() {
        console.log('🔍 Verificando permisos de notificaciones...');
        
        // Verificar si el navegador soporta notificaciones
        if (!('Notification' in window)) {
            console.error('❌ Este navegador no soporta notificaciones');
            return false;
        }
        
        // Verificar si el navegador soporta Service Workers
        if (!('serviceWorker' in navigator)) {
            console.error('❌ Este navegador no soporta Service Workers');
            return false;
        }
        
        console.log('📱 Estado actual de permisos:', Notification.permission);
        
        // Solicitar permisos si no están concedidos
        if (Notification.permission === 'default') {
            console.log('🔒 Solicitando permisos de notificaciones...');
            const permission = await Notification.requestPermission();
            console.log('📋 Resultado de solicitud:', permission);
            return permission === 'granted';
        }
        
        if (Notification.permission === 'denied') {
            console.error('🚫 Permisos de notificaciones DENEGADOS');
            alert('⚠️ Las notificaciones están bloqueadas. Para habilitarlas:\n\n' +
                  '1. Haz clic en el ícono de candado/información junto a la URL\n' +
                  '2. Cambia "Notificaciones" a "Permitir"\n' +
                  '3. Recarga la página');
            return false;
        }
        
        console.log('✅ Permisos de notificaciones CONCEDIDOS');
        return true;
    }
    
    static async testNotification() {
        const hasPermission = await this.checkAndRequestPermissions();
        
        if (hasPermission) {
            console.log('🧪 Enviando notificación de prueba...');
            
            // Verificar si hay Service Worker registrado
            const registration = await navigator.serviceWorker.getRegistration();
            
            if (registration) {
                console.log('✅ Service Worker encontrado:', registration);
                
                // Enviar notificación de prueba
                registration.showNotification('🧪 Prueba de Notificación', {
                    body: 'Si ves esto, las notificaciones funcionan correctamente',
                    icon: '/images/lateral01.png',
                    badge: '/images/lateral01.png',
                    tag: 'test-notification',
                    requireInteraction: true,
                    actions: [
                        {
                            action: 'close',
                            title: 'Cerrar'
                        }
                    ]
                });
                
                console.log('📨 Notificación de prueba enviada');
                return true;
            } else {
                console.error('❌ No se encontró Service Worker registrado');
                return false;
            }
        }
        
        return false;
    }
    
    static displayPermissionStatus() {
        const status = {
            notifications: Notification.permission,
            serviceWorker: 'serviceWorker' in navigator,
            pushSupported: 'PushManager' in window,
            https: location.protocol === 'https:'
        };
        
        console.log('📊 Estado de permisos y soporte:', status);
        
        // Mostrar estado en la interfaz
        const statusDiv = document.createElement('div');
        statusDiv.id = 'notification-status';
        statusDiv.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            background: #333;
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            z-index: 9999;
            max-width: 300px;
        `;
        
        statusDiv.innerHTML = `
            <h4>🔔 Estado de Notificaciones</h4>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Permisos: <strong>${status.notifications}</strong></li>
                <li>Service Worker: <strong>${status.serviceWorker ? '✅' : '❌'}</strong></li>
                <li>Push API: <strong>${status.pushSupported ? '✅' : '❌'}</strong></li>
                <li>HTTPS: <strong>${status.https ? '✅' : '❌'}</strong></li>
            </ul>
            <button onclick="NotificationPermissionManager.testNotification()" 
                    style="margin-top: 10px; padding: 5px 10px;">
                🧪 Probar Notificación
            </button>
            <button onclick="this.parentElement.remove()" 
                    style="margin-top: 5px; padding: 5px 10px; background: #666;">
                ✕ Cerrar
            </button>
        `;
        
        // Remover status anterior si existe
        const existing = document.getElementById('notification-status');
        if (existing) existing.remove();
        
        document.body.appendChild(statusDiv);
        
        return status;
    }
}

// Auto-inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Inicializando verificación de permisos de notificaciones...');
    NotificationPermissionManager.displayPermissionStatus();
});

// Exportar para uso global
window.NotificationPermissionManager = NotificationPermissionManager;