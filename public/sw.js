"use strict";

const CACHE_NAME = "offline-cache-v1"; // versión lógica de recursos offline
// Si alguna página antigua carga sw.js?v=sw-v5 o anterior, este script sigue operando pero usaremos misma lógica.
const OFFLINE_URL = '/offline.html';

const filesToCache = [
    OFFLINE_URL
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(filesToCache))
    );
});

self.addEventListener("fetch", (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.match(OFFLINE_URL);
                })
        );
    } else {
        event.respondWith(
            caches.match(event.request)
                .then((response) => {
                    return response || fetch(event.request);
                })
        );
    }
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Web Push: mostrar notificaciones (versión diag 3)
self.addEventListener('push', (event) => {
    let data = {};
    try {
        if (event.data) data = event.data.json();
    } catch(e){
        // fallback: texto plano
        data = { title: 'Notificación', body: event.data?.text() };
    }
    const title = data.title || 'Notificación';
    const options = {
        body: data.body || '',
        icon: data.icon || '/logo.png',
        badge: data.badge || '/logo.png',
        data: data.data || { url: '/' },
        tag: data.tag || undefined,
        renotify: !!data.renotify,
        vibrate: data.vibrate || [100,50,100],
        actions: data.actions || []
    };
    console.log('[SW][push] recibido payload:', data);
    event.waitUntil(self.registration.showNotification(title, options));
});

// Clic en notificación: enfocar/abrir
self.addEventListener('notificationclick', (event) => {
    const action = event.action;
    const data = event.notification.data || {};
    const url = data.url || '/';
    console.log('[SW][notificationclick] action=', action, 'url=', url, data);

    if(action === 'close' || action === 'dismiss'){
        event.notification.close();
        return;
    }

    event.notification.close();
    event.waitUntil((async () => {
        try {
            const all = await clients.matchAll({ type: 'window', includeUncontrolled: true });
            const targetOrigin = self.location.origin;
            let exactMatch = null;
            let anySameOrigin = null;
            const targetUrl = new URL(url, targetOrigin);
            console.log('[SW][click] ventanas encontradas:', all.map(c=>c.url));
            for(const c of all){
                try {
                    const cUrl = new URL(c.url);
                    if(cUrl.origin === targetOrigin){
                        if(!anySameOrigin) anySameOrigin = c;
                        // Match by pathname ignoring query first
                        if(cUrl.pathname === targetUrl.pathname){
                            exactMatch = c;
                            break;
                        }
                    }
                } catch(e){ /* ignore */ }
            }
            if(exactMatch){
                console.log('[SW][click] exactMatch encontrado, url=', exactMatch.url);
                // Navigate if different (e.g. need query focus) then focus
                if(exactMatch.url !== targetUrl.toString()){
                    try { await exactMatch.navigate(targetUrl.toString()); console.log('[SW][click] navigate exactMatch OK'); } catch(e){ console.warn('[SW][click] navigate exactMatch fallo', e); }
                }
                try { return await exactMatch.focus(); } catch(e){ console.warn('[SW][click] focus exactMatch fallo', e); }
            }
            if(anySameOrigin){
                console.log('[SW][click] usando anySameOrigin', anySameOrigin.url);
                try { await anySameOrigin.navigate(targetUrl.toString()); console.log('[SW][click] navigate anySameOrigin OK'); } catch(e){ console.warn('[SW][click] navigate anySameOrigin fallo', e); }
                try { return await anySameOrigin.focus(); } catch(e){ console.warn('[SW][click] focus anySameOrigin fallo', e); }
            }
            if(clients.openWindow){
                console.log('[SW][click] intentando openWindow', targetUrl.toString());
                try { return await clients.openWindow(targetUrl.toString()); }
                catch(e){
                    console.error('[SW][notificationclick] openWindow failed', e);
                    // Last resort: open root
                    try { return await clients.openWindow('/'); } catch(_){}
                }
            }
            // Como fallback adicional (casos raros) intentamos un segundo intento diferido
            setTimeout(()=>{
                if(clients.openWindow){
                    console.log('[SW][click] segundo intento openWindow diferido');
                    clients.openWindow(targetUrl.toString()).catch(err=>console.error('[SW][click] segundo intento fallo', err));
                }
            }, 120);
        } catch(err){
            console.error('[SW][notificationclick] unexpected error', err);
        }
    })());
});
