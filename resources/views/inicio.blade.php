<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>ALERTA IXTA</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/alerta.css') }}?v=2">

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        #mapa {
            width: 100%;
            height: 350px;
            border-radius: 12px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .estado-alerta {
            background: #f4f4f4;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .estado-alerta p {
            margin: 7px 0;
        }

        .boton-cerrar-sesion {
            background: #691C32;
            color: white;
            margin-top: 10px;
        }

        .boton-cancelar-alerta {
            background: #9F2241;
            color: white;
            margin-top: 12px;
        }

        .boton-cancelar-alerta:hover {
            background: #691C32;
        }

        .mensaje {
            margin-top: 12px;
            text-align: center;
            font-weight: bold;
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

        .sos {
            position: relative;
            isolation: isolate;
            overflow: visible;
            touch-action: none;
            user-select: none;
        }

        .sos-texto {
            position: relative;
            z-index: 2;
            pointer-events: none;
        }

        .sos::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #ef5350;
            transform: scale(0);
            opacity: 0;
            pointer-events: none;
            z-index: 1;
        }

        .sos-presionando::before {
            opacity: 1;
            animation: llenarBotonSOS 3s linear forwards;
        }

        .sos-completado::before {
            opacity: 1;
            transform: scale(1);
        }

        .sos-completado::after {
            content: '';
            position: absolute;
            inset: 0;
            border: 4px solid #ef5350;
            border-radius: 50%;
            pointer-events: none;
            animation: expansionSOS 0.8s ease-out forwards;
        }

        .sos-anillo {
            position: absolute;
            width: 220px;
            height: 220px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
            pointer-events: none;
            overflow: visible;
            z-index: 0;
        }

        .sos-anillo-fondo {
            fill: none;
            stroke: #F2CAD0;
            stroke-width: 7;
            opacity: 0.6;
        }

        .sos-anillo-avance {
            fill: none;
            stroke: #D9334A;
            stroke-width: 8;
            stroke-linecap: round;
            stroke-dasharray: 628.32;
            stroke-dashoffset: 628.32;
            opacity: 0;
        }

        .sos-presionando .sos-anillo-avance {
            opacity: 1;
            animation: progresoAnilloSOS 3s linear forwards;
        }

        .sos-completado .sos-anillo-avance {
            opacity: 1;
            stroke-dashoffset: 0;
        }

        @keyframes progresoAnilloSOS {
            from {
                stroke-dashoffset: 628.32;
            }

            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes llenarBotonSOS {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        @keyframes expansionSOS {
            0% {
                opacity: 0.9;
                transform: scale(1);
            }

            100% {
                opacity: 0;
                transform: scale(1.4);
            }
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

        /* Pantalla independiente de la cuenta ciudadana */
        #panelCuentaCiudadano {
            display: none;
        }

        #pantallaContenido.modo-cuenta> :not(#panelCuentaCiudadano) {
            display: none !important;
        }

        #pantallaContenido.modo-cuenta #panelCuentaCiudadano {
            display: block;
            margin-top: 0;
        }

        .acceso-historial-ciudadano {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 16px;
            margin: 18px 0;
            background: white;
            border: 1px solid #dddddd;
            border-radius: 10px;
            color: #691C32;
            text-align: left;
            cursor: pointer;
        }

        .acceso-historial-ciudadano span:first-child {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .acceso-historial-ciudadano small {
            color: #666666;
            font-size: 12px;
        }

        .acceso-historial-ciudadano span:last-child {
            font-size: 26px;
            font-weight: bold;
        }

        .tarjeta-historial {
            background: #FFFFFF;
            border: 1px solid #DDDDDD;
            border-left: 4px solid #691C32;
            border-radius: 10px;
            padding: 14px;
            margin-top: 12px;
        }

        .tarjeta-historial p {
            margin: 8px 0;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .tarjeta-historial strong {
            color: #691C32;
        }
    </style>

</head>


<body>

    <div class="contenedor contenedor-app-movil">

        <div class="app app-movil">

            <!-- Barra superior de la aplicación -->
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

            <!-- Contenido principal de la aplicación -->
            <main class="app-contenido" id="pantallaContenido">

                <h2 id="saludo">
                    Hola
                </h2>


                <div class="campo" id="campoTipoAlerta" style="display: none;">

                    <label>
                        Tipo de alerta
                    </label>

                    <select id="tipoAlerta">

                        <option value="">
                            Cargando tipos...
                        </option>

                    </select>

                </div>


                <div id="mapa"></div>


                <div class="ubicacion" id="textoUbicacion">
                    📍 Obteniendo ubicación...
                </div>

                <button
                    type="button"
                    class="boton boton-secundario"
                    onclick="corregirUbicacionManual()">
                    Corregir ubicación en el mapa
                </button>


                <div class="sos-contenedor">

                    <button
                        type="button"
                        class="sos"
                        id="btnSOS">

                        <svg
                            class="sos-anillo"
                            viewBox="0 0 220 220"
                            aria-hidden="true">

                            <circle
                                class="sos-anillo-fondo"
                                cx="110"
                                cy="110"
                                r="100">
                            </circle>

                            <circle
                                class="sos-anillo-avance"
                                cx="110"
                                cy="110"
                                r="100">
                            </circle>

                        </svg>

                        <span class="sos-texto">SOS</span>

                    </button>

                    <div
                        class="instruccion"
                        id="instruccionSOS">
                        Mantén presionado SOS durante 3 segundos
                    </div>

                </div>


                <div
                    id="mensajeAlerta"
                    class="mensaje">
                </div>


                <div
                    id="panelEstado"
                    class="estado-alerta"
                    style="display: none;">

                    <h3>
                        Estado de mi alerta
                    </h3>

                    <p>
                        <strong>Folio:</strong>
                        <span id="folio">
                            -
                        </span>
                    </p>

                    <p>
                        <strong>Tipo:</strong>
                        <span id="tipoActual">
                            -
                        </span>
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        <span id="estado">
                            -
                        </span>
                    </p>

                    <p>
                        <strong>Policía asignado:</strong>
                        <span id="policia">
                            Sin asignar
                        </span>
                    </p>

                    <p id="filaDistancia" style="display: none;">
                        <strong>Distancia aproximada:</strong>
                        <span id="distanciaPolicia">
                            -
                        </span>
                    </p>

                    <p id="filaTiempo" style="display: none;">
                        <strong>Tiempo aproximado:</strong>
                        <span id="tiempoPolicia">
                            -
                        </span>
                    </p>

                    <button
                        type="button"
                        id="btnCancelarAlerta"
                        class="boton boton-cancelar-alerta"
                        onclick="cancelarAlerta()"
                        style="display: none;">
                        Cancelar alerta
                    </button>

                </div>

                <section
                    id="panelCuentaCiudadano"
                    class="estado-alerta">

                    <h2>Mi cuenta</h2>

                    <p>
                        <strong>Nombre:</strong>
                        <span id="nombreCuentaCiudadano">
                            Ciudadano
                        </span>
                    </p>

                    <p>
                        <strong>Rol:</strong>
                        Ciudadano
                    </p>

                    <button
                        type="button"
                        id="btnHistorialCiudadano"
                        class="acceso-historial-ciudadano"
                        onclick="abrirHistorialCiudadano()">

                        <span>
                            <strong>Historial de alertas</strong>
                            <small>Consulta tus emergencias anteriores</small>
                        </span>

                        <span aria-hidden="true">›</span>

                    </button>

                    <button
                        type="button"
                        class="boton boton-cerrar-sesion"
                        onclick="cerrarSesion()">
                        Cerrar sesión
                    </button>

                    <div
                        id="vistaHistorialCiudadano"
                        style="display: none;">

                        <button
                            type="button"
                            class="boton boton-secundario"
                            onclick="volverCuentaCiudadano()">
                            ← Volver a mi cuenta
                        </button>

                        <h2>Historial de alertas</h2>

                        <div id="listaHistorialCiudadano">
                            Aquí se mostrarán tus alertas anteriores.
                        </div>

                    </div>

                </section>

            </main>

            <!-- Barra de navegación inferior -->
            <nav
                id="navCiudadano"
                class="app-nav-inferior"
                aria-label="Navegación principal">

                <button
                    type="button"
                    class="activo"
                    data-seccion="inicio"
                    onclick="navegarCiudadano('inicio')">
                    Inicio
                </button>

                <button
                    type="button"
                    data-seccion="sos"
                    onclick="navegarCiudadano('sos')">
                    SOS
                </button>

                <button
                    type="button"
                    data-seccion="cuenta"
                    onclick="navegarCiudadano('cuenta')">
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

        const token =
            sessionStorage.getItem(
                'alerta_ixta_token'
            );


        let mapa = null;

        let marcadorCiudadano = null;

        let marcadorPolicia = null;

        let latitudActual = null;

        let longitudActual = null;

        let precisionActual = null;

        let idAlertaActual = null;

        let intervaloEstado = null;

        let rutaPoliciaCiudadano = null;

        let intervaloUbicacionCiudadano = null;

        let ubicacionManualCiudadano = false;

        let temporizadorSOS = null;
        let temporizadorAnimacionSOS = null;

        let sosPresionado = false;

        let sosProcesando = false;


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


        // Si no existe token, regresar al login.
        if (!token) {
            window.location.href = '/';
        }


        document.addEventListener(
            'DOMContentLoaded',
            async function() {

                iniciarMapa();

                await cargarUsuario();

                await cargarTiposAlerta();

                obtenerUbicacion();

                await cargarMisAlertas();

            }
        );

        // Marcar la pestaña seleccionada.
        function marcarPestanaCiudadano(seccion) {

            document.querySelectorAll('#navCiudadano button')
                .forEach(function(boton) {

                    boton.classList.toggle(
                        'activo',
                        boton.dataset.seccion === seccion
                    );

                });
        }

        // Consultar las alertas anteriores del ciudadano.
        async function cargarHistorialCiudadano() {

            const lista = document.getElementById(
                'listaHistorialCiudadano'
            );

            lista.textContent = 'Cargando historial...';

            try {

                const respuesta = await fetch(
                    API + '/mis-alertas', {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!respuesta.ok) {
                    lista.textContent =
                        'No fue posible consultar el historial.';
                    return;
                }

                const datos = await respuesta.json();

                const alertas = Array.isArray(datos) ?
                    datos :
                    (datos.alertas ?? []);

                if (!Array.isArray(alertas)) {
                    lista.textContent =
                        'No se pudo interpretar el historial.';
                    return;
                }

                // Mostrar solamente alertas terminadas o canceladas.
                const historial = alertas.filter(function(alerta) {

                    return (
                        alerta.estado === 'CERRADA' ||
                        alerta.estado === 'CANCELADA'
                    );

                });

                lista.replaceChildren();

                if (historial.length === 0) {

                    lista.textContent =
                        'Todavía no tienes alertas finalizadas.';

                    return;
                }

                historial.forEach(function(alerta) {

                    const tarjeta = document.createElement('div');

                    tarjeta.className = 'tarjeta-historial';

                    const tipo = typeof alerta.tipo === 'object' ?
                        alerta.tipo?.nombre :
                        alerta.tipo;

                    const policia = typeof alerta.policia === 'object' ?
                        alerta.policia?.nombre :
                        alerta.policia;

                    // Obtener la fecha real de la alerta.
                    const fechaOriginal =
                        alerta.fecha_hora ??
                        alerta.fecha_creacion ??
                        alerta.created_at ??
                        alerta.fecha ??
                        null;

                    let fecha = 'No disponible';

                    if (fechaOriginal) {

                        const fechaObjeto = new Date(fechaOriginal);

                        if (!Number.isNaN(fechaObjeto.getTime())) {

                            // Mostrar la fecha en horario de Ixtapaluca.
                            fecha = new Intl.DateTimeFormat('es-MX', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                                timeZone: 'America/Mexico_City'
                            }).format(fechaObjeto) + ' h';

                        } else {

                            fecha = String(fechaOriginal);

                        }
                    }

                    const campos = [
                        ['Folio', alerta.folio ?? 'No disponible'],
                        ['Fecha', fecha],
                        ['Tipo', tipo ?? 'No disponible'],
                        ['Estado', alerta.estado],
                        ['Policía', policia ?? 'Sin asignar']
                    ];

                    campos.forEach(function(campo) {

                        const parrafo = document.createElement('p');

                        const etiqueta = document.createElement('strong');

                        etiqueta.textContent = campo[0] + ': ';

                        parrafo.appendChild(etiqueta);

                        parrafo.appendChild(
                            document.createTextNode(
                                String(campo[1])
                            )
                        );

                        tarjeta.appendChild(parrafo);

                    });

                    lista.appendChild(tarjeta);

                });

            } catch (error) {

                console.error(
                    'Error consultando historial:',
                    error
                );

                lista.textContent =
                    'No fue posible conectar con el servidor.';

            }
        }

        // Abrir el historial dentro de Cuenta.
        function abrirHistorialCiudadano() {

            const cuenta = document.getElementById(
                'panelCuentaCiudadano'
            );

            const historial = document.getElementById(
                'vistaHistorialCiudadano'
            );

            Array.from(cuenta.children).forEach(function(elemento) {
                if (elemento !== historial) {
                    elemento.style.display = 'none';
                }
            });

            historial.style.display = 'block';

            // Consultar las alertas guardadas en MySQL.
            cargarHistorialCiudadano();

            document.getElementById('pantallaContenido')
                .scrollTo({
                    top: 0,
                    behavior: 'auto'
                });
        }


        // Regresar del historial a Mi cuenta.
        function volverCuentaCiudadano() {

            const cuenta = document.getElementById(
                'panelCuentaCiudadano'
            );

            const historial = document.getElementById(
                'vistaHistorialCiudadano'
            );

            historial.style.display = 'none';

            Array.from(cuenta.children).forEach(function(elemento) {
                if (elemento !== historial) {
                    elemento.style.removeProperty('display');
                }
            });

        }

        // Navegar entre Inicio, SOS y Cuenta.
        function navegarCiudadano(seccion) {

            const contenido = document.getElementById(
                'pantallaContenido'
            );

            // Mostrar u ocultar la pantalla Cuenta.
            contenido.classList.toggle(
                'modo-cuenta',
                seccion === 'cuenta'
            );

            // Cambiar la pestaña activa.
            marcarPestanaCiudadano(seccion);

            if (seccion === 'inicio' || seccion === 'cuenta') {

                contenido.scrollTo({
                    top: 0,
                    behavior: 'auto'
                });

            }

            if (seccion === 'sos') {

                requestAnimationFrame(function() {

                    document.getElementById('btnSOS')
                        .scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                });

            }

            // Ajustar el mapa al regresar desde Cuenta.
            if (seccion !== 'cuenta' && mapa) {

                requestAnimationFrame(function() {
                    mapa.invalidateSize();
                });

            }
        }


        // Crear mapa.
        function iniciarMapa() {

            mapa = L.map('mapa').setView(
                [19.3132, -98.8843],
                14
            );


            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }
            ).addTo(mapa);

        }


        // Consultar ciudadano autenticado.
        async function cargarUsuario() {

            try {

                const respuesta = await fetch(
                    API + '/usuario', {
                        headers: {
                            'Authorization': 'Bearer ' + token,

                            'Accept': 'application/json'
                        }
                    }
                );


                if (respuesta.status === 401) {

                    sessionStorage.removeItem(
                        'alerta_ixta_token'
                    );

                    window.location.href = '/';

                    return;
                }


                const datos =
                    await respuesta.json();


                const usuario =
                    datos.usuario ??
                    datos.user ??
                    datos;


                if (usuario.name) {

                    document
                        .getElementById('saludo')
                        .textContent =
                        'Hola, ' + usuario.name;

                    document
                        .getElementById('nombreCuentaCiudadano')
                        .textContent = usuario.name;

                }

            } catch (error) {

                console.error(
                    'Error al consultar usuario:',
                    error
                );

            }

        }


        // Consultar tipos desde MySQL mediante la API.
        async function cargarTiposAlerta() {

            const select =
                document.getElementById(
                    'tipoAlerta'
                );


            try {

                const respuesta = await fetch(
                    API + '/tipos-alerta', {
                        headers: {
                            'Authorization': 'Bearer ' + token,

                            'Accept': 'application/json'

                        }
                    }
                );


                const datos =
                    await respuesta.json();


                const tipos =
                    datos.tipos ??
                    datos.tipos_alerta ??
                    datos;


                select.innerHTML =
                    '<option value="">Selecciona una opción</option>';


                if (Array.isArray(tipos)) {

                    tipos.forEach(
                        function(tipo) {

                            if (
                                tipo.activo === false ||
                                tipo.activo === 0
                            ) {
                                return;
                            }


                            const opcion =
                                document.createElement(
                                    'option'
                                );


                            opcion.value =
                                tipo.id_tipo;


                            opcion.textContent =
                                tipo.nombre;


                            select.appendChild(
                                opcion
                            );

                        }
                    );

                }

                // Seleccionar Seguridad automáticamente.
                const opcionSeguridad = Array.from(select.options).find(
                    opcion => opcion.textContent.trim().toLowerCase() === 'seguridad'
                );

                if (opcionSeguridad) {
                    select.value = opcionSeguridad.value;
                }


            } catch (error) {

                select.innerHTML =
                    '<option value="">No se pudieron cargar los tipos</option>';

            }

        }


        // Obtener GPS del ciudadano.
        function obtenerUbicacion() {

            const texto =
                document.getElementById(
                    'textoUbicacion'
                );


            if (!navigator.geolocation) {

                texto.textContent =
                    'Ubicación no disponible';

                return;
            }


            navigator.geolocation.getCurrentPosition(

                function(posicion) {

                    if (
                        ubicacionManualCiudadano
                    ) {
                        return;
                    }

                    latitudActual =
                        posicion.coords.latitude;

                    longitudActual =
                        posicion.coords.longitude;

                    precisionActual =
                        posicion.coords.accuracy;


                    texto.textContent =
                        '📍 Ubicación disponible - precision aproximada: ' + Math.round(precisionActual) + ' metros';


                    actualizarMarcadorCiudadano(
                        latitudActual,
                        longitudActual
                    );

                },


                function(error) {

                    if (error.code === 1) {
                        texto.textContent =
                            'Permiso de ubicación denegado.';
                    } else if (error.code === 2) {
                        texto.textContent =
                            'No se pudo determinar la ubicación.';
                    } else if (error.code === 3) {
                        texto.textContent =
                            'La ubicación tardó demasiado en obtenerse.';
                    } else {
                        texto.textContent =
                            'No se pudo obtener la ubicación.';
                    }

                },


                {
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 5000
                }

            );

        }

        // Permitir corregir manualmente la ubicación durante las pruebas.
        function corregirUbicacionManual() {

            const texto =
                document.getElementById(
                    'textoUbicacion'
                );

            texto.textContent =
                '📍 Haz clic en el mapa sobre tu ubicación correcta.';


            mapa.once(
                'click',
                function(evento) {

                    latitudActual =
                        evento.latlng.lat;

                    longitudActual =
                        evento.latlng.lng;

                    precisionActual =
                        null;

                    ubicacionManualCiudadano =
                        true;


                    actualizarMarcadorCiudadano(
                        latitudActual,
                        longitudActual
                    );

                    //si ya existe un policia en camino recalcula la ruta
                    if (
                        marcadorPolicia
                    ) {
                        calcularRutaCiudadano();
                    }

                    if (idAlertaActual !== null) {
                        enviarUbicacionCiudadano();
                    }


                    texto.textContent =
                        '📍 Ubicación corregida manualmente para esta prueba.';

                }
            );

        }


        // Actualizar marcador del ciudadano.
        function actualizarMarcadorCiudadano(
            latitud,
            longitud
        ) {

            if (!marcadorCiudadano) {

                marcadorCiudadano =
                    L.marker(
                        [
                            latitud,
                            longitud
                        ], {
                            icon: iconoCiudadano
                        }
                    )
                    .addTo(mapa)
                    .bindPopup(
                        'Mi ubicación'
                    );

            } else {

                marcadorCiudadano
                    .setLatLng([
                        latitud,
                        longitud
                    ]);

            }


            mapa.setView(
                [
                    latitud,
                    longitud
                ],
                16
            );

        }

        async function enviarUbicacionCiudadano() {

            if (
                idAlertaActual === null ||
                latitudActual === null ||
                longitudActual === null
            ) {
                return;
            }


            try {

                const respuesta =
                    await fetch(
                        API +
                        '/mis-alertas/' +
                        idAlertaActual +
                        '/ubicacion', {

                            method: 'POST',

                            headers: {

                                'Authorization': 'Bearer ' +
                                    token,

                                'Content-Type': 'application/json',

                                'Accept': 'application/json'

                            },

                            body: JSON.stringify({

                                latitud: latitudActual,

                                longitud: longitudActual,

                                precision_m: precisionActual

                            })

                        }
                    );


                if (
                    respuesta.status ===
                    409
                ) {

                    detenerSeguimientoUbicacionCiudadano();

                    return;

                }


                if (!respuesta.ok) {

                    console.error(
                        'No fue posible actualizar la ubicación del ciudadano.'
                    );

                }


            } catch (error) {

                console.error(
                    'Error enviando ubicación del ciudadano:',
                    error
                );

            }

        }



        function actualizarUbicacionCiudadano() {

            if (
                idAlertaActual === null
            ) {
                return;
            }


            // Si la ubicación fue corregida manualmente, no volvemos a pedir la ubicación de Chrome. Solamente enviamos el punto de prueba actual.
            if (
                ubicacionManualCiudadano
            ) {

                enviarUbicacionCiudadano();

                return;

            }


            if (
                !navigator.geolocation
            ) {
                return;
            }


            navigator.geolocation.getCurrentPosition(

                async function(
                        posicion
                    ) {

                        latitudActual =
                            posicion.coords.latitude;

                        longitudActual =
                            posicion.coords.longitude;

                        precisionActual =
                            posicion.coords.accuracy;


                        actualizarMarcadorCiudadano(
                            latitudActual,
                            longitudActual
                        );


                        await enviarUbicacionCiudadano();

                    },

                    function(
                        error
                    ) {

                        console.error(
                            'No fue posible actualizar la ubicación:',
                            error
                        );

                    },

                    {

                        enableHighAccuracy: true,

                        timeout: 10000,

                        maximumAge: 5000

                    }

            );

        }



        function iniciarSeguimientoUbicacionCiudadano() {

            if (
                intervaloUbicacionCiudadano
            ) {

                clearInterval(
                    intervaloUbicacionCiudadano
                );

            }


            intervaloUbicacionCiudadano =
                setInterval(
                    function() {

                        actualizarUbicacionCiudadano();

                    },
                    5000
                );

        }



        function detenerSeguimientoUbicacionCiudadano() {

            if (
                intervaloUbicacionCiudadano
            ) {

                clearInterval(
                    intervaloUbicacionCiudadano
                );


                intervaloUbicacionCiudadano =
                    null;

            }

        }


        // Crear alerta.

        const btnSOS =
            document.getElementById(
                'btnSOS'
            );


        const instruccionSOS =
            document.getElementById(
                'instruccionSOS'
            );


        function iniciarPresionSOS(evento) {

            evento.preventDefault();


            if (
                sosProcesando ||
                temporizadorSOS !== null
            ) {
                return;
            }

            // Marcar SOS sin mover la pantalla durante la pulsación.
            marcarPestanaCiudadano('sos');

            sosPresionado = true;

            // Detener cualquier animación anterior.
            if (temporizadorAnimacionSOS !== null) {
                clearTimeout(temporizadorAnimacionSOS);
                temporizadorAnimacionSOS = null;
            }

            // Reiniciar el efecto visual.
            btnSOS.classList.remove('sos-completado');

            // Comenzar el llenado del círculo durante 3 segundos.
            btnSOS.classList.add('sos-presionando');

            instruccionSOS.textContent =
                'Sigue presionando...';


            temporizadorSOS =
                setTimeout(
                    async function() {

                            temporizadorSOS =
                                null;


                            if (
                                !sosPresionado ||
                                sosProcesando
                            ) {
                                return;
                            }


                            sosPresionado =
                                false;

                            sosProcesando =
                                true;

                            // El círculo terminó de llenarse.
                            btnSOS.classList.remove('sos-presionando');

                            // Mostrar la onda expansiva.
                            btnSOS.classList.add('sos-completado');

                            // Retirar el efecto después de 0.8 segundos.
                            temporizadorAnimacionSOS = setTimeout(function() {
                                btnSOS.classList.remove('sos-completado');
                                temporizadorAnimacionSOS = null;
                            }, 800);

                            instruccionSOS.textContent =
                                'Enviando alerta...';

                            await crearAlerta();


                            sosProcesando =
                                false;


                            instruccionSOS.textContent =
                                'Mantén presionado SOS durante 3 segundos';

                        },
                        3000
                );
        }


        function cancelarPresionSOS() {

            /*
            Si ya pasaron los 3 segundos y estamos enviando la alerta, ya no cancelamos la acción.
             */
            if (sosProcesando) {
                return;
            }


            sosPresionado =
                false;


            if (
                temporizadorSOS !== null
            ) {

                clearTimeout(
                    temporizadorSOS
                );

                temporizadorSOS =
                    null;
            }

            // Cancelar el llenado si no se completaron los 3 segundos.
            btnSOS.classList.remove('sos-presionando');
            btnSOS.classList.remove('sos-completado');

            instruccionSOS.textContent =
                'Mantén presionado SOS durante 3 segundos';

        }

        // Eventos para detectar cuando se presiona y suelta el botón.
        btnSOS.addEventListener(
            'pointerdown',
            iniciarPresionSOS
        );


        btnSOS.addEventListener(
            'pointerup',
            cancelarPresionSOS
        );


        btnSOS.addEventListener(
            'pointerleave',
            cancelarPresionSOS
        );


        btnSOS.addEventListener(
            'pointercancel',
            cancelarPresionSOS
        );

        async function crearAlerta() {

            const tipo =
                document
                .getElementById(
                    'tipoAlerta'
                )
                .value;


            const mensaje =
                document.getElementById(
                    'mensajeAlerta'
                );


            if (!tipo) {

                mensaje.style.color =
                    '#9F2241';

                mensaje.textContent =
                    'Selecciona un tipo de alerta.';

                return;
            }


            if (
                latitudActual === null ||
                longitudActual === null
            ) {

                mensaje.style.color =
                    '#9F2241';

                mensaje.textContent =
                    'Todavía no se ha obtenido tu ubicación.';

                obtenerUbicacion();

                return;
            }


            mensaje.style.color =
                '#235B4E';

            mensaje.textContent =
                'Enviando alerta...';


            try {

                const respuesta = await fetch(
                    API + '/alertas', {
                        method: 'POST',

                        headers: {
                            'Authorization': 'Bearer ' + token,

                            'Content-Type': 'application/json',

                            'Accept': 'application/json'
                        },

                        body: JSON.stringify({

                            id_tipo: Number(tipo),

                            latitud: latitudActual,

                            longitud: longitudActual,

                            precision_m: precisionActual

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
                        'No fue posible crear la alerta.';

                    return;
                }


                idAlertaActual =
                    datos.alerta.id_alerta;


                mensaje.style.color =
                    '#235B4E';


                mensaje.textContent =
                    'Alerta enviada correctamente.';


                await consultarEstadoAlerta();

                iniciarActualizacionAutomatica();

                iniciarSeguimientoUbicacionCiudadano();


            } catch (error) {

                mensaje.style.color =
                    '#9F2241';

                mensaje.textContent =
                    'No fue posible conectar con el servidor.';

            }

        }

        async function cancelarAlerta() {

            if (!idAlertaActual) {
                return;
            }


            const confirmar =
                window.confirm(
                    '¿Confirmas que deseas cancelar esta alerta?'
                );


            if (!confirmar) {
                return;
            }


            const mensaje =
                document.getElementById(
                    'mensajeAlerta'
                );


            const boton =
                document.getElementById(
                    'btnCancelarAlerta'
                );


            boton.disabled = true;

            boton.textContent =
                'Cancelando...';


            try {

                const respuesta =
                    await fetch(
                        API +
                        '/mis-alertas/' +
                        idAlertaActual +
                        '/cancelar', {

                            method: 'POST',

                            headers: {

                                'Authorization': 'Bearer ' + token,

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
                        'No fue posible cancelar la alerta.';


                    boton.disabled =
                        false;

                    boton.textContent =
                        'Cancelar alerta';

                    return;
                }


                mensaje.style.color =
                    '#235B4E';

                mensaje.textContent =
                    'Alerta cancelada correctamente.';


                document
                    .getElementById(
                        'estado'
                    )
                    .textContent =
                    'CANCELADA';


                boton.style.display =
                    'none';


                /*
                 * Ya no necesitamos seguir
                 * consultando ni enviando ubicación.
                 */
                if (intervaloEstado) {

                    clearInterval(
                        intervaloEstado
                    );

                    intervaloEstado =
                        null;
                }


                detenerSeguimientoUbicacionCiudadano();


                /*
                 * Evitar que se sigan enviando
                 * coordenadas a la alerta cancelada.
                 */
                idAlertaActual =
                    null;


                /*
                 * Si había un policía o ruta
                 * mostrados, eliminarlos.
                 */
                actualizarPoliciaEnMapa({
                    policia: null
                });


            } catch (error) {

                console.error(
                    'Error cancelando alerta:',
                    error
                );


                mensaje.style.color =
                    '#9F2241';

                mensaje.textContent =
                    'No fue posible conectar con el servidor.';


                boton.disabled =
                    false;

                boton.textContent =
                    'Cancelar alerta';
            }
        }

        // Consultar historial del ciudadano.
        async function cargarMisAlertas() {

            try {

                const respuesta = await fetch(
                    API + '/mis-alertas', {
                        headers: {
                            'Authorization': 'Bearer ' + token,

                            'Accept': 'application/json'
                        }
                    }
                );


                if (!respuesta.ok) {
                    return;
                }


                const datos =
                    await respuesta.json();


                const alertas =
                    datos.alertas ??
                    datos;


                if (
                    Array.isArray(alertas) &&
                    alertas.length > 0
                ) {

                    idAlertaActual =
                        alertas[0].id_alerta;


                    await consultarEstadoAlerta();


                    if (
                        alertas[0].estado !==
                        'CERRADA'
                    ) {

                        iniciarActualizacionAutomatica();

                    }

                }


            } catch (error) {

                console.error(
                    'Error al consultar alertas:',
                    error
                );

            }

        }


        // Consultar estado de la alerta actual.
        async function consultarEstadoAlerta() {

            if (!idAlertaActual) {
                return;
            }


            try {

                const respuesta = await fetch(
                    API +
                    '/mis-alertas/' +
                    idAlertaActual +
                    '/estado', {
                        headers: {
                            'Authorization': 'Bearer ' + token,

                            'Accept': 'application/json'
                        }
                    }
                );


                if (!respuesta.ok) {
                    return;
                }


                const datos =
                    await respuesta.json();


                mostrarEstado(
                    datos.alerta
                );


            } catch (error) {

                console.error(
                    'Error al consultar estado:',
                    error
                );

            }

        }


        // Mostrar estado de la alerta.
        function mostrarEstado(alerta) {

            document
                .getElementById(
                    'panelEstado'
                )
                .style.display =
                'block';


            document
                .getElementById(
                    'folio'
                )
                .textContent =
                alerta.folio;


            document
                .getElementById(
                    'tipoActual'
                )
                .textContent =
                alerta.tipo;


            document
                .getElementById(
                    'estado'
                )
                .textContent =
                alerta.estado;

            const btnCancelarAlerta =
                document.getElementById(
                    'btnCancelarAlerta'
                );


            if (
                alerta.estado ===
                'NUEVA'
            ) {

                btnCancelarAlerta
                    .style.display =
                    'block';

                btnCancelarAlerta.disabled =
                    false;

                btnCancelarAlerta.textContent =
                    'Cancelar alerta';

            } else {

                btnCancelarAlerta
                    .style.display =
                    'none';
            }


            if (alerta.policia) {

                document
                    .getElementById(
                        'policia'
                    )
                    .textContent =
                    alerta.policia.nombre;

            } else {

                document
                    .getElementById(
                        'policia'
                    )
                    .textContent =
                    'Sin asignar';

            }


            if (
                !ubicacionManualCiudadano &&
                alerta.ubicacion &&
                alerta.ubicacion.latitud &&
                alerta.ubicacion.longitud
            ) {

                actualizarMarcadorCiudadano(
                    parseFloat(
                        alerta.ubicacion.latitud
                    ),

                    parseFloat(
                        alerta.ubicacion.longitud
                    )
                );

            }


            actualizarPoliciaEnMapa(
                alerta
            );


            if (
                alerta.estado === 'CERRADA' || alerta.estado === 'CANCELADA'
            ) {

                if (intervaloEstado) {

                    clearInterval(
                        intervaloEstado
                    );

                    intervaloEstado = null;

                }

            }

        }


        // Mostrar o mover policía.
        function actualizarPoliciaEnMapa(
            alerta
        ) {

            if (
                alerta.policia &&
                alerta.policia.ubicacion &&
                alerta.policia.ubicacion.latitud &&
                alerta.policia.ubicacion.longitud
            ) {

                const latitud =
                    parseFloat(
                        alerta.policia
                        .ubicacion
                        .latitud
                    );

                const longitud =
                    parseFloat(
                        alerta.policia
                        .ubicacion
                        .longitud
                    );


                // Crear o mover el marcador del policía.
                if (!marcadorPolicia) {

                    marcadorPolicia =
                        L.marker(
                            [
                                latitud,
                                longitud
                            ], {
                                icon: iconoPolicia
                            }
                        )
                        .addTo(mapa)
                        .bindPopup(
                            'Policía en camino'
                        );

                } else {

                    marcadorPolicia
                        .setLatLng([
                            latitud,
                            longitud
                        ]);

                }


                // Calcular la ruta real por calles.
                calcularRutaCiudadano();


            } else {

                // Eliminar policía si ya no debe mostrarse.
                if (marcadorPolicia) {

                    mapa.removeLayer(
                        marcadorPolicia
                    );

                    marcadorPolicia = null;

                }


                // Eliminar ruta.
                if (rutaPoliciaCiudadano) {

                    mapa.removeLayer(
                        rutaPoliciaCiudadano
                    );

                    rutaPoliciaCiudadano = null;

                }

                // Ocultar distancia.
                document
                    .getElementById(
                        'filaDistancia'
                    )
                    .style.display =
                    'none';


                // Ocultar tiempo aproximado.
                document
                    .getElementById(
                        'filaTiempo'
                    )
                    .style.display =
                    'none';

            }

        }


        // Actualizar estado cada 5 segundos.
        function iniciarActualizacionAutomatica() {

            if (intervaloEstado) {

                clearInterval(
                    intervaloEstado
                );

            }


            intervaloEstado =
                setInterval(
                    consultarEstadoAlerta,
                    5000
                );

        }

        async function calcularRutaCiudadano() {

            if (
                !marcadorCiudadano ||
                !marcadorPolicia
            ) {
                return;
            }


            const ciudadano =
                marcadorCiudadano.getLatLng();

            const policia =
                marcadorPolicia.getLatLng();


            try {

                const url =
                    'https://router.project-osrm.org/route/v1/driving/' +
                    policia.lng + ',' +
                    policia.lat + ';' +
                    ciudadano.lng + ',' +
                    ciudadano.lat +
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


                if (rutaPoliciaCiudadano) {

                    mapa.removeLayer(
                        rutaPoliciaCiudadano
                    );

                }


                rutaPoliciaCiudadano =
                    L.polyline(
                        coordenadas, {
                            weight: 5
                        }
                    )
                    .addTo(mapa);


                const distanciaKm =
                    ruta.distance / 1000;


                const minutos =
                    Math.ceil(
                        ruta.duration / 60
                    );


                document
                    .getElementById(
                        'distanciaPolicia'
                    )
                    .textContent =
                    distanciaKm.toFixed(2) +
                    ' km';


                document
                    .getElementById(
                        'tiempoPolicia'
                    )
                    .textContent =
                    minutos +
                    ' min';


                document
                    .getElementById(
                        'filaDistancia'
                    )
                    .style.display =
                    'block';


                document
                    .getElementById(
                        'filaTiempo'
                    )
                    .style.display =
                    'block';


            } catch (error) {

                console.error(
                    'No se pudo calcular la ruta del policía:',
                    error
                );

            }

        }


        // Cerrar sesión.
        async function cerrarSesion() {

            try {

                await fetch(
                    API + '/logout', {
                        method: 'POST',

                        headers: {
                            'Authorization': 'Bearer ' + token,

                            'Accept': 'application/json'
                        }
                    }
                );

            } catch (error) {

                console.error(error);

            }


            sessionStorage.removeItem(
                'alerta_ixta_token'
            );


            window.location.href = '/';

        }
    </script>

</body>

</html>