self.addEventListener('install', (event) => {
    console.log('Service Worker: Installed');
});

self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activated');
});

// Browsers require a fetch event handler to trigger the Install prompt
self.addEventListener('fetch', (event) => {
    // Just pass the request through to the network normally
    event.respondWith(fetch(event.request));
});
