const CACHE_NAME = "streetpos-v9"; // Network First with better offline handling
const urlsToCache = [
    "/",
    "/login",
    "/register",
    "/products",
    "/sales",
    "/quick-sale",
    "/stock-alerts",
    "/expenses",
    "/reports/daily-sales",
    "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css",
    "https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js",
];

// Install Service Worker
self.addEventListener("install", (event) => {
    console.log("[ServiceWorker] Installing...");
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log("[ServiceWorker] Caching app shell");
            // Cache pages one by one to avoid failure if one fails
            return Promise.all(
                urlsToCache.map((url) => {
                    return cache.add(url).catch((err) => {
                        console.log("[ServiceWorker] Failed to cache:", url, err);
                    });
                })
            );
        }).catch((err) => {
            console.error("[ServiceWorker] Installation failed:", err);
        })
    );
    self.skipWaiting();
});

// Activate Service Worker
self.addEventListener("activate", (event) => {
    console.log("[ServiceWorker] Activating...");
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log(
                            "[ServiceWorker] Removing old cache:",
                            cacheName
                        );
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Fetch event - Network first for API, Cache first for static assets
self.addEventListener("fetch", (event) => {
    // Skip chrome extensions and browser extensions
    if (event.request.url.startsWith("chrome-extension://")) return;
    if (event.request.url.startsWith("moz-extension://")) return;
    if (event.request.url.startsWith("safari-extension://")) return;

    // Skip API sync calls (handled separately)
    if (event.request.url.includes("/api/")) return;

    // Skip non-GET requests - let them pass through to the network
    // The page-level JavaScript will handle offline scenarios
    if (event.request.method !== "GET") {
        return;
    }

    const url = new URL(event.request.url);

    // Only cache specific routes - let everything else pass through
    const cacheableRoutes = [
        "/",
        "/login",
        "/register",
        "/products",
        "/sales",
        "/quick-sale",
        "/stock-alerts",
        "/expenses",
        "/reports/daily-sales",
    ];

    // Check if this is a cacheable route or CDN resource
    const isCacheableRoute = cacheableRoutes.includes(url.pathname);
    const isCDN = url.hostname.includes("cdnjs.cloudflare.com") || url.hostname.includes("cdn.jsdelivr.net");

    // If not cacheable and not CDN, let the browser handle it normally
    if (!isCacheableRoute && !isCDN) {
        return;
    }

    event.respondWith(
        // Network first, but with smart offline handling
        fetch(event.request)
            .then((response) => {
                // Don't cache if not successful
                if (!response || response.status !== 200) {
                    // If network failed, try cache
                    return caches.match(event.request).then((cachedResponse) => {
                        return cachedResponse || response;
                    });
                }

                // Clone the response
                const responseClone = response.clone();

                // Update cache in background
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseClone);
                });

                return response;
            })
            .catch((error) => {
                // Network failed (offline), try cache first
                return caches.match(event.request).then((cachedResponse) => {
                    // If we have a cached version, return it
                    if (cachedResponse) {
                        console.log('[SW] Serving from cache (offline):', event.request.url);
                        return cachedResponse;
                    }

                    // No cache available - this is where the form submission issue happens
                    // For document requests (page navigation), show friendly offline message
                    // But DON'T interfere with form submissions - let the page JS handle it
                    if (event.request.destination === "document") {
                        console.log('[SW] No cache available for:', event.request.url);
                        // Only show offline page if it's a navigation, not a form result
                        // Check if this might be coming from a form redirect
                        const url = new URL(event.request.url);
                        if (url.searchParams.has('t')) {
                            // This is a post-form-submission redirect with timestamp
                            // Try to serve the cached version without the timestamp
                            const urlWithoutTimestamp = url.origin + url.pathname;
                            return caches.match(urlWithoutTimestamp).then((cached) => {
                                if (cached) {
                                    return cached;
                                }
                                // Still no cache, just return the offline page
                                return new Response(
                                    `<!DOCTYPE html>
                                        <html>
                                        <head>
                                            <title>Offline - StreetPOS</title>
                                            <style>
                                                body {
                                                    font-family: Arial, sans-serif;
                                                    display: flex;
                                                    justify-content: center;
                                                    align-items: center;
                                                    height: 100vh;
                                                    margin: 0;
                                                    background: linear-gradient(135deg, #800000, #FFD700);
                                                    color: white;
                                                    text-align: center;
                                                }
                                                .container {
                                                    background: rgba(255,255,255,0.1);
                                                    padding: 40px;
                                                    border-radius: 20px;
                                                    backdrop-filter: blur(10px);
                                                }
                                                h1 { font-size: 48px; margin: 0 0 20px 0; }
                                                p { font-size: 18px; margin: 10px 0; }
                                                button {
                                                    margin-top: 20px;
                                                    padding: 15px 30px;
                                                    background: #FFD700;
                                                    color: #800000;
                                                    border: none;
                                                    border-radius: 10px;
                                                    font-size: 16px;
                                                    font-weight: bold;
                                                    cursor: pointer;
                                                }
                                                button:hover {
                                                    background: #e6c200;
                                                }
                                            </style>
                                        </head>
                                        <body>
                                            <div class="container">
                                                <h1>📴 You're Offline</h1>
                                                <p>This page hasn't been cached yet.</p>
                                                <p>Connect to the internet and visit this page first.</p>
                                                <button onclick="window.location.href='/'">Go to Dashboard</button>
                                            </div>
                                        </body>
                                        </html>`,
                                    {
                                        headers: { "Content-Type": "text/html" },
                                    }
                                );
                            });
                        }
                    }

                    // For other requests, just fail gracefully
                    return new Response('Offline', { status: 503, statusText: 'Service Unavailable' });
                });
            })
    );
});

// Listen for messages from the main app
self.addEventListener("message", (event) => {
    if (event.data && event.data.type === "SKIP_WAITING") {
        self.skipWaiting();
    }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    // Navigate to the app when notification is clicked
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // If app is already open, focus it
                for (const client of clientList) {
                    if ('focus' in client) {
                        return client.focus();
                    }
                }
                // Otherwise open a new window
                if (clients.openWindow) {
                    return clients.openWindow('/');
                }
            })
    );
});
