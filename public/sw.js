// Service Worker for Offline Support and Caching
const CACHE_NAME = 'shopbd-v1';
const urlsToCache = [
    '/',
    '/products',
    '/categories',
    '/brands',
    '/about',
    '/contact',
    '/css/app.css',
    '/js/app.js',
    '/images/placeholder-product.jpg'
];

// Install event - cache resources
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

// Fetch event - serve from cache when offline
self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                // Cache hit - return response
                if (response) {
                    return response;
                }

                // Clone the request
                const fetchRequest = event.request.clone();

                return fetch(fetchRequest).then(
                    function(response) {
                        // Check if valid response
                        if(!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // Clone the response
                        const responseToCache = response.clone();

                        caches.open(CACHE_NAME)
                            .then(function(cache) {
                                cache.put(event.request, responseToCache);
                            });

                        return response;
                    }
                ).catch(function() {
                    // Offline fallback
                    if (event.request.destination === 'image') {
                        return caches.match('/images/placeholder-product.jpg');
                    }
                });
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Background sync for offline actions
self.addEventListener('sync', function(event) {
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

function doBackgroundSync() {
    // Handle offline actions like form submissions, cart updates, etc.
    return self.registration.showNotification('Sync Complete', {
        body: 'Your offline actions have been synced.',
        icon: '/images/icon-192x192.png'
    });
}

// Push notification handler
self.addEventListener('push', function(event) {
    const options = {
        body: event.data ? event.data.text() : 'New notification',
        icon: '/images/icon-192x192.png',
        badge: '/images/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        }
    };

    event.waitUntil(
        self.registration.showNotification('ShopBD', options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', function(event) {
    console.log('Notification click received.');

    event.notification.close();

    event.waitUntil(
        clients.openWindow('/')
    );
});
