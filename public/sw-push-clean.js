// Service Worker para Push Notifications
const CACHE_NAME = 'clicidea-push-v1';

// Instalar service worker
self.addEventListener('install', event => {
    self.skipWaiting();
});

// Activar service worker
self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

// Escuchar mensajes push
self.addEventListener('push', event => {
    let data = {};
    
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = {
                title: 'ClicIdea',
                body: event.data.text() || 'Nueva notificación',
                icon: '/images/lateral01.png'
            };
        }
    } else {
        data = {
            title: 'ClicIdea',
            body: 'Nueva notificación',
            icon: '/images/lateral01.png'
        };
    }
    
    const options = {
        body: data.body || 'Nueva notificación de ClicIdea',
        icon: data.icon || '/images/lateral01.png',
        badge: data.badge || '/images/lateral01.png',
        tag: data.tag || 'clicidea-notification',
        data: data.data || {},
        actions: data.actions || [],
        requireInteraction: true,
        vibrate: data.vibrate || [200, 100, 200, 100, 200],
        silent: false,
        timestamp: Date.now()
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title || 'ClicIdea', options)
    );
});

// Manejar clic en notificación
self.addEventListener('notificationclick', event => {
    event.notification.close();
    
    // Obtener URL de la data
    const urlToOpen = event.notification.data?.url || '/dashboard';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Si ya hay una ventana abierta, enfocarla
                for (const client of clientList) {
                    if (client.url.includes(urlToOpen) && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // Si no, abrir nueva ventana
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Manejar cierre de notificación
self.addEventListener('notificationclose', event => {
    // Aquí puedes enviar analytics si quieres
    // fetch('/api/notification-closed', { method: 'POST', ... });
});
