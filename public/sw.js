const CACHE_NAME = 'unjani-surat-v1.0.0';
const OFFLINE_URL = '/offline.html';

// Files to cache for offline functionality
const STATIC_CACHE_FILES = [
  '/',
  '/dashboard',
  '/search',
  '/offline.html',
  '/assets/css/dashboard.css',
  '/assets/js/app.js',
  '/assets/icons/icon-192x192.png',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
  'https://cdn.jsdelivr.net/npm/chart.js'
];

// Dynamic cache patterns
const DYNAMIC_CACHE_PATTERNS = [
  /^https:\/\/cdn\.jsdelivr\.net\//,
  /^https:\/\/fonts\.googleapis\.com\//,
  /^https:\/\/fonts\.gstatic\.com\//
];

// Install event - cache static files
self.addEventListener('install', event => {
  console.log('Service Worker installing...');
  
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('Caching static files');
      return cache.addAll(STATIC_CACHE_FILES.map(url => {
        return new Request(url, { cache: 'reload' });
      }));
    }).then(() => {
      console.log('Static files cached successfully');
      self.skipWaiting();
    })
  );
});

// Activate event - cleanup old caches
self.addEventListener('activate', event => {
  console.log('Service Worker activating...');
  
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => {
      console.log('Service Worker activated');
      return self.clients.claim();
    })
  );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Handle navigation requests
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then(response => {
          // Cache successful navigation responses
          if (response.status === 200) {
            const responseClone = response.clone();
            caches.open(CACHE_NAME).then(cache => {
              cache.put(request, responseClone);
            });
          }
          return response;
        })
        .catch(() => {
          // Serve cached page or offline fallback
          return caches.match(request).then(cachedResponse => {
            if (cachedResponse) {
              return cachedResponse;
            }
            // Show offline page for navigation requests
            return caches.match(OFFLINE_URL);
          });
        })
    );
    return;
  }

  // Handle API requests
  if (url.pathname.startsWith('/api/') || url.pathname.includes('ajax')) {
    event.respondWith(
      fetch(request)
        .then(response => {
          // Cache successful API responses for short term
          if (response.status === 200 && request.method === 'GET') {
            const responseClone = response.clone();
            caches.open(CACHE_NAME + '-api').then(cache => {
              cache.put(request, responseClone);
              // Set expiration for API cache (1 hour)
              setTimeout(() => {
                cache.delete(request);
              }, 3600000);
            });
          }
          return response;
        })
        .catch(() => {
          // Serve cached API response if available
          return caches.match(request).then(cachedResponse => {
            if (cachedResponse) {
              // Add offline indicator to cached API responses
              return new Response(
                JSON.stringify({
                  ...JSON.parse(cachedResponse.body),
                  _offline: true,
                  _cached_at: new Date().toISOString()
                }),
                {
                  status: cachedResponse.status,
                  statusText: cachedResponse.statusText,
                  headers: cachedResponse.headers
                }
              );
            }
            // Return offline response for API requests
            return new Response(
              JSON.stringify({
                error: 'Offline',
                message: 'This feature requires an internet connection'
              }),
              {
                status: 503,
                headers: { 'Content-Type': 'application/json' }
              }
            );
          });
        })
    );
    return;
  }

  // Handle static resources
  if (shouldCache(request)) {
    event.respondWith(
      caches.match(request).then(cachedResponse => {
        if (cachedResponse) {
          // Serve from cache and update in background
          fetch(request).then(response => {
            if (response.status === 200) {
              caches.open(CACHE_NAME).then(cache => {
                cache.put(request, response.clone());
              });
            }
          }).catch(() => {
            // Ignore network errors for background updates
          });
          return cachedResponse;
        }

        // Fetch from network and cache
        return fetch(request).then(response => {
          if (response.status === 200) {
            const responseClone = response.clone();
            caches.open(CACHE_NAME).then(cache => {
              cache.put(request, responseClone);
            });
          }
          return response;
        }).catch(() => {
          // Return offline fallback for images
          if (request.destination === 'image') {
            return caches.match('/assets/icons/icon-192x192.png');
          }
          throw error;
        });
      })
    );
    return;
  }

  // Default: just fetch from network
  event.respondWith(fetch(request));
});

// Background sync for form submissions
self.addEventListener('sync', event => {
  console.log('Background sync:', event.tag);
  
  if (event.tag === 'background-sync-form') {
    event.waitUntil(
      // Handle offline form submissions
      syncOfflineActions()
    );
  }
});

// Push notification handler
self.addEventListener('push', event => {
  console.log('Push notification received');
  
  const options = {
    body: event.data ? event.data.text() : 'New notification from UNJANI Surat',
    icon: '/assets/icons/icon-192x192.png',
    badge: '/assets/icons/icon-72x72.png',
    vibrate: [200, 100, 200],
    data: {
      url: '/dashboard',
      timestamp: Date.now()
    },
    actions: [
      {
        action: 'open',
        title: 'Open App',
        icon: '/assets/icons/icon-72x72.png'
      },
      {
        action: 'dismiss',
        title: 'Dismiss',
        icon: '/assets/icons/icon-72x72.png'
      }
    ],
    requireInteraction: true,
    silent: false
  };

  event.waitUntil(
    self.registration.showNotification('UNJANI Surat', options)
  );
});

// Notification click handler
self.addEventListener('notificationclick', event => {
  console.log('Notification clicked:', event.action);
  
  event.notification.close();

  if (event.action === 'open' || !event.action) {
    event.waitUntil(
      clients.openWindow(event.notification.data?.url || '/dashboard')
    );
  }
});

// Helper functions
function shouldCache(request) {
  const url = new URL(request.url);
  
  // Cache CSS, JS, images, and fonts
  if (request.destination === 'style' || 
      request.destination === 'script' || 
      request.destination === 'image' || 
      request.destination === 'font') {
    return true;
  }

  // Cache CDN resources
  return DYNAMIC_CACHE_PATTERNS.some(pattern => pattern.test(request.url));
}

async function syncOfflineActions() {
  // Retrieve offline actions from IndexedDB and sync them
  // This would be implemented based on specific offline action storage
  console.log('Syncing offline actions...');
  
  try {
    // Example: sync offline form submissions
    const offlineActions = await getOfflineActions();
    
    for (const action of offlineActions) {
      try {
        await fetch(action.url, {
          method: action.method,
          headers: action.headers,
          body: action.body
        });
        
        // Remove successfully synced action
        await removeOfflineAction(action.id);
      } catch (error) {
        console.error('Failed to sync action:', error);
      }
    }
  } catch (error) {
    console.error('Background sync failed:', error);
  }
}

// Placeholder functions for offline action management
async function getOfflineActions() {
  // Implement IndexedDB retrieval
  return [];
}

async function removeOfflineAction(id) {
  // Implement IndexedDB removal
  console.log('Removing offline action:', id);
}