<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>ALERTA IXTA - Policía</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/alerta.css') }}?v=3">

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        #mapaPolicia {
            width: 100%;
            height: 350px;
            border-radius: 12px;
            margin: 15px 0;
        }

        .oculto {
            display: none;
        }

        .alerta-policia {
            background: #f4f4f4;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            cursor: pointer;
        }

        .alerta-policia:hover {
            background: #e9e9e9;
        }

        .alerta-seleccionada {
            border-left: 5px solid #9F2241;
        }

        .estado-policia {
            background: #eef5f3;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }

        .mensaje {
            text-align: center;
            margin: 12px 0;
            font-weight: bold;
        }

        .btn-aceptar {
            background: #9F2241;
            color: white;
            width: 100%;
            margin-top: 10px;
        }

        .marcador-ciudadano {
            width: 26px;
            height: 26px;
            background: #d32f2f;
            border: 4px solid white;
            border-radius: 50%;
            box-shadow: 0 0 0 rgba(211, 47, 47, 0.7);
            animation: pulsoEmergencia 1.5s infinite;
        }

        .marcador-policia {
            width: 36px;
            height: 36px;
            background: #1565c0;
            border: 3px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
        }

        @keyframes pulsoEmergencia {
            0% {
                box-shadow: 0 0 0 0 rgba(211, 47, 47, 0.7);
            }

            70% {
                box-shadow: 0 0 0 18px rgba(211, 47, 47, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(211, 47, 47, 0);
            }
        }

        .btn-rechazar {
            width: 100%;
            margin-top: 8px;
            background: #9F2241;
            color: white;
            font-weight: bold;
        }

        .btn-finalizar {
            width: 100%;
            margin-top: 15px;
            background: #235B4E;
            color: white;
            font-weight: bold;
        }

        .contador-alertas {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            margin-left: 4px;
            background: #9F2241;
            color: white;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .contador-alertas.oculto {
            display: none;
        }

        /* Aviso visible cuando existe una oferta policial pendiente. */
        .aviso-oferta-policia {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #F8E8EC;
            border-top: 2px solid #9F2241;
            padding: 12px;
            flex-shrink: 0;
        }

        .aviso-oferta-policia.oculto {
            display: none;
        }

        .aviso-oferta-texto {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 12px;
            color: #691C32;
        }

        .aviso-oferta-texto strong {
            font-size: 13px;
        }

        .aviso-oferta-policia button {
            background: #691C32;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            white-space: nowrap;
        }

        .indicador-disponibilidad {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .indicador-disponibilidad.disponible {
            background: #E8F5EE;
            color: #235B4E;
        }

        .indicador-disponibilidad.en-servicio {
            background: #FFF2DD;
            color: #956014;
        }
    </style>

</head>

<body>

    <div class="contenedor contenedor-app-movil">

        <div class="app app-movil">

            <header class="app-cabecera">

                <img
                    src="{{ asset('images/logoIxtapaluca2025.png') }}"
                    alt="Gobierno de Ixtapaluca"
                    class="app-cabecera-logo">

                <div class="app-cabecera-texto">

                    <h1 class="app-cabecera-titulo">
                        ALERTA IXTA
                    </h1>

                    <p class="app-cabecera-subtitulo">
                        Sistema Municipal de Alerta
                    </p>

                </div>

                <div aria-hidden="true"></div>

            </header>

            <main class="app-contenido" id="pantallaContenidoPolicia">

                <div id="loginPolicia">

                    <h2>
                        Acceso policial
                    </h2>

                    <div class="campo">

                        <label>
                            Usuario
                        </label>

                        <input
                            type="text"
                            id="usuarioPolicia"
                            placeholder="Usuario">

                    </div>

                    <div class="campo">

                        <label>
                            Contraseña
                        </label>

                        <input
                            type="password"
                            id="passwordPolicia"
                            placeholder="Contraseña">

                    </div>

                    <button
                        type="button"
                        class="boton"
                        onclick="iniciarSesionPolicia()">

                        Iniciar sesión

                    </button>

                    <div
                        id="mensajeLoginPolicia"
                        class="mensaje">
                    </div>

                </div>


                <div
                    id="panelPolicia"
                    class="oculto">

                    <div id="vistaTrabajoPolicia">

                        <section id="vistaInicioPolicia">

                            <h2 id="saludoPolicia">
                                Policía
                            </h2>

                            <div
                                id="indicadorDisponibilidadPolicia"
                                class="indicador-disponibilidad disponible">
                                Disponible
                            </div>

                            <div
                                id="servicioActual"
                                class="estado-policia oculto">

                                <h3>
                                    🚨 Mi servicio actual
                                </h3>

                                <p>
                                    <strong>Folio:</strong>
                                    <span id="folioServicio">-</span>
                                </p>

                                <p>
                                    <strong>Tipo:</strong>
                                    <span id="tipoServicio">-</span>
                                </p>

                                <p>
                                    <strong>Estado:</strong>
                                    <span id="estadoServicio">-</span>
                                </p>

                                <p>
                                    <strong>Mi ubicación:</strong>
                                    <span id="ubicacionPoliciaTexto">
                                        Esperando ubicación...
                                    </span>
                                </p>

                                <p>
                                    <strong>Dirección:</strong>
                                    <span id="direccionServicio">
                                        Obteniendo...
                                    </span>
                                </p>

                                <p>
                                    <strong>Distancia:</strong>
                                    <span id="distanciaServicio">
                                        -
                                    </span>
                                </p>

                                <p>
                                    <strong>Tiempo estimado:</strong>
                                    <span id="tiempoServicio">
                                        -
                                    </span>
                                </p>

                                <form id="formEvidenciaPolicial" onsubmit="subirEvidenciaPolicial(event)" hidden>
                                    <div class="campo">
                                        <label for="fotoEvidenciaPolicial">Foto de evidencia</label>
                                        <input
                                            id="fotoEvidenciaPolicial"
                                            name="evidencia_foto"
                                            type="file"
                                            accept="image/jpeg,image/png"
                                            aria-describedby="ayudaEvidenciaPolicial"
                                            required>
                                        <small id="ayudaEvidenciaPolicial">JPG o PNG de hasta 2 MB.</small>
                                    </div>

                                    <button type="submit" id="btnSubirEvidenciaPolicial" class="boton btn-finalizar">
                                        Subir evidencia
                                    </button>

                                    <p id="mensajeCargaEvidencia" class="info" role="status"></p>
                                </form>

                                <p id="evidenciaGuardadaPolicial" class="info" role="status" hidden>
                                    Evidencia guardada.
                                </p>

                                <button
                                    type="button"
                                    id="btnFinalizar"
                                    class="boton btn-finalizar"
                                    onclick="finalizarAtencion()"
                                    style="display: none;">
                                    Finalizar atención
                                </button>

                            </div>

                        </section>

                        <section
                            id="vistaAlertasPolicia"
                            class="oculto">

                            <p class="info">
                                Alertas de seguridad pendientes
                            </p>


                            <div id="listaAlertas">

                                Cargando alertas...

                            </div>

                        </section>


                        <div id="mapaPolicia"></div>

                        <button
                            type="button"
                            class="boton boton-secundario"
                            onclick="volverAUbicacionPolicia()">

                            Volver a mi ubicación

                        </button>

                        <button
                            type="button"
                            class="boton boton-secundario"
                            onclick="corregirUbicacionPolicia()">
                            Corregir mi ubicación para prueba
                        </button>


                        <div
                            id="detalleAlerta"
                            class="estado-policia oculto">

                            <p>
                                <strong>Folio:</strong>
                                <span id="folioPolicia">-</span>
                            </p>

                            <p>
                                <strong>Tipo:</strong>
                                <span id="tipoPolicia">-</span>
                            </p>

                            <p>
                                <strong>Estado:</strong>
                                <span id="estadoPolicia">-</span>
                            </p>

                            <button
                                type="button"
                                id="btnAceptar"
                                class="boton btn-aceptar"
                                onclick="aceptarAlerta()">

                                Aceptar alerta

                            </button>

                            <button
                                type="button"
                                id="btnRechazar"
                                class="boton btn-rechazar"
                                onclick="rechazarAlerta()">

                                Rechazar alerta

                            </button>

                        </div>


                        <div
                            id="mensajePolicia"
                            class="mensaje">
                        </div>

                    </div>

                    <div
                        id="panelCuentaPolicia"
                        class="estado-policia oculto">

                        <h3>Mi cuenta</h3>

                        <p>
                            <strong>Nombre:</strong>
                            <span id="nombreCuentaPolicia">Policía</span>
                        </p>

                        <p>
                            <strong>Rol:</strong>
                            Policía
                        </p>

                        <button
                            type="button"
                            class="boton"
                            style="background: #691C32; margin-top: 20px;"
                            onclick="cerrarSesionPolicia()">
                            Cerrar sesión
                        </button>

                    </div>

                </div>

            </main>

            <!-- Aviso de oferta policial pendiente -->
            <div
                id="avisoOfertaPolicia"
                class="aviso-oferta-policia oculto"
                role="alert">

                <div class="aviso-oferta-texto">
                    <strong>Nueva alerta de Seguridad</strong>
                    <span>Tienes una solicitud de ayuda pendiente.</span>
                </div>

                <button
                    type="button"
                    onclick="abrirOfertaPolicia()">
                    Ver alerta
                </button>

            </div>

            <nav
                id="navPolicia"
                class="app-nav-inferior"
                aria-label="Navegación policial"
                style="display: none;">

                <button
                    type="button"
                    class="activo"
                    onclick="navegarPolicia('inicio', this)">
                    Inicio
                </button>

                <button
                    type="button"
                    id="pestanaAlertasPolicia"
                    onclick="navegarPolicia('alertas', this)">
                    Alertas
                    <span id="contadorAlertasPolicia" class="contador-alertas oculto">0</span>
                </button>

                <button
                    type="button"
                    onclick="navegarPolicia('cuenta', this)">
                    Cuenta
                </button>

            </nav>

        </div>

    </div>


    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>


    <script>
        const API = '/api';

        let tokenPolicia =
            sessionStorage.getItem(
                'alerta_ixta_token_policia'
            );

        let mapaPolicia = null;

        let marcadorAlerta = null;

        let marcadorPolicia = null;

        let idAlertaSeleccionada = null;

        let idServicioActual = null;

        let latitudPolicia = null;

        let longitudPolicia = null;

        let precisionPolicia = null;

        let intervaloUbicacion = null;

        let ubicacionManual = false;

        let operacionPoliciaEnCurso = false;

        let modoMapaPolicia = 'POLICIA';

        let mostrarRutaCompletaUnaVez = false;

        let seguirPoliciaEnMapa = true;

        let rutaPolicia = null;

        let latitudDestino = null;

        let longitudDestino = null;

        const iconoCiudadano = L.divIcon({
            className: '',
            html: '<div class="marcador-ciudadano"></div>',
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        const iconoPolicia = L.divIcon({
            className: '',
            html: '<div class="marcador-policia">🚓</div>',
            iconSize: [42, 42],
            iconAnchor: [21, 21]
        });

        // Abrir las alertas pendientes desde el aviso.
        function abrirOfertaPolicia() {

            const botonAlertas = document.getElementById(
                'pestanaAlertasPolicia'
            );

            if (botonAlertas) {
                navegarPolicia('alertas', botonAlertas);
            }
        }

        // Navegar entre las secciones del panel policial.
        // Cambiar entre Inicio, Alertas y Cuenta.
        function navegarPolicia(seccion, boton) {

            const contenido = document.getElementById(
                'pantallaContenidoPolicia'
            );

            const vistaTrabajo = document.getElementById(
                'vistaTrabajoPolicia'
            );

            const vistaInicio = document.getElementById(
                'vistaInicioPolicia'
            );

            const vistaAlertas = document.getElementById(
                'vistaAlertasPolicia'
            );

            const vistaCuenta = document.getElementById(
                'panelCuentaPolicia'
            );

            // Mostrar únicamente la sección seleccionada.
            if (seccion === 'cuenta') {

                vistaTrabajo.classList.add('oculto');
                vistaCuenta.classList.remove('oculto');

            } else {

                vistaCuenta.classList.add('oculto');
                vistaTrabajo.classList.remove('oculto');

                if (seccion === 'inicio') {
                    vistaInicio.classList.remove('oculto');
                    vistaAlertas.classList.add('oculto');
                }

                if (seccion === 'alertas') {
                    vistaInicio.classList.add('oculto');
                    vistaAlertas.classList.remove('oculto');
                }

                // Ajustar el mapa después de cambiar de sección.
                if (mapaPolicia) {
                    requestAnimationFrame(function() {
                        mapaPolicia.invalidateSize();
                    });
                }
            }

            // Regresar al principio del contenido.
            contenido.scrollTo({
                top: 0,
                behavior: 'auto'
            });

            // Actualizar la pestaña seleccionada.
            document.querySelectorAll('#navPolicia button')
                .forEach(function(elemento) {
                    elemento.classList.remove('activo');
                });

            boton.classList.add('activo');
        }

        // Cerrar la sesión del policía.
        async function cerrarSesionPolicia() {

            // No cerrar sesión mientras se atiende una emergencia.
            if (idServicioActual !== null) {
                alert('No puedes cerrar sesión mientras atiendes una alerta.');
                return;
            }

            const confirmar = confirm(
                '¿Deseas cerrar tu sesión?'
            );

            if (!confirmar) {
                return;
            }

            try {

                const respuesta = await fetch(
                    API + '/logout', {
                        method: 'POST',

                        headers: {
                            'Authorization': 'Bearer ' + tokenPolicia,
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!respuesta.ok) {
                    alert('No fue posible cerrar la sesión. Intenta nuevamente.');
                    return;
                }

                // Detener el envío automático de ubicación.
                if (intervaloUbicacion !== null) {
                    clearInterval(intervaloUbicacion);
                    intervaloUbicacion = null;
                }

                // Eliminar el token guardado en el navegador.
                sessionStorage.removeItem(
                    'alerta_ixta_token_policia'
                );

                tokenPolicia = null;

                // Regresar a la pantalla de acceso policial.
                window.location.reload();

            } catch (error) {

                console.error('Error al cerrar sesión:', error);

                alert('No fue posible conectar con el servidor.');

            }
        }

        async function iniciarSesionPolicia() {

            const usuario =
                document
                .getElementById(
                    'usuarioPolicia'
                )
                .value
                .trim();

            const password =
                document
                .getElementById(
                    'passwordPolicia'
                )
                .value;

            const mensaje =
                document.getElementById(
                    'mensajeLoginPolicia'
                );


            if (
                usuario === '' ||
                password === ''
            ) {

                mensaje.textContent =
                    'Ingresa usuario y contraseña.';

                return;
            }


            try {

                const respuesta = await fetch(
                    API + '/personal/login', {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',

                            'Accept': 'application/json'
                        },

                        body: JSON.stringify({
                            usuario: usuario,
                            password: password
                        })
                    }
                );


                const datos =
                    await respuesta.json();


                if (!respuesta.ok) {

                    mensaje.textContent =
                        datos.message ??
                        'No fue posible iniciar sesión.';

                    return;
                }


                const personal =
                    datos.personal ??
                    datos.usuario ??
                    datos;


                if (
                    personal.rol &&
                    personal.rol !== 'POLICIA'
                ) {

                    mensaje.textContent =
                        'Esta interfaz es exclusiva para policías.';

                    return;
                }


                tokenPolicia =
                    datos.token;


                sessionStorage.setItem(
                    'alerta_ixta_token_policia',
                    tokenPolicia
                );


                document
                    .getElementById(
                        'loginPolicia'
                    )
                    .classList
                    .add('oculto');


                document
                    .getElementById(
                        'panelPolicia'
                    )
                    .classList
                    .remove('oculto');

                // Mostrar la barra inferior después de iniciar sesión.
                document.getElementById('navPolicia').style.display = 'flex';

                // Mostrar el nombre del policía en la sección Cuenta.
                document.getElementById('nombreCuentaPolicia').textContent =
                    personal.nombre ?? 'Policía';


                document
                    .getElementById(
                        'saludoPolicia'
                    )
                    .textContent =
                    'Hola, ' +
                    (
                        personal.nombre ??
                        'Policía'
                    );


                iniciarMapaPolicia();

                await cargarServicioActual();

                await cargarAlertas();

                obtenerUbicacionPolicia();


            } catch (error) {

                mensaje.textContent =
                    'No fue posible conectar con el servidor.';

            }

        }


        function iniciarMapaPolicia() {

            if (mapaPolicia) {
                return;
            }


            mapaPolicia =
                L.map(
                    'mapaPolicia'
                )
                .setView(
                    [19.3132, -98.8843],
                    14
                );


            L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }
                )
                .addTo(
                    mapaPolicia
                );

            /*
             * Si el policía mueve manualmente
             * el mapa durante un servicio,
             * dejamos de seguirlo automáticamente.
             */
            mapaPolicia.on(
                'dragstart',
                function() {

                    if (
                        modoMapaPolicia ===
                        'SERVICIO'
                    ) {

                        seguirPoliciaEnMapa =
                            false;

                    }

                }
            );


            /*
             * También detectamos otras interacciones
             * manuales sobre el mapa.
             */
            mapaPolicia
                .getContainer()
                .addEventListener(
                    'pointerdown',
                    function() {

                        if (
                            modoMapaPolicia ===
                            'SERVICIO'
                        ) {

                            seguirPoliciaEnMapa =
                                false;

                        }

                    }
                );

            mapaPolicia.on(
                'dragstart',
                function() {

                    if (
                        modoMapaPolicia ===
                        'SERVICIO'
                    ) {

                        seguirPoliciaEnMapa =
                            false;

                    }

                }
            );

        }

        // Actualizar el indicador según el servicio policial.
        function actualizarDisponibilidadVisual() {

            const indicador = document.getElementById(
                'indicadorDisponibilidadPolicia'
            );

            if (!indicador) {
                return;
            }

            indicador.classList.remove(
                'disponible',
                'en-servicio'
            );

            if (idServicioActual !== null) {

                indicador.textContent = 'En servicio';

                indicador.classList.add('en-servicio');

            } else {

                indicador.textContent = 'Disponible';

                indicador.classList.add('disponible');
            }
        }

        let ultimaConsultaServicio = 0;

        function mostrarControlesEvidencia(servicio) {
            const enSitio = servicio?.estado === 'EN_SITIO';
            const tieneEvidencia = enSitio && servicio.tiene_evidencia === true;

            document.getElementById('formEvidenciaPolicial').hidden = !enSitio || tieneEvidencia;
            document.getElementById('evidenciaGuardadaPolicial').hidden = !tieneEvidencia;
            document.getElementById('btnFinalizar').style.display = tieneEvidencia ? 'block' : 'none';
        }

        async function cargarServicioActual() {
            if (operacionPoliciaEnCurso) {
                return;
            }

            const consulta = ++ultimaConsultaServicio;
            const idAlConsultar = idServicioActual;

            try {
                const respuesta = await fetch(API + '/policia/servicio-actual', {
                    headers: {
                        'Authorization': 'Bearer ' + tokenPolicia,
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                });

                if (!respuesta.ok) {
                    return;
                }

                const {
                    servicio
                } = await respuesta.json();

                if (
                    consulta !== ultimaConsultaServicio ||
                    operacionPoliciaEnCurso ||
                    idAlConsultar !== idServicioActual
                ) {
                    return;
                }

                const idNuevo = servicio?.id_alerta ?? null;

                if (idNuevo !== idServicioActual) {
                    document.getElementById('formEvidenciaPolicial').reset();
                    document.getElementById('mensajeCargaEvidencia').textContent = '';
                }

                idServicioActual = idNuevo;
                actualizarDisponibilidadVisual();

                document.getElementById('servicioActual')
                    .classList.toggle('oculto', !servicio);

                mostrarControlesEvidencia(servicio);

                if (!servicio) {
                    return;
                }

                document.getElementById('folioServicio').textContent = servicio.folio;
                document.getElementById('tipoServicio').textContent = servicio.tipo;
                document.getElementById('estadoServicio').textContent = servicio.estado;

                if (servicio.ubicacion?.latitud && servicio.ubicacion?.longitud) {
                    mostrarDestinoEnMapa(
                        parseFloat(servicio.ubicacion.latitud),
                        parseFloat(servicio.ubicacion.longitud)
                    );
                }
            } catch (error) {
                console.error('Error al cargar servicio:', error);
            }
        }

        function mostrarDestinoEnMapa(
            latitud,
            longitud
        ) {

            latitudDestino =
                latitud;

            longitudDestino =
                longitud;


            if (!marcadorAlerta) {

                marcadorAlerta =
                    L.marker(
                        [
                            latitud,
                            longitud
                        ], {
                            icon: iconoCiudadano
                        }
                    )
                    .addTo(
                        mapaPolicia
                    )
                    .bindPopup(
                        'Ciudadano solicitando ayuda'
                    );

            } else {

                marcadorAlerta
                    .setLatLng([
                        latitud,
                        longitud
                    ]);

            }

            if (
                latitudPolicia !== null &&
                longitudPolicia !== null
            ) {

                calcularRuta();

            }


            obtenerDireccionDestino();

        }


        async function cargarAlertas() {

            const contenedor =
                document.getElementById(
                    'listaAlertas'
                );


            try {

                const respuesta = await fetch(
                    API + '/monitoreo/alertas', {
                        headers: {
                            'Authorization': 'Bearer ' +
                                tokenPolicia,

                            'Accept': 'application/json'
                        }
                    }
                );


                const datos =
                    await respuesta.json();


                if (!respuesta.ok) {

                    contenedor.textContent =
                        datos.message ??
                        'No fue posible consultar alertas.';

                    return;
                }


                const alertas =
                    datos.alertas ?? [];


                // Por ahora el policía solamente verá
                // alertas nuevas de Seguridad.
                const disponibles =
                    alertas.filter(
                        function(alerta) {

                            return (
                                alerta.tipo ===
                                'Seguridad' &&

                                alerta.estado ===
                                'NUEVA'
                            );

                        }
                    );

                // Mostrar cuántas ofertas tiene pendientes este policía.
                const contador = document.getElementById(
                    'contadorAlertasPolicia'
                );

                if (contador) {

                    contador.textContent = disponibles.length;

                    contador.classList.toggle(
                        'oculto',
                        disponibles.length === 0
                    );

                }

                // Mostrar el aviso si el policía tiene ofertas pendientes.
                const avisoOferta = document.getElementById(
                    'avisoOfertaPolicia'
                );

                if (avisoOferta) {
                    avisoOferta.classList.toggle(
                        'oculto',
                        disponibles.length === 0
                    );
                }

                /*
                  Si el policía tenía una alerta seleccionada pero ya no aparece entre sus ofertas,
                  cerrar automáticamente el detalle.
                  Puede ocurrir porque:
                  - el ciudadano la canceló,
                  - venció la oferta,
                  - fue enviada a otro policía,
                  - o cambió de estado.
                 */
                if (
                    idAlertaSeleccionada !== null &&
                    idServicioActual === null
                ) {

                    const sigueDisponible =
                        disponibles.some(
                            function(alerta) {

                                return (
                                    Number(
                                        alerta.id_alerta
                                    ) ===
                                    Number(
                                        idAlertaSeleccionada
                                    )
                                );
                            }
                        );


                    if (!sigueDisponible) {

                        idAlertaSeleccionada =
                            null;


                        modoMapaPolicia =
                            'POLICIA';


                        document
                            .getElementById(
                                'detalleAlerta'
                            )
                            .classList
                            .add('oculto');


                        document
                            .getElementById(
                                'btnAceptar'
                            )
                            .style.display =
                            'none';


                        document
                            .getElementById(
                                'btnRechazar'
                            )
                            .style.display =
                            'none';


                        /*
                         * Quitar del mapa al ciudadano
                         * de la oferta que ya terminó.
                         */
                        if (marcadorAlerta) {

                            mapaPolicia.removeLayer(
                                marcadorAlerta
                            );

                            marcadorAlerta =
                                null;
                        }


                        /*
                         * Quitar una posible ruta
                         * de revisión.
                         */
                        if (rutaPolicia) {

                            mapaPolicia.removeLayer(
                                rutaPolicia
                            );

                            rutaPolicia =
                                null;
                        }


                        latitudDestino =
                            null;

                        longitudDestino =
                            null;


                        document
                            .getElementById(
                                'mensajePolicia'
                            )
                            .textContent =
                            'La alerta seleccionada ya no está disponible para atención.';
                    }
                }


                contenedor.innerHTML = '';


                if (
                    disponibles.length === 0
                ) {

                    contenedor.textContent =
                        'No hay alertas de seguridad pendientes.';

                    return;
                }


                disponibles.forEach(
                    function(alerta) {

                        const tarjeta =
                            document.createElement(
                                'div'
                            );


                        tarjeta.className =
                            'alerta-policia';


                        tarjeta.innerHTML =
                            '<strong>' +
                            alerta.folio +
                            '</strong><br>' +

                            'Tipo: ' +
                            alerta.tipo +
                            '<br>' +

                            'Estado: ' +
                            alerta.estado;


                        tarjeta.onclick =
                            function() {

                                seleccionarAlerta(
                                    alerta.id_alerta,
                                    tarjeta
                                );

                            };


                        contenedor.appendChild(
                            tarjeta
                        );

                    }
                );


            } catch (error) {

                contenedor.textContent =
                    'Error al consultar las alertas.';

            }

        }


        async function seleccionarAlerta(
            idAlerta,
            tarjeta
        ) {

            idAlertaSeleccionada =
                idAlerta;

            modoMapaPolicia =
                'REVISANDO';

            const detallePanel =
                document.getElementById(
                    'detalleAlerta'
                );


            const btnAceptar =
                document.getElementById(
                    'btnAceptar'
                );

            const btnRechazar =
                document.getElementById(
                    'btnRechazar'
                );


            detallePanel
                .classList
                .remove('oculto');


            btnAceptar.style.display =
                'block';


            btnAceptar.disabled =
                true;


            btnAceptar.textContent =
                'Cargando alerta...';


            document
                .querySelectorAll(
                    '.alerta-policia'
                )
                .forEach(
                    function(elemento) {

                        elemento.classList.remove(
                            'alerta-seleccionada'
                        );

                    }
                );


            tarjeta.classList.add(
                'alerta-seleccionada'
            );


            const respuesta = await fetch(
                API +
                '/monitoreo/alertas/' +
                idAlerta, {
                    headers: {
                        'Authorization': 'Bearer ' +
                            tokenPolicia,

                        'Accept': 'application/json'
                    }
                }
            );


            const datos =
                await respuesta.json();


            if (!respuesta.ok) {
                return;
            }


            const alerta =
                datos.alerta;


            document
                .getElementById(
                    'detalleAlerta'
                )
                .classList
                .remove('oculto');


            document
                .getElementById(
                    'folioPolicia'
                )
                .textContent =
                alerta.folio;


            document
                .getElementById(
                    'tipoPolicia'
                )
                .textContent =
                alerta.tipo;


            document
                .getElementById(
                    'estadoPolicia'
                )
                .textContent =
                alerta.estado;




            if (
                alerta.estado ===
                'NUEVA'
            ) {

                btnAceptar.style.display =
                    'block';

                btnAceptar.disabled =
                    false;

                btnAceptar.textContent =
                    'Aceptar alerta';

                btnRechazar.style.display =
                    'block';

                btnRechazar.disabled =
                    false;

                btnRechazar.textContent =
                    'Rechazar alerta';

            } else {

                btnAceptar.style.display =
                    'none';

                btnRechazar.style.display =
                    'none';


            }


            if (
                alerta.ubicacion &&
                alerta.ubicacion.latitud &&
                alerta.ubicacion.longitud
            ) {

                const latitud =
                    parseFloat(
                        alerta.ubicacion.latitud
                    );


                const longitud =
                    parseFloat(
                        alerta.ubicacion.longitud
                    );


                if (!marcadorAlerta) {

                    marcadorAlerta =
                        L.marker([
                            latitud,
                            longitud
                        ])
                        .addTo(
                            mapaPolicia
                        )
                        .bindPopup(
                            'Ciudadano solicitando ayuda'
                        );

                } else {

                    marcadorAlerta
                        .setLatLng([
                            latitud,
                            longitud
                        ]);

                }


                mapaPolicia.setView(
                    [
                        latitud,
                        longitud
                    ],
                    16
                );

            }

        }


        async function aceptarAlerta() {

            if (!idAlertaSeleccionada) {
                return;
            }

            const confirmar =
                window.confirm(
                    '¿Confirmas que deseas aceptar esta alerta?'
                );


            if (!confirmar) {
                return;
            }


            const mensaje =
                document.getElementById(
                    'mensajePolicia'
                );


            const respuesta = await fetch(
                API +
                '/monitoreo/alertas/' +
                idAlertaSeleccionada +
                '/aceptar', {
                    method: 'POST',

                    headers: {
                        'Authorization': 'Bearer ' +
                            tokenPolicia,

                        'Accept': 'application/json'
                    }
                }
            );


            const datos =
                await respuesta.json();


            if (!respuesta.ok) {

                mensaje.style.color =
                    '#9F2241';

                mensaje.textContent =
                    datos.message ??
                    'No fue posible aceptar la alerta.';

                return;
            }


            mensaje.style.color =
                '#235B4E';


            mensaje.textContent =
                'Alerta aceptada. Dirígete al ciudadano.';


            document
                .getElementById(
                    'estadoPolicia'
                )
                .textContent =
                'EN_CAMINO';


            document
                .getElementById(
                    'btnAceptar'
                )
                .style.display =
                'none';

            document
                .getElementById(
                    'btnRechazar'
                )
                .style.display =
                'none';

            idServicioActual =
                idAlertaSeleccionada;

            actualizarDisponibilidadVisual();

            modoMapaPolicia =
                'SERVICIO';

            mostrarRutaCompletaUnaVez = true;
            seguirPoliciaEnMapa = true;

            // Ir automáticamente a Inicio cuando el policía acepta la alerta.
            const botonInicioPolicia = document.querySelector(
                '#navPolicia button'
            );

            if (botonInicioPolicia) {
                navegarPolicia('inicio', botonInicioPolicia);
            }

            // Cargar el servicio aceptado y su ubicación.
            await cargarServicioActual();

            await cargarAlertas();

        }

        async function rechazarAlerta() {

            if (!idAlertaSeleccionada) {
                return;
            }

            const confirmar =
                window.confirm(
                    'Rechazar alerta'
                );


            if (!confirmar) {
                return;
            }


            const boton =
                document.getElementById(
                    'btnRechazar'
                );


            const mensaje =
                document.getElementById(
                    'mensajePolicia'
                );


            boton.disabled =
                true;


            boton.textContent =
                'Rechazando...';


            try {

                const respuesta =
                    await fetch(
                        API +
                        '/monitoreo/alertas/' +
                        idAlertaSeleccionada +
                        '/rechazar', {

                            method: 'POST',

                            headers: {

                                'Authorization': 'Bearer ' +
                                    tokenPolicia,

                                'Accept': 'application/json'

                            }

                        }
                    );


                const datos =
                    await respuesta.json();


                if (!respuesta.ok) {

                    mensaje.style.color =
                        '#9F2241';


                    mensaje.textContent =
                        datos.message ??
                        'No fue posible rechazar la alerta.';


                    boton.disabled =
                        false;


                    boton.textContent =
                        'Rechazar alerta';


                    return;
                }


                mensaje.style.color =
                    '#235B4E';


                mensaje.textContent =
                    'Oferta rechazada. Se buscará otro policía disponible.';


                /*
                 * Limpiar la alerta que
                 * acaba de rechazar.
                 */
                if (marcadorAlerta) {

                    mapaPolicia.removeLayer(
                        marcadorAlerta
                    );

                    marcadorAlerta =
                        null;

                }


                if (rutaPolicia) {

                    mapaPolicia.removeLayer(
                        rutaPolicia
                    );

                    rutaPolicia =
                        null;

                }


                latitudDestino =
                    null;


                longitudDestino =
                    null;


                idAlertaSeleccionada =
                    null;


                document
                    .getElementById(
                        'detalleAlerta'
                    )
                    .classList
                    .add('oculto');


                await cargarAlertas();


            } catch (error) {

                console.error(
                    'Error rechazando alerta:',
                    error
                );


                mensaje.style.color =
                    '#9F2241';


                mensaje.textContent =
                    'No fue posible conectar con el servidor.';


                boton.disabled =
                    false;


                boton.textContent =
                    'Rechazar alerta';

            }

        }

        function obtenerUbicacionPolicia() {

            if (!navigator.geolocation) {

                document
                    .getElementById(
                        'ubicacionPoliciaTexto'
                    )
                    .textContent =
                    'GPS no disponible';

                return;
            }


            navigator.geolocation.getCurrentPosition(

                async function(posicion) {

                        if (ubicacionManual) {
                            return;
                        }


                        latitudPolicia =
                            posicion.coords.latitude;

                        longitudPolicia =
                            posicion.coords.longitude;

                        precisionPolicia =
                            posicion.coords.accuracy;


                        actualizarMarcadorPolicia();


                        await enviarUbicacionPolicia();


                        iniciarEnvioAutomaticoUbicacion();

                    },


                    function(error) {

                        document
                            .getElementById(
                                'ubicacionPoliciaTexto'
                            )
                            .textContent =
                            'No se pudo obtener la ubicación.';

                    },


                    {
                        enableHighAccuracy: true,
                        timeout: 30000,
                        maximumAge: 5000
                    }

            );

        }

        function actualizarMarcadorPolicia() {

            if (
                latitudPolicia === null ||
                longitudPolicia === null
            ) {
                return;
            }


            if (!marcadorPolicia) {

                marcadorPolicia =
                    L.marker(
                        [
                            latitudPolicia,
                            longitudPolicia
                        ], {
                            icon: iconoPolicia,
                            zIndexOffset: 1000
                        }
                    )
                    .addTo(
                        mapaPolicia
                    )
                    .bindPopup(
                        'Mi ubicación'
                    );

            } else {

                marcadorPolicia
                    .setLatLng([
                        latitudPolicia,
                        longitudPolicia
                    ]);

            }


            document
                .getElementById(
                    'ubicacionPoliciaTexto'
                )
                .textContent =
                latitudPolicia.toFixed(6) +
                ', ' +
                longitudPolicia.toFixed(6);

            if (
                modoMapaPolicia ===
                'POLICIA'
            ) {

                mapaPolicia.setView(
                    [
                        latitudPolicia,
                        longitudPolicia
                    ],
                    16
                );

            }


            if (
                modoMapaPolicia ===
                'SERVICIO' &&
                latitudDestino !== null &&
                longitudDestino !== null
            ) {

                calcularRuta();

            }

        }

        async function enviarUbicacionPolicia() {

            if (
                latitudPolicia === null ||
                longitudPolicia === null
            ) {
                return;
            }


            try {

                const respuesta = await fetch(
                    API + '/policia/ubicacion', {
                        method: 'POST',

                        headers: {
                            'Authorization': 'Bearer ' +
                                tokenPolicia,

                            'Content-Type': 'application/json',

                            'Accept': 'application/json'
                        },

                        body: JSON.stringify({
                            latitud: latitudPolicia,

                            longitud: longitudPolicia,

                            precision_m: precisionPolicia
                        })
                    }
                );


                const datos =
                    await respuesta.json();


                if (!respuesta.ok) {

                    console.error(
                        datos
                    );

                    return;
                }


                if (datos.alerta) {

                    document
                        .getElementById(
                            'estadoServicio'
                        )
                        .textContent =
                        datos.alerta.estado;

                }


            } catch (error) {

                console.error(
                    'Error enviando ubicación:',
                    error
                );

            }

        }

        function iniciarEnvioAutomaticoUbicacion() {

            if (intervaloUbicacion) {

                clearInterval(
                    intervaloUbicacion
                );

            }


            intervaloUbicacion =
                setInterval(
                    async function() {

                            if (
                                operacionPoliciaEnCurso
                            ) {
                                return;
                            }

                            if (!ubicacionManual) {

                                navigator.geolocation
                                    .getCurrentPosition(

                                        async function(
                                            posicion
                                        ) {

                                            latitudPolicia =
                                                posicion
                                                .coords
                                                .latitude;

                                            longitudPolicia =
                                                posicion
                                                .coords
                                                .longitude;

                                            precisionPolicia =
                                                posicion
                                                .coords
                                                .accuracy;


                                            actualizarMarcadorPolicia();

                                            await enviarUbicacionPolicia();

                                            await cargarServicioActual();

                                        }

                                    );

                            } else {

                                await enviarUbicacionPolicia();

                                await cargarServicioActual();

                            }

                        },

                        5000
                );

        }

        function volverAUbicacionPolicia() {

            if (
                latitudPolicia === null ||
                longitudPolicia === null
            ) {
                return;
            }


            /*
             * Si está atendiendo un servicio,
             * regresamos al policía y volvemos
             * a activar su seguimiento.
             */
            if (
                idServicioActual
            ) {

                modoMapaPolicia =
                    'SERVICIO';


                seguirPoliciaEnMapa =
                    true;

            } else {

                modoMapaPolicia =
                    'POLICIA';


                seguirPoliciaEnMapa =
                    true;

            }


            mapaPolicia.setView(
                [
                    latitudPolicia,
                    longitudPolicia
                ],
                16
            );

        }

        function corregirUbicacionPolicia() {

            ubicacionManual = true;

            // Enfocar el mapa sin desplazar el panel.
            mapaPolicia.getContainer().focus({
                preventScroll: true
            });

            document
                .getElementById(
                    'ubicacionPoliciaTexto'
                )
                .textContent =
                'Haz clic en el mapa sobre la ubicación del policía.';


            mapaPolicia.once(
                'click',
                async function(evento) {

                    latitudPolicia =
                        evento.latlng.lat;

                    longitudPolicia =
                        evento.latlng.lng;

                    precisionPolicia =
                        null;


                    actualizarMarcadorPolicia();


                    await enviarUbicacionPolicia();


                    iniciarEnvioAutomaticoUbicacion();

                }
            );

        }

        async function calcularRuta() {

            if (
                latitudPolicia === null ||
                longitudPolicia === null ||
                latitudDestino === null ||
                longitudDestino === null
            ) {
                return;
            }


            try {

                const url =
                    'https://router.project-osrm.org/route/v1/driving/' +
                    longitudPolicia + ',' +
                    latitudPolicia + ';' +
                    longitudDestino + ',' +
                    latitudDestino +
                    '?overview=full&geometries=geojson';


                const respuesta =
                    await fetch(url);


                const datos =
                    await respuesta.json();


                if (
                    !datos.routes ||
                    datos.routes.length === 0
                ) {
                    return;
                }


                const ruta =
                    datos.routes[0];


                const coordenadas =
                    ruta.geometry.coordinates.map(
                        function(coordenada) {

                            return [
                                coordenada[1],
                                coordenada[0]
                            ];

                        }
                    );


                if (rutaPolicia) {

                    mapaPolicia.removeLayer(
                        rutaPolicia
                    );

                }


                rutaPolicia =
                    L.polyline(
                        coordenadas, {
                            weight: 5
                        }
                    )
                    .addTo(
                        mapaPolicia
                    );


                if (
                    mostrarRutaCompletaUnaVez
                ) {

                    mapaPolicia.fitBounds(
                        rutaPolicia.getBounds(), {
                            padding: [
                                30,
                                30
                            ]
                        }
                    );


                    mostrarRutaCompletaUnaVez =
                        false;


                    setTimeout(
                        function() {

                            if (
                                modoMapaPolicia ===
                                'SERVICIO' &&
                                seguirPoliciaEnMapa &&
                                latitudPolicia !== null &&
                                longitudPolicia !== null
                            ) {

                                mapaPolicia.setView(
                                    [
                                        latitudPolicia,
                                        longitudPolicia
                                    ],
                                    16
                                );

                            }

                        },
                        2000
                    );


                } else if (
                    modoMapaPolicia === 'SERVICIO' && seguirPoliciaEnMapa && latitudPolicia !== null &&
                    longitudPolicia !== null
                ) {

                    mapaPolicia.setView(
                        [
                            latitudPolicia,
                            longitudPolicia
                        ],
                        16
                    );

                }


                const distanciaKm =
                    ruta.distance / 1000;


                const minutos =
                    Math.ceil(
                        ruta.duration / 60
                    );


                document
                    .getElementById(
                        'distanciaServicio'
                    )
                    .textContent =
                    distanciaKm.toFixed(2) +
                    ' km';


                document
                    .getElementById(
                        'tiempoServicio'
                    )
                    .textContent =
                    minutos +
                    ' min';


            } catch (error) {

                console.error(
                    'No se pudo calcular la ruta:',
                    error
                );

            }

        }

        async function obtenerDireccionDestino() {

            if (
                latitudDestino === null ||
                longitudDestino === null
            ) {
                return;
            }


            try {

                const url =
                    'https://nominatim.openstreetmap.org/reverse' +
                    '?format=json' +
                    '&lat=' +
                    latitudDestino +
                    '&lon=' +
                    longitudDestino;


                const respuesta =
                    await fetch(url);


                const datos =
                    await respuesta.json();


                document
                    .getElementById(
                        'direccionServicio'
                    )
                    .textContent =
                    datos.display_name ??
                    'Dirección no disponible';


            } catch (error) {

                document
                    .getElementById(
                        'direccionServicio'
                    )
                    .textContent =
                    'Dirección no disponible';

            }

        }

        async function subirEvidenciaPolicial(evento) {
            evento.preventDefault();

            const idAlerta = idServicioActual;
            const formulario = evento.currentTarget;
            const campoFoto = document.getElementById('fotoEvidenciaPolicial');
            const boton = document.getElementById('btnSubirEvidenciaPolicial');
            const mensaje = document.getElementById('mensajeCargaEvidencia');
            const foto = campoFoto.files[0];

            if (!idAlerta || operacionPoliciaEnCurso || !foto) {
                return;
            }

            if (foto.size > 2 * 1024 * 1024) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent = 'Selecciona una foto de hasta 2 MB.';
                return;
            }

            const datosFoto = new FormData(formulario);
            operacionPoliciaEnCurso = true;
            ultimaConsultaServicio++;
            campoFoto.disabled = true;
            boton.disabled = true;
            mensaje.style.color = '#252A30';
            mensaje.textContent = 'Subiendo evidencia...';

            try {
                const respuesta = await fetch(API + '/monitoreo/alertas/' + idAlerta + '/evidencia', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + tokenPolicia,
                        'Accept': 'application/json'
                    },
                    body: datosFoto
                });

                const datos = await respuesta.json();

                if (!respuesta.ok) {
                    mensaje.style.color = '#9F2241';
                    mensaje.textContent = datos.errors?.evidencia_foto?.[0] ??
                        datos.message ??
                        'No se pudo subir la evidencia.';
                    return;
                }

                formulario.reset();
                mensaje.textContent = '';

                if (idServicioActual === idAlerta) {
                    mostrarControlesEvidencia({
                        estado: 'EN_SITIO',
                        tiene_evidencia: true
                    });
                }
            } catch (error) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent = 'No fue posible subir la evidencia. Intenta nuevamente.';
            } finally {
                campoFoto.disabled = false;
                boton.disabled = false;
                operacionPoliciaEnCurso = false;
            }
        }

        async function finalizarAtencion() {

            if (
                !idServicioActual ||
                operacionPoliciaEnCurso
            ) {
                return;
            }


            const boton =
                document.getElementById(
                    'btnFinalizar'
                );


            const mensaje =
                document.getElementById(
                    'mensajePolicia'
                );


            const confirmar =
                window.confirm(
                    '¿Confirmas que la atención de la emergencia ha terminado?'
                );


            if (!confirmar) {
                return;
            }


            operacionPoliciaEnCurso =
                true;


            boton.disabled =
                true;


            boton.textContent =
                'Finalizando...';


            try {

                const respuesta =
                    await fetch(
                        API +
                        '/monitoreo/alertas/' +
                        idServicioActual +
                        '/cerrar', {

                            method: 'POST',

                            headers: {

                                'Authorization': 'Bearer ' +
                                    tokenPolicia,

                                'Content-Type': 'application/json',

                                'Accept': 'application/json'

                            },

                            body: JSON.stringify({

                                observaciones: 'La atención de la alerta fue concluida por el policía.'

                            })

                        }
                    );


                const datos =
                    await respuesta.json();


                if (!respuesta.ok) {

                    mensaje.style.color =
                        '#9F2241';


                    mensaje.textContent =
                        datos.message ??
                        'No fue posible cerrar la alerta.';


                    boton.disabled =
                        false;


                    boton.textContent =
                        'Finalizar atención';


                    return;
                }


                mensaje.style.color =
                    '#235B4E';

                mensaje.textContent = datos.message ?? 'Atención finalizada correctamente.';

                /*
                 * Limpiar el servicio terminado.
                 */

                if (
                    marcadorAlerta
                ) {

                    mapaPolicia.removeLayer(
                        marcadorAlerta
                    );

                    marcadorAlerta =
                        null;

                }


                if (
                    rutaPolicia
                ) {

                    mapaPolicia.removeLayer(
                        rutaPolicia
                    );

                    rutaPolicia =
                        null;

                }


                latitudDestino =
                    null;


                longitudDestino =
                    null;


                idServicioActual =
                    null;
                actualizarDisponibilidadVisual();

                idAlertaSeleccionada =
                    null;


                document
                    .getElementById(
                        'servicioActual'
                    )
                    .classList
                    .add('oculto');


                document
                    .getElementById(
                        'detalleAlerta'
                    )
                    .classList
                    .add('oculto');


                boton.style.display =
                    'none';


                boton.disabled =
                    false;


                boton.textContent =
                    'Finalizar atención';


                /*
                 * Después del servicio queda
                 * únicamente el policía.
                 */

                if (
                    latitudPolicia !== null &&
                    longitudPolicia !== null
                ) {

                    mapaPolicia.setView(
                        [
                            latitudPolicia,
                            longitudPolicia
                        ],
                        16
                    );

                }


                await cargarAlertas();


            } catch (error) {

                console.error(
                    'Error cerrando alerta:',
                    error
                );


                mensaje.style.color =
                    '#9F2241';


                mensaje.textContent =
                    'No fue posible finalizar la atención. Intenta nuevamente.';


                boton.disabled =
                    false;


                boton.textContent =
                    'Finalizar atención';


            } finally {

                operacionPoliciaEnCurso =
                    false;

            }

        }

        // Actualizar automáticamente las alertas pendientes.
        setInterval(
            function() {

                if (
                    tokenPolicia &&
                    !operacionPoliciaEnCurso
                ) {

                    cargarAlertas();

                }

            },
            5000
        );
    </script>

</body>

</html>