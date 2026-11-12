<style>
    #panelVerificacionIdentidad .btn-salir {
        background: var(--vino);
        color: white;
        border: 0;
        border-radius: 8px;
        padding: 14px 18px;
        font-size: 16px;
    }

    #panelVerificacionIdentidad .btn-salir:hover:not(:disabled) {
        background: var(--vino-claro);
    }

    #panelVerificacionIdentidad .btn-salir:disabled {
        opacity: 0.65;
        cursor: wait;
    }
</style>

<section id="panelVerificacionIdentidad" hidden>
    <div class="titulo-pagina">
        <h2>Verificación de identidad</h2>
    </div>

    <p id="mensajeDecisionIdentidad" role="status"></p>

    <div id="listaVerificacionesIdentidad" class="detalle-contenido"></div>
</section>

<script>
    let consultaVerificacionesIdentidad = null;

    function mostrarSeccionMonitoreo(seccion) {
        const verIdentidad = seccion === 'identidad';

        document.getElementById('panelGeneralMonitoreo').hidden = verIdentidad;
        document.getElementById('panelVerificacionIdentidad').hidden = !verIdentidad;

        document.getElementById('menuPanelGeneral')
            .classList.toggle('activa', !verIdentidad);

        document.getElementById('menuVerificacionIdentidad')
            .classList.toggle('activa', verIdentidad);


        if (verIdentidad) {
            cargarVerificacionesIdentidad();
        }
        if (!verIdentidad && mapaMonitoreo) {
            requestAnimationFrame(() => mapaMonitoreo.invalidateSize({
                pan: false
            }));
        }
    }

    function actualizarVerificacionesVisibles() {
        const panel = document.getElementById('panelVerificacionIdentidad');

        if (!document.hidden && !panel.hidden && !consultaVerificacionesIdentidad) {
            cargarVerificacionesIdentidad();
        }
    }

    setInterval(actualizarVerificacionesVisibles, 5000);
    document.addEventListener('visibilitychange', actualizarVerificacionesVisibles);

    async function cargarVerificacionesIdentidad() {
        // Al volver a la sección o guardar una decisión, consulta la lista más reciente.
        consultaVerificacionesIdentidad?.abort();
        const consulta = new AbortController();
        consultaVerificacionesIdentidad = consulta;

        const lista = document.getElementById('listaVerificacionesIdentidad');
        const mensaje = document.getElementById('mensajeDecisionIdentidad');

        if (!lista.hasChildNodes()) {
            lista.textContent = 'Cargando verificaciones...';
        }

        try {
            const respuesta = await fetch(
                API + '/monitoreo/verificaciones-identidad', {
                    signal: consulta.signal,
                    headers: {
                        'Authorization': 'Bearer ' + tokenMonitoreo,
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                }
            );

            if (respuesta.status === 401) {
                cerrarSesionMonitoreo();
                return;
            }

            if (!respuesta.ok) {
                if (respuesta.status === 403) {
                    lista.textContent = 'Tu cuenta no tiene permiso para revisar identidades.';
                } else if (lista.childElementCount === 0) {
                    lista.textContent = 'No se pudieron cargar las verificaciones.';
                }
                return;
            }

            const { ciudadanos } = await respuesta.json();

            if (consulta.signal.aborted) {
                return;
            }

            if (ciudadanos.length === 0) {
                lista.textContent = 'No hay verificaciones pendientes.';
                return;
            }

            const idsPendientes = new Set(ciudadanos.map(ciudadano => String(ciudadano.id)));
            const idsVisibles = new Set();

            // Conserva las tarjetas que siguen pendientes, incluida su foto abierta.
            for (const tarjeta of Array.from(lista.children)) {
                const id = tarjeta.dataset.idCiudadano;
                if (idsPendientes.has(id)) {
                    idsVisibles.add(id);
                } else {
                    tarjeta.remove();
                }
            }

            if (idsVisibles.size === 0) {
                lista.replaceChildren();
            }

            for (const [indice, ciudadano] of ciudadanos.entries()) {
                const id = String(ciudadano.id);
                if (idsVisibles.has(id)) {
                    continue;
                }

                if (mensaje.dataset.idCiudadano === id) {
                    mensaje.textContent = '';
                    delete mensaje.dataset.idCiudadano;
                }

                const tarjeta = document.createElement('article');
                tarjeta.className = 'panel detalle-contenido';
                tarjeta.dataset.idCiudadano = id;

                const nombre = document.createElement('h3');
                nombre.textContent = ciudadano.nombre;

                const curp = document.createElement('p');
                curp.textContent = 'CURP: ' + ciudadano.curp;

                const botonFoto = document.createElement('button');
                botonFoto.type = 'button';
                botonFoto.className = 'btn-salir';
                botonFoto.textContent = 'Ver foto';

                const espacioFoto = document.createElement('div');
                espacioFoto.setAttribute('role', 'status');

                botonFoto.addEventListener('click', function() {
                    mostrarFotoIdentidad(ciudadano.id, espacioFoto, botonFoto);
                });

                const botonAprobar = document.createElement('button');
                botonAprobar.type = 'button';
                botonAprobar.className = 'btn-salir boton-ine-aprobar';
                botonAprobar.textContent = 'Aprobar identidad';
                botonAprobar.style.marginLeft = '6px';
                botonAprobar.onclick = () => {
                    resolverVerificacionIdentidad(ciudadano.id, 'VERIFICADA', tarjeta);
                };

                const botonRechazar = document.createElement('button');
                botonRechazar.type = 'button';
                botonRechazar.className = 'btn-salir';
                botonRechazar.textContent = 'Rechazar foto';
                botonRechazar.style.marginLeft = '6px';
                botonRechazar.onclick = () => {
                    resolverVerificacionIdentidad(ciudadano.id, 'RECHAZADA', tarjeta);
                };

                tarjeta.append(nombre, curp, botonFoto, botonAprobar, botonRechazar, espacioFoto);
                lista.insertBefore(tarjeta, lista.children[indice] ?? null);
            }
        } catch (error) {
            if (!consulta.signal.aborted && lista.childElementCount === 0) {
                lista.textContent = 'No fue posible consultar las verificaciones.';
            }
        } finally {
            if (consultaVerificacionesIdentidad === consulta) {
                consultaVerificacionesIdentidad = null;
            }
        }
    }

    async function mostrarFotoIdentidad(idCiudadano, espacioFoto, botonFoto) {
        if (espacioFoto.querySelector('img')) {
            espacioFoto.replaceChildren();
            botonFoto.textContent = 'Ver foto';
            return;
        }

        botonFoto.disabled = true;
        espacioFoto.textContent = 'Cargando foto...';

        try {
            const respuesta = await fetch(
                API + '/monitoreo/verificaciones-identidad/' + idCiudadano + '/foto', {
                    headers: {
                        'Authorization': 'Bearer ' + tokenMonitoreo,
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                }
            );

            if (respuesta.status === 401) {
                cerrarSesionMonitoreo();
                return;
            }

            if (!respuesta.ok) {
                espacioFoto.textContent = 'No se pudo abrir la foto.';
                return;
            }

            const archivo = await respuesta.blob();
            const urlFoto = URL.createObjectURL(archivo);

            try {
                const imagen = document.createElement('img');
                imagen.alt = 'Foto del INE para revisión';
                imagen.style.display = 'block';
                imagen.style.maxWidth = 'min(100%, 720px)';
                imagen.style.marginTop = '12px';
                imagen.src = urlFoto;

                await imagen.decode();

                espacioFoto.replaceChildren(imagen);
                botonFoto.textContent = 'Ocultar foto';
            } finally {
                URL.revokeObjectURL(urlFoto);
            }
        } catch (error) {
            espacioFoto.textContent = 'No fue posible cargar la imagen.';
        } finally {
            botonFoto.disabled = false;
        }
    }

    async function resolverVerificacionIdentidad(idCiudadano, decision, tarjeta) {
        const mensaje = document.getElementById('mensajeDecisionIdentidad');
        mensaje.style.color = '#9F2241';
        delete mensaje.dataset.idCiudadano;

        let motivo = null;

        if (decision === 'VERIFICADA') {
            if (!tarjeta.querySelector('img')) {
                mensaje.textContent = 'Abre y revisa la foto antes de aprobar.';
                return;
            }

            if (!window.confirm('¿Confirmas que el nombre y la CURP coinciden con la foto?')) {
                return;
            }
        } else {
            const texto = window.prompt('Escribe por qué se rechaza la foto.');

            if (texto === null) {
                return;
            }

            motivo = texto.trim();

            if (!motivo || motivo.length > 500) {
                mensaje.textContent = 'Escribe un motivo de entre 1 y 500 caracteres.';
                return;
            }
        }

        const botones = tarjeta.querySelectorAll('button');
        botones.forEach(boton => boton.disabled = true);
        mensaje.style.color = '#252A30';
        mensaje.textContent = 'Guardando decisión...';

        try {
            const respuesta = await fetch(
                API + '/monitoreo/verificaciones-identidad/' + idCiudadano + '/resolver', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + tokenMonitoreo,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        decision,
                        motivo
                    })
                }
            );

            if (respuesta.status === 401) {
                cerrarSesionMonitoreo();
                return;
            }

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent = datos.message ?? 'No se pudo guardar la decisión.';
                return;
            }

            mensaje.style.color = '#235B4E';
            mensaje.textContent = datos.message;
            mensaje.dataset.idCiudadano = String(idCiudadano);
            tarjeta.remove();
            await cargarVerificacionesIdentidad();
        } catch (error) {
            mensaje.style.color = '#9F2241';
            mensaje.textContent = 'No fue posible guardar la decisión.';
        } finally {
            botones.forEach(boton => boton.disabled = false);
        }
    }
</script>