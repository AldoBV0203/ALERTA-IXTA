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
        href="{{ asset('css/alerta.css') }}">

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

        .mensaje {
            margin-top: 12px;
            text-align: center;
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

            <h2 id="saludo">
                Hola
            </h2>

            <p class="info">
                Selecciona el tipo de alerta y solicita ayuda.
            </p>


            <div class="campo">

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
                    SOS
                </button>

                <div class="instruccion">
                    Presiona para solicitar ayuda
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

            </div>


            <button
                type="button"
                class="boton boton-secundario"
                onclick="cargarMisAlertas()">
                Actualizar estado
            </button>


            <button
                type="button"
                class="boton boton-cerrar-sesion"
                onclick="cerrarSesion()">
                Cerrar sesión
            </button>

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


                    actualizarMarcadorCiudadano(
                        latitudActual,
                        longitudActual
                    );


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
                    L.marker([
                        latitud,
                        longitud
                    ])
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


        // Crear alerta.
        document
            .getElementById('btnSOS')
            .addEventListener(
                'click',
                crearAlerta
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


            } catch (error) {

                mensaje.style.color =
                    '#9F2241';

                mensaje.textContent =
                    'No fue posible conectar con el servidor.';

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
                alerta.estado ===
                'CERRADA'
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


                if (!marcadorPolicia) {

                    marcadorPolicia =
                        L.marker([
                            latitud,
                            longitud
                        ])
                        .addTo(mapa)
                        .bindPopup(
                            'Policía asignado'
                        );

                } else {

                    marcadorPolicia
                        .setLatLng([
                            latitud,
                            longitud
                        ]);

                }


            } else {

                if (marcadorPolicia) {

                    mapa.removeLayer(
                        marcadorPolicia
                    );

                    marcadorPolicia = null;

                }

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