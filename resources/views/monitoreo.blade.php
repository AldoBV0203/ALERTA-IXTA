<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>ALERTA IXTA - Centro de Monitoreo</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/alerta.css') }}">

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>

        body {
            background: #f3f4f6;
        }

        .monitoreo-contenedor {
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
        }

        .encabezado-monitoreo {
            background: #691C32;
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .encabezado-monitoreo img {
            width: 85px;
            background: white;
            border-radius: 8px;
            padding: 5px;
        }

        .paneles {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 20px;
        }

        .panel {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,.10);
        }

        .resumen {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .resumen-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }

        .resumen-numero {
            font-size: 28px;
            font-weight: bold;
            color: #691C32;
        }

        .alerta-item {
            padding: 13px;
            margin-bottom: 10px;
            background: #f4f4f4;
            border-radius: 8px;
            cursor: pointer;
            border-left: 5px solid #999;
        }

        .alerta-item:hover {
            background: #eaeaea;
        }

        .estado-NUEVA {
            border-left-color: #d32f2f;
        }

        .estado-EN_CAMINO {
            border-left-color: #1565c0;
        }

        .estado-EN_SITIO {
            border-left-color: #ef6c00;
        }

        .estado-CERRADA {
            border-left-color: #2e7d32;
        }

        #mapaMonitoreo {
            width: 100%;
            height: 430px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .detalle-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .historial-item {
            border-left: 4px solid #691C32;
            padding: 10px;
            margin-bottom: 8px;
            background: #f6f6f6;
        }

        .oculto {
            display: none;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        button {
            cursor: pointer;
        }

        @media(max-width: 900px) {

            .paneles {
                grid-template-columns: 1fr;
            }

            .resumen {
                grid-template-columns: 1fr 1fr;
            }

        }

    </style>
</head>


<body>

<div class="monitoreo-contenedor">

    <div class="encabezado-monitoreo">

        <img
            src="{{ asset('images/logoIxtapaluca2025.png') }}"
            alt="Ixtapaluca">

        <div>
            <h1 style="margin:0;">
                ALERTA IXTA
            </h1>

            <div>
                Centro de Monitoreo
            </div>
        </div>

    </div>


    <div
        id="loginMonitoreo"
        class="panel"
        style="max-width:450px;margin:auto;">

        <h2>
            Acceso de personal
        </h2>

        <input
            type="text"
            id="usuarioMonitoreo"
            placeholder="Usuario">

        <input
            type="password"
            id="passwordMonitoreo"
            placeholder="Contraseña">

        <button
            class="boton"
            onclick="iniciarSesionMonitoreo()">
            Iniciar sesión
        </button>

        <div id="mensajeLogin"></div>

    </div>


    <div
        id="contenidoMonitoreo"
        class="oculto">

        <div class="resumen">

            <div class="resumen-item">
                <div
                    class="resumen-numero"
                    id="totalAlertas">
                    0
                </div>
                Total
            </div>

            <div class="resumen-item">
                <div
                    class="resumen-numero"
                    id="totalNuevas">
                    0
                </div>
                Nuevas
            </div>

            <div class="resumen-item">
                <div
                    class="resumen-numero"
                    id="totalAtencion">
                    0
                </div>
                En atención
            </div>

            <div class="resumen-item">
                <div
                    class="resumen-numero"
                    id="totalCerradas">
                    0
                </div>
                Cerradas
            </div>

        </div>


        <div class="paneles">

            <div class="panel">

                <h2>
                    Alertas
                </h2>

                <div id="listaMonitoreo">
                    Cargando...
                </div>

            </div>


            <div class="panel">

                <div id="mapaMonitoreo"></div>


                <div
                    id="detalleMonitoreo"
                    class="oculto">

                    <h2>
                        Detalle de alerta
                    </h2>


                    <div class="detalle-grid">

                        <p>
                            <strong>Folio:</strong>
                            <span id="detalleFolio">-</span>
                        </p>

                        <p>
                            <strong>Tipo:</strong>
                            <span id="detalleTipo">-</span>
                        </p>

                        <p>
                            <strong>Estado:</strong>
                            <span id="detalleEstado">-</span>
                        </p>

                        <p>
                            <strong>Prioridad:</strong>
                            <span id="detallePrioridad">-</span>
                        </p>

                        <p>
                            <strong>Policía:</strong>
                            <span id="detallePolicia">
                                Sin asignar
                            </span>
                        </p>

                    </div>


                    <h3>
                        Historial
                    </h3>

                    <div id="historialMonitoreo"></div>

                </div>

            </div>

        </div>

    </div>

</div>


<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
</script>


<script>

    const API = '/api';

    let tokenMonitoreo = null;

    let mapaMonitoreo = null;

    let marcadorMonitoreo = null;


    async function iniciarSesionMonitoreo() {

        const usuario =
            document
                .getElementById(
                    'usuarioMonitoreo'
                )
                .value
                .trim();


        const password =
            document
                .getElementById(
                    'passwordMonitoreo'
                )
                .value;


        const mensaje =
            document.getElementById(
                'mensajeLogin'
            );


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
                personal.rol === 'POLICIA'
            ) {

                mensaje.textContent =
                    'Esta interfaz es para personal de monitoreo.';

                return;
            }


            tokenMonitoreo =
                datos.token;


            document
                .getElementById(
                    'loginMonitoreo'
                )
                .classList
                .add('oculto');


            document
                .getElementById(
                    'contenidoMonitoreo'
                )
                .classList
                .remove('oculto');


            iniciarMapaMonitoreo();

            await cargarAlertasMonitoreo();


        } catch (error) {

            mensaje.textContent =
                'No fue posible conectar con el servidor.';

        }

    }


    function iniciarMapaMonitoreo() {

        mapaMonitoreo =
            L.map(
                'mapaMonitoreo'
            )
            .setView(
                [19.3132, -98.8843],
                13
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
            mapaMonitoreo
        );

    }


    async function cargarAlertasMonitoreo() {

        const respuesta = await fetch(
            API + '/monitoreo/alertas',
            {
                headers: {
                    'Authorization':
                        'Bearer ' +
                        tokenMonitoreo,

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


        const alertas =
            datos.alertas ?? [];


        actualizarResumen(
            alertas
        );


        const lista =
            document.getElementById(
                'listaMonitoreo'
            );


        lista.innerHTML = '';


        alertas.forEach(
            function (alerta) {

                const item =
                    document.createElement(
                        'div'
                    );


                item.className =
                    'alerta-item estado-' +
                    alerta.estado;


                item.innerHTML =
                    '<strong>' +
                    alerta.folio +
                    '</strong><br>' +

                    alerta.tipo +
                    '<br>' +

                    'Estado: ' +
                    alerta.estado;


                item.onclick =
                    function () {

                        cargarDetalleMonitoreo(
                            alerta.id_alerta
                        );

                    };


                lista.appendChild(
                    item
                );

            }
        );

    }


    function actualizarResumen(
        alertas
    ) {

        document
            .getElementById(
                'totalAlertas'
            )
            .textContent =
            alertas.length;


        document
            .getElementById(
                'totalNuevas'
            )
            .textContent =
            alertas.filter(
                a => a.estado === 'NUEVA'
            ).length;


        document
            .getElementById(
                'totalAtencion'
            )
            .textContent =
            alertas.filter(
                a =>
                    a.estado === 'EN_CAMINO' ||
                    a.estado === 'EN_SITIO'
            ).length;


        document
            .getElementById(
                'totalCerradas'
            )
            .textContent =
            alertas.filter(
                a => a.estado === 'CERRADA'
            ).length;

    }


    async function cargarDetalleMonitoreo(
        idAlerta
    ) {

        const respuesta =
            await fetch(
                API +
                '/monitoreo/alertas/' +
                idAlerta,
                {
                    headers: {
                        'Authorization':
                            'Bearer ' +
                            tokenMonitoreo,

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
                'detalleMonitoreo'
            )
            .classList
            .remove('oculto');


        document
            .getElementById(
                'detalleFolio'
            )
            .textContent =
            alerta.folio;


        document
            .getElementById(
                'detalleTipo'
            )
            .textContent =
            alerta.tipo;


        document
            .getElementById(
                'detalleEstado'
            )
            .textContent =
            alerta.estado;


        document
            .getElementById(
                'detallePrioridad'
            )
            .textContent =
            alerta.prioridad;


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


            if (!marcadorMonitoreo) {

                marcadorMonitoreo =
                    L.marker([
                        latitud,
                        longitud
                    ])
                    .addTo(
                        mapaMonitoreo
                    );

            } else {

                marcadorMonitoreo
                    .setLatLng([
                        latitud,
                        longitud
                    ]);

            }


            mapaMonitoreo.setView(
                [
                    latitud,
                    longitud
                ],
                16
            );

        }


        await cargarHistorialMonitoreo(
            idAlerta
        );

    }


    async function cargarHistorialMonitoreo(
        idAlerta
    ) {

        const respuesta =
            await fetch(
                API +
                '/monitoreo/alertas/' +
                idAlerta +
                '/seguimiento',
                {
                    headers: {
                        'Authorization':
                            'Bearer ' +
                            tokenMonitoreo,

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


        const historial =
            datos.historial ?? [];


        const contenedor =
            document.getElementById(
                'historialMonitoreo'
            );


        contenedor.innerHTML = '';


        let policia =
            'Sin asignar';


        historial.forEach(
            function (registro) {

                if (
                    registro.accion ===
                    'ACEPTO_ALERTA'
                ) {

                    policia =
                        registro.operador;

                }


                const item =
                    document.createElement(
                        'div'
                    );


                item.className =
                    'historial-item';


                item.innerHTML =
                    '<strong>' +
                    registro.accion +
                    '</strong><br>' +

                    registro.operador +
                    '<br>' +

                    (
                        registro.observaciones ??
                        ''
                    );


                contenedor.appendChild(
                    item
                );

            }
        );


        document
            .getElementById(
                'detallePolicia'
            )
            .textContent =
            policia;

    }

</script>

</body>

</html>