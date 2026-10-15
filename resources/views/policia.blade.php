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
        href="{{ asset('css/alerta.css') }}">

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

        .btn-finalizar {
            width: 100%;
            margin-top: 15px;
            background: #235B4E;
            color: white;
            font-weight: bold;
        }
    </style>

</head>

<body>

    <div class="contenedor">

        <div class="app">

            <img
                src="{{ asset('images/logoIxtapaluca2025.png') }}"
                alt="Gobierno de Ixtapaluca"
                class="logo-ixtapaluca">

            <div class="logo">
                ALERTA IXTA
            </div>

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

                <h2 id="saludoPolicia">
                    Policía
                </h2>

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

                    <button
                        type="button"
                        id="btnFinalizar"
                        class="boton btn-finalizar"
                        onclick="finalizarAtencion()"
                        style="display: none;">
                        Finalizar atención
                    </button>

                </div>

                <p class="info">
                    Alertas de seguridad pendientes
                </p>


                <div id="listaAlertas">

                    Cargando alertas...

                </div>


                <div id="mapaPolicia"></div>

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

                </div>


                <div
                    id="mensajePolicia"
                    class="mensaje">
                </div>

            </div>

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

        }

        async function cargarServicioActual() {

            try {

                const respuesta = await fetch(
                    API + '/policia/servicio-actual', {
                        headers: {
                            'Authorization': 'Bearer ' +
                                tokenPolicia,

                            'Accept': 'application/json'
                        }
                    }
                );


                const datos =
                    await respuesta.json();


                if (
                    !respuesta.ok ||
                    !datos.servicio
                ) {

                    document
                        .getElementById(
                            'servicioActual'
                        )
                        .classList
                        .add('oculto');

                    return;
                }


                const servicio =
                    datos.servicio;


                idServicioActual =
                    servicio.id_alerta;


                document
                    .getElementById(
                        'servicioActual'
                    )
                    .classList
                    .remove('oculto');


                document
                    .getElementById(
                        'folioServicio'
                    )
                    .textContent =
                    servicio.folio;


                document
                    .getElementById(
                        'tipoServicio'
                    )
                    .textContent =
                    servicio.tipo;


                document
                    .getElementById(
                        'estadoServicio'
                    )
                    .textContent =
                    servicio.estado;

                const btnFinalizar =
                    document.getElementById(
                        'btnFinalizar'
                    );


                if (
                    servicio.estado ===
                    'EN_SITIO'
                ) {

                    btnFinalizar.style.display =
                        'block';

                } else {

                    btnFinalizar.style.display =
                        'none';

                }


                if (
                    servicio.ubicacion &&
                    servicio.ubicacion.latitud &&
                    servicio.ubicacion.longitud
                ) {

                    mostrarDestinoEnMapa(
                        parseFloat(
                            servicio.ubicacion.latitud
                        ),

                        parseFloat(
                            servicio.ubicacion.longitud
                        )
                    );

                }


            } catch (error) {

                console.error(
                    'Error al cargar servicio:',
                    error
                );

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


            mapaPolicia.setView(
                [
                    latitud,
                    longitud
                ],
                16
            );


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


            await cargarAlertas();

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
                            icon: iconoPolicia
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

        function corregirUbicacionPolicia() {

            ubicacionManual = true;


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


                mapaPolicia.fitBounds(
                    rutaPolicia.getBounds(), {
                        padding: [30, 30]
                    }
                );


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

        async function finalizarAtencion() {

            if (!idServicioActual) {
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


            boton.disabled = true;

            boton.textContent =
                'Finalizando...';


            try {

                const respuesta = await fetch(
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

                    boton.disabled = false;

                    boton.textContent =
                        'Finalizar atención';

                    return;
                }


                document
                    .getElementById(
                        'estadoServicio'
                    )
                    .textContent =
                    'CERRADA';


                mensaje.style.color =
                    '#235B4E';


                mensaje.textContent =
                    'Atención finalizada correctamente.';


                boton.style.display =
                    'none';


                if (intervaloUbicacion) {

                    clearInterval(
                        intervaloUbicacion
                    );

                    intervaloUbicacion = null;

                }


                await cargarAlertas();


            } catch (error) {

                mensaje.style.color =
                    '#9F2241';


                mensaje.textContent =
                    'No fue posible conectar con el servidor.';


                boton.disabled = false;


                boton.textContent =
                    'Finalizar atención';

            }

        }
    </script>

</body>

</html>