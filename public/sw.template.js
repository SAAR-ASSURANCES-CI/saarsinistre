// __SW_VERSION__ sera remplacé automatiquement par le script de build
const CACHE_VERSION = '__SW_VERSION__';
const CACHE_NAME = `saarsinistre-v${CACHE_VERSION}`;
const STATIC_CACHE = `saarsinistre-static-v${CACHE_VERSION}`;
const DYNAMIC_CACHE = `saarsinistre-dynamic-v${CACHE_VERSION}`;

const STATIC_ASSETS = [
    '/manifest.json',
    '/logo.png',
    '/favicon.ico',
    '/offline.html',
];

const ROUTES_TO_CACHE = [];

async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        return caches.match('/offline.html') || new Response('Erreur de connexion', { status: 503 });
    }
}

async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        throw error;
    }
}

// Stratégie stale-while-revalidate pour un meilleur équilibre
async function staleWhileRevalidate(request) {
    const cachedResponse = await caches.match(request);
    
    const fetchPromise = fetch(request).then(networkResponse => {
        if (networkResponse.ok) {
            const cache = caches.open(DYNAMIC_CACHE);
            cache.then(c => c.put(request, networkResponse.clone()));
        }
        return networkResponse;
    }).catch(() => cachedResponse);
    
    return cachedResponse || fetchPromise;
}

self.addEventListener('install', event => {
    console.log('Service Worker: Installation en cours...');
    event.waitUntil(
        Promise.all([
            caches.open(STATIC_CACHE).then(cache => {
                console.log('Service Worker: Mise en cache des ressources statiques');
                return Promise.all(
                    STATIC_ASSETS.map(asset => 
                        cache.add(asset).catch(err => 
                            console.log(`Impossible de mettre en cache ${asset}:`, err.message)
                        )
                    )
                );
            }),
            caches.open(DYNAMIC_CACHE).then(cache => {
                console.log('Service Worker: Mise en cache des routes principales');
                return Promise.all(
                    ROUTES_TO_CACHE.map(route => 
                        cache.add(route).catch(err => 
                            console.log(`Impossible de mettre en cache ${route}:`, err)
                        )
                    )
                );
            })
        ]).then(() => {
            console.log('Service Worker: Installation terminée');
            self.skipWaiting();
        })
    );
});

self.addEventListener('activate', event => {
    console.log('Service Worker: Activation en cours...');
    event.waitUntil(
        Promise.all([
            caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                            console.log('Service Worker: Suppression de l\'ancien cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            }),
            self.clients.claim()
        ]).then(() => {
            console.log('Service Worker: Activation terminée');
        })
    );
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    if (request.method !== 'GET') {
        return;
    }
    
    if (url.hostname === 'localhost' || url.hostname === '127.0.0.1') {
        return;
    }
    
    // Assets statiques spécifiques 
    if (STATIC_ASSETS.includes(url.pathname)) {
        event.respondWith(cacheFirst(request));
        return;
    }
    
    // Assets buildés par Vite 
    if (url.pathname.startsWith('/build/')) {
        event.respondWith(cacheFirst(request));
        return;
    }
    
    if (url.pathname === '/sw.js') {
        event.respondWith(fetch(request));
        return;
    }
    
    // Icônes - cache OK
    if (url.pathname.startsWith('/icons/')) {
        event.respondWith(cacheFirst(request));
        return;
    }
    
    // JS/CSS non buildés 
    if (url.pathname.startsWith('/js/') || url.pathname.startsWith('/css/')) {
        event.respondWith(staleWhileRevalidate(request));
        return;
    }
    
    // API et données dynamiques 
    if (url.pathname.startsWith('/api/') || 
        url.pathname.includes('sinistres') ||
        url.pathname.includes('users') ||
        url.pathname.includes('chat') ||
        url.pathname.includes('login') ||
        url.pathname.includes('logout') ||
        url.pathname.includes('dashboard')) {
        event.respondWith(networkFirst(request));
        return;
    }
    
    
    event.respondWith(staleWhileRevalidate(request));
});

self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});

self.addEventListener('push', event => {
    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body || 'Nouvelle notification SAARCISinistres',
            icon: '/icons/icon-192x192.svg',
            badge: '/icons/icon-72x72.svg',
            tag: 'saarsinistre-notification',
            data: data.data || {},
            actions: data.actions || []
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title || 'SAARCISinistres', options)
        );
    }
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    
    if (event.action) {
        console.log('Action de notification:', event.action);
    } else {
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

self.addEventListener('error', event => {
    console.error('Service Worker: Erreur:', event.error);
});

self.addEventListener('unhandledrejection', event => {
    console.error('Service Worker: Promesse rejetée non gérée:', event.reason);
});

