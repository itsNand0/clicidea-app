// public/js/push-notifications.js

class PushNotificationManager {
    constructor() {
        this.publicKey = 'BK_j0RO4ydoPlS3Hmh_3MLhFQhl_mU5buRTy66WnOpqyOAwk0oYjHgoSZgKXMkTUjN0DmUPUNqgs2Mdc_leWCSU'; // Clave VAPID pública real
        this.subscriptions = new Map();
    }

    async init() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            return false;
        }

        try {
            // Registrar service worker específico para push notifications
            const registration = await navigator.serviceWorker.register('/sw-push.js', {
                scope: '/'
            });

            // Esperar a que se active
            await navigator.serviceWorker.ready;

            // Verificar permiso
            await this.requestPermission();
            
            return true;
        } catch (error) {
            return false;
        }
    }

    async requestPermission() {
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            await this.subscribeToPush();
        }
        
        return permission;
    }

    async subscribeToPush() {
        try {
            const registration = await navigator.serviceWorker.ready;
            
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.publicKey)
            });

            // Enviar suscripción al servidor
            await this.sendSubscriptionToServer(subscription);
            
        } catch (error) {
            // Error silenciado
        }
    }

    async sendSubscriptionToServer(subscription) {
        try {
            // Para testing, usar endpoint público sin autenticación
            const response = await fetch('/api/web-push/subscribe-public', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: this.arrayBufferToBase64(subscription.getKey('p256dh')),
                        auth: this.arrayBufferToBase64(subscription.getKey('auth'))
                    },
                    contentEncoding: 'aesgcm'
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
        } catch (error) {
            // Error silenciado
        }
    }

    urlBase64ToUint8Array(base64String) {
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

    arrayBufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }
}

// Inicializar cuando la página cargue
document.addEventListener('DOMContentLoaded', async () => {
    const pushManager = new PushNotificationManager();
    const initialized = await pushManager.init();
    
    if (initialized) {
        // Exponer globalmente para testing
        window.pushManager = pushManager;
    }
});

// Testing: mostrar notificación de prueba
window.testNotification = function() {
    if (!('Notification' in window)) {
        alert('❌ Tu navegador no soporta notificaciones');
        return;
    }
    
    if (Notification.permission === 'granted') {
        const notification = new Notification('🧪 Test ClicIdea', {
            body: 'Sistema de notificaciones funcionando correctamente',
            icon: '/images/lateral01.png',
            badge: '/images/lateral01.png',
            tag: 'test',
            requireInteraction: false,
            timestamp: Date.now()
        });
        
        notification.onclick = function() {
            window.focus();
            notification.close();
        };
        
    } else if (Notification.permission === 'default') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                const notification = new Notification('🧪 Test ClicIdea', {
                    body: 'Permisos concedidos! Sistema funcionando correctamente',
                    icon: '/images/lateral01.png',
                    tag: 'test-permissions'
                });
            } else {
                alert('❌ Para recibir notificaciones, debes permitir las notificaciones en tu navegador');
            }
        }).catch(err => {
            // Error silenciado
        });
        
    } else {
        alert('❌ Las notificaciones están bloqueadas. Ve a configuración del navegador para habilitarlas.');
    }
};

// === FUNCIÓN DE TEST SIMPLE ===
window.testSimpleEndpoint = async function() {
    try {
        const response = await fetch('/api/web-push/test-simple', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        return data;

    } catch (error) {
        throw error;
    }
};

// === FUNCIÓN DE TEST INCIDENCIA ===
window.testIncidenciaNotification = async function() {
    try {
        const response = await fetch('/api/web-push/test-incidencia', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        return data;

    } catch (error) {
        throw error;
    }
};

// 🔍 DIAGNÓSTICO COMPLETO
function diagnosticoPushNotifications() {
    // Función disponible para debugging manual cuando se necesite
    return {
        protocol: window.location.protocol,
        notificationAPI: 'Notification' in window,
        serviceWorker: 'serviceWorker' in navigator,
        pushManager: 'PushManager' in window,
        permission: 'Notification' in window ? Notification.permission : 'unknown',
        userAgent: navigator.userAgent,
        platform: navigator.platform
    };
}
