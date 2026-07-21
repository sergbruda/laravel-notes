const CACHE_NAME = 'notebook-pwa-v1';

// Устанавливаем воркер и кэшируем главную страницу
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll([
        '/',
        '/manifest.json',
        '/icons/icon-192x192.png',
        '/icons/icon-512x512.png'
      ]);
    })
  );
});

// Перехватываем запросы: сначала сеть, если нет сети - кэш
self.addEventListener('fetch', event => {
  return event.respondWith(
    fetch(event.request)
      .then(response => {
        // Клонируем ответ и сохраняем в кэш
        let responseClone = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseClone));
        return response;
      })
      .catch(() => {
        // Если сети нет, берем из кэша
        return caches.match(event.request);
      })
  );
});
