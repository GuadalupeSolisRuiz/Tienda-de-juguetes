// Service Worker — Tienda de Juguetes
const CACHE = 'toy-store-v1';

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', e => e.waitUntil(clients.claim()));

// Handle push events from server (future use)
self.addEventListener('push', e => {
  const data = e.data?.json() ?? {};
  e.waitUntil(
    self.registration.showNotification(data.title || '🛒 Tienda de Juguetes', {
      body: data.body || '¡Tienes novedades esperándote!',
      icon: 'assets/img/notif-icon.png',
      tag: 'toy-push',
      renotify: true,
      data: { url: data.url || '/' }
    })
  );
});

// Notification click → focus or open tab
self.addEventListener('notificationclick', e => {
  e.notification.close();
  const target = e.notification.data?.url || '/';
  e.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
      const existing = list.find(c => c.url.includes(target));
      return existing ? existing.focus() : clients.openWindow(target);
    })
  );
});