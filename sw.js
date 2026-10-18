const CACHE_NAME = 'alrish-cache-v7';
const STATIC_ASSETS = [
    'manifest.json',
    'manifest.php',
    'logo.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('Pre-cache asset notice:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        }).then(() => clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    const url = new URL(req.url);

    // Skip non-GET requests (POSTs, mutations) - always go straight to network
    if (req.method !== 'GET') {
        return;
    }

    // Skip dynamic polling / stream endpoints
    if (url.searchParams.has('live_update') || url.searchParams.has('events')) {
        return;
    }

    // Navigation requests (HTML App Shell) -> Network First with dynamic cache & safe fallback
    if (req.mode === 'navigate') {
        event.respondWith(
            fetch(req)
                .then((networkRes) => {
                    if (networkRes && networkRes.ok) {
                        const resClone = networkRes.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put('app_shell_cached', resClone)).catch(() => {});
                    }
                    return networkRes;
                })
                .catch(async () => {
                    const cached = await caches.match('app_shell_cached');
                    if (cached) return cached;
                    const rootCached = await caches.match('./');
                    if (rootCached) return rootCached;
                    return new Response('<!DOCTYPE html><html lang="en" class="dark"><head><meta charset="UTF-8"><title>Al Rish Offline</title></head><body style="background:#0b0f19;color:#e2e8f0;font-family:sans-serif;text-align:center;padding:50px 20px;"><div style="max-width:400px;margin:0 auto;background:#1e293b;padding:30px;border-radius:24px;border:1px solid #334155;"><h2 style="color:#f97316;margin-bottom:8px;">Al Rish Inventory</h2><p style="font-size:14px;color:#94a3b8;">Offline Mode. Reconnecting...</p></div></body></html>', {
                        headers: { 'Content-Type': 'text/html' }
                    });
                })
        );
        return;
    }

    // Static assets (Fonts, Icons, Images, CSS, JS) -> Cache First / Stale-While-Revalidate
    event.respondWith(
        caches.match(req).then((cachedRes) => {
            if (cachedRes) {
                // Background refresh
                fetch(req).then((networkRes) => {
                    if (networkRes && networkRes.ok) {
                        caches.open(CACHE_NAME).then((cache) => cache.put(req, networkRes)).catch(() => {});
                    }
                }).catch(() => {});
                return cachedRes;
            }

            return fetch(req)
                .then((networkRes) => {
                    if (networkRes && networkRes.ok && (url.origin === location.origin || url.hostname.includes('fonts.') || url.hostname.includes('tailwindcss.com'))) {
                        const resClone = networkRes.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(req, resClone)).catch(() => {});
                    }
                    return networkRes;
                })
                .catch(() => {
                    return new Response('', { status: 408, statusText: 'Resource unavailable offline' });
                });
        }).catch(() => {
            return fetch(req).catch(() => new Response('', { status: 408, statusText: 'Resource unavailable offline' }));
        })
    );
});