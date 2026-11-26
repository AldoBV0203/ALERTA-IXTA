self.addEventListener('install', (event) => {
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', (event) => {
    if (!event.data) return;

    let datos;

    try {
        datos = event.data.json();
    } catch {
        return;
    }

    const titulo = datos.titulo || 'ALERTA IXTA';
    const cuerpo = datos.cuerpo || 'Tienes una nueva alerta SOS.';
    const destino = datos.destino;

    if (typeof destino !== 'string' ||
        !/^\/inicio\?notificacion=[1-9]\d*$/.test(destino)) {
        return;
    }

    event.waitUntil(
        self.registration.showNotification(titulo, {
            body: cuerpo,
            data: { destino },
            tag: `sos-${datos.notificacionId || destino}`,
            renotify: true,
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    const destino = event.notification.data?.destino;
    event.notification.close();

    if (typeof destino !== 'string' ||
        !/^\/inicio\?notificacion=[1-9]\d*$/.test(destino)) {
        return;
    }

    event.waitUntil((async () => {
        const url = new URL(destino, self.location.origin);

        const ventanas = await self.clients.matchAll({
            type: 'window',
            includeUncontrolled: true,
        });

        const inicios = ventanas.filter((cliente) => {
            const pagina = new URL(cliente.url);

            return pagina.origin === self.location.origin &&
                pagina.pathname === '/inicio';
        });

        const ventana = inicios.find(cliente => cliente.focused) ||
            inicios.find(cliente => cliente.visibilityState === 'visible') ||
            inicios[0];

        if (ventana) {
            try {
                const actualizada = await ventana.navigate(url.href);

                if (actualizada) {
                    await actualizada.focus();
                    return;
                }
            } catch (error) {
                console.error('No se pudo abrir la alerta en la pestaña.', error);
            }
        }

        await self.clients.openWindow(url.href);
    })());
});