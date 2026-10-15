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

            <p class="info">
                Alertas de seguridad pendientes
            </p>


            <div id="listaAlertas">

                Cargando alertas...

            </div>


            <div id="mapaPolicia"></div>


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

    let idAlertaSeleccionada = null;


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
                API + '/personal/login',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json'
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

            await cargarAlertas();


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
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution:
                    '© OpenStreetMap'
            }
        )
        .addTo(
            mapaPolicia
        );

    }


    async function cargarAlertas() {

        const contenedor =
            document.getElementById(
                'listaAlertas'
            );


        try {

            const respuesta = await fetch(
                API + '/monitoreo/alertas',
                {
                    headers: {
                        'Authorization':
                            'Bearer ' +
                            tokenPolicia,

                        'Accept':
                            'application/json'
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
                    function (alerta) {

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
                function (alerta) {

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
                        function () {

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
                function (elemento) {

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
            idAlerta,
            {
                headers: {
                    'Authorization':
                        'Bearer ' +
                        tokenPolicia,

                    'Accept':
                        'application/json'
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
            '/aceptar',
            {
                method: 'POST',

                headers: {
                    'Authorization':
                        'Bearer ' +
                        tokenPolicia,

                    'Accept':
                        'application/json'
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

</script>

</body>

</html>