const CACHE_NAME = 'clicidea-v3';
const urlsToCache = [
    '/',
    '/manifest.json',
    '/images/icons/icon-192x192.svg',
    '/images/icons/icon-512x512.svg',
    '/offline.html'
];

// Instalar Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache)
                    .catch(error => {
                        return Promise.all([
                            cache.add('/').catch(() => console.log('/ no encontrado')),
                            cache.add('/manifest.json').catch(() => console.log('manifest.json no encontrado')),
                            cache.add('/offline.html').catch(() => console.log('offline.html no encontrado')),
                            cache.add('/images/icons/icon-192x192.svg').catch(() => console.log('icono 192 no encontrado')),
                            cache.add('/images/icons/icon-512x512.svg').catch(() => console.log('icono 512 no encontrado')),
                        ]);
                    });
            })
    );
    // Forzar la activación inmediata
    self.skipWaiting();
});

// Activar Service Worker
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    // Tomar control inmediatamente
    self.clients.claim();
});

// Interceptar peticiones
self.addEventListener('fetch', event => {
    // Solo manejar peticiones GET para evitar problemas con CSRF
    if (event.request.method !== 'GET') {
        return; // Dejar que Laravel maneje las peticiones POST/PUT/DELETE normalmente
    }
    
    // Ignorar peticiones de extensiones del navegador
    if (event.request.url.startsWith('chrome-extension://') || 
        event.request.url.startsWith('moz-extension://')) return;
    
    // Ignorar peticiones a rutas que requieren autenticación
    const url = new URL(event.request.url);
    const ignoredPaths = ['/pwa/register-push-token', '/pwa/test-push', '/logout'];
    if (ignoredPaths.some(path => url.pathname.includes(path))) {
        return; // Dejar que Laravel maneje estas rutas
    }
    
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Si está en cache, devolver desde cache
                if (response) {
                    return response;
                }
                
                // Si no está en cache, hacer fetch
                return fetch(event.request)
                    .then(response => {
                        // Solo cachear respuestas exitosas
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }
                        
                        // No cachear si hay errores de autenticación
                        if (response.status === 419 || response.status === 401 || response.status === 403) {
                            return response;
                        }
                        
                        // Clonar la respuesta
                        const responseToCache = response.clone();
                        
                        // Añadir al cache si es una URL que queremos cachear
                        if (shouldCache(event.request.url)) {
                            caches.open(CACHE_NAME)
                                .then(cache => {
                                    cache.put(event.request, responseToCache);
                                });
                        }
                        
                        return response;
                    })
                    .catch(error => {
                        // Si es una petición de navegación, servir página offline
                        if (event.request.destination === 'document') {
                            return caches.match('/offline.html');
                        }
                        // Para otros recursos, devolver respuesta de error
                        return new Response('Recurso no disponible offline', {
                            status: 503,
                            statusText: 'Service Unavailable',
                            headers: new Headers({
                                'Content-Type': 'text/plain'
                            })
                        });
                    });
            })
    );
});

// Función para determinar si una URL debe ser cacheada
function shouldCache(url) {
    // Cachear recursos estáticos comunes
    return url.includes('.css') || 
           url.includes('.js') || 
           url.includes('.png') || 
           url.includes('.jpg') || 
           url.includes('.jpeg') || 
           url.includes('.svg') ||
           url.includes('manifest.json');
}

// Manejar notificaciones push
self.addEventListener('push', event => {
    const options = {
        body: event.data ? event.data.text() : 'Nueva notificación',
        icon: '/images/icons/icon-192x192.svg',
        badge: '/images/icons/icon-192x192.svg',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Ver incidencia',
                icon: '/images/icons/icon-192x192.svg'
            },
            {
                action: 'close',
                title: 'Cerrar',
                icon: '/images/icons/icon-192x192.svg'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('ClicIdea - Incidencias', options)
    );
});

// Manejar clicks en notificaciones
self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'explore') {
        // Abrir la aplicación
        event.waitUntil(
            clients.openWindow('/')
        );
    } else if (event.action === 'close') {
        // Solo cerrar la notificación
        event.notification.close();
    } else {
        // Click en el cuerpo de la notificación
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Sincronización en segundo plano
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

function doBackgroundSync() {
    return fetch('/api/sync')
        .then(response => response.json())
        .then(data => {
            console.log('Sincronización completada:', data);
        })
        .catch(error => {
            console.error('Error en sincronización:', error);
        });
}

// 🔔 MANEJADOR DE PUSH NOTIFICATIONS
self.addEventListener('push', event => {
    console.log('Push notification received:', event);
    
    if (event.data) {
        const data = event.data.json();
        console.log('Push data:', data);
        
        const options = {
            body: data.body || 'Nueva notificación',
            icon: data.icon || '/images/lateral01.png',
            badge: data.badge || '/images/lateral01.png',
            tag: data.tag || 'default',
            data: data.data || {},
            actions: data.actions || [],
            requireInteraction: data.requireInteraction || false,
            vibrate: data.vibrate || [200, 100, 200]
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title || 'ClicIdea', options)
        );
    }
});

// 🔔 MANEJADOR DE CLICK EN NOTIFICACIONES
self.addEventListener('notificationclick', event => {
    console.log('Notification clicked:', event);
    
    event.notification.close();
    
    if (event.action === 'view' || !event.action) {
        // Abrir/enfocar la URL de la notificación
        const urlToOpen = event.notification.data.url || '/';
        
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true })
                .then(clientList => {
                    // Buscar una ventana ya abierta con la URL
                    for (const client of clientList) {
                        if (client.url === urlToOpen && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    // Si no hay ventana abierta, abrir una nueva
                    if (clients.openWindow) {
                        return clients.openWindow(urlToOpen);
                    }
                })
        );
    }
});
