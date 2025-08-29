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
    console.log('SW: Instalando...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('SW: Cache abierto');
                return cache.addAll(urlsToCache)
                    .catch(error => {
                        console.error('SW: Error cacheando URLs:', error);
                        // Cachear solo las URLs que existen
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
    console.log('SW: Activando...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('SW: Eliminando cache viejo:', cacheName);
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
                    console.log('SW: Sirviendo desde cache:', event.request.url);
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
                        console.log('SW: Error de red, sirviendo offline:', error);
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
