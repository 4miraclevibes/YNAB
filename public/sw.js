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
const messages = [
  'Jangan lupa catat pengeluaranmu hari ini!',
  'Sudah update budget bulan ini?',
  'Yuk cek saldo keuanganmu!',
  'Ada transaksi baru di YNAB!'
];

const showNotification = () => {
  if (self.registration) {
    self.registration.showNotification('YNAB Notification', {
      body: 'Ada transaksi baru di YNAB!',
      icon: '/logo.png',
      tag: 'ynab-notification',
      actions: [
        {
          action: 'open',
          title: 'Buka YNAB'
        },
        {
          action: 'close',
          title: 'Tutup'
        }
      ]
    });
  }
};

// Tambahkan event listener untuk handle click notification
self.addEventListener('notificationclick', function(event) {
  event.notification.close(); // Tutup notifikasi

  if (event.action === 'open') {
    // Buka aplikasi YNAB
    clients.openWindow('/');
  } else if (event.action === 'close') {
    // Hanya tutup notifikasi
    return;
  } else {
    // Jika user klik di area notifikasi (bukan di action button)
    clients.openWindow('/');
  }
});

const intervalID = setInterval(showNotification, 15000);

// Optional: cleanup interval
self.addEventListener('terminate', () => {
  if (intervalID) {
    clearInterval(intervalID);
  }
});
