const preLoad = function () {
    return caches.open("offline").then(function (cache) {
        // caching index and important routes
        return cache.addAll(filesToCache);
    });
};

self.addEventListener("install", (event) => {
    self.skipWaiting();
});

self.addEventListener("activate", (event) => {
    event.waitUntil(clients.claim());
});

const filesToCache = [
    '/',
    '/offline.html'
];

const checkResponse = function (request) {
    return new Promise(function (fulfill, reject) {
        fetch(request).then(function (response) {
            if (response.status !== 404) {
                fulfill(response);
            } else {
                reject();
            }
        }, reject);
    });
};

const addToCache = function (request) {
    return caches.open("offline").then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request, response);
        });
    });
};

const returnFromCache = function (request) {
    return caches.open("offline").then(function (cache) {
        return cache.match(request).then(function (matching) {
            if (!matching || matching.status === 404) {
                return cache.match("offline.html");
            } else {
                return matching;
            }
        });
    });
};

self.addEventListener("fetch", (event) => {
    event.respondWith(
        fetch(event.request)
    );
});

// Notifikasi setiap 2 detik
const showNotification = () => {
  if (self.registration) {  // Pastikan registration tersedia
    self.registration.showNotification('YNAB Notification', {
      body: 'Jangan lupa catet keuanganmu di YNAB!',
      icon: '/logo.png',
      tag: 'ynab-notification'
    }).catch(err => console.error('Error showing notification:', err));
  }
};

const intervalID = setInterval(showNotification, 2000);

// Optional: cleanup interval
self.addEventListener('terminate', () => {
  if (intervalID) {
    clearInterval(intervalID);
  }
});
