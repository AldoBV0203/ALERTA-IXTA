<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        ALERTA IXTA | Centro de Monitoreo
    </title>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        :root {
            --vino: #691C32;
            --vino-claro: #9F2241;
            --verde: #235B4E;
            --verde-oscuro: #10312B;
            --dorado: #BC955C;
            --beige: #DDC9A3;

            --fondo: #F4F6F8;
            --blanco: #FFFFFF;
            --gris-borde: #E3E7EB;
            --gris-texto: #5B6470;
            --texto: #252A30;

            --rojo: #C62828;
            --azul: #1565C0;
            --ambar: #EF6C00;
            --verde-estado: #2E7D32;
        }


        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
            background: var(--fondo);
            color: var(--texto);
        }


        button,
        input,
        select {
            font-family: inherit;
        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN
        |--------------------------------------------------------------------------
        */

        #pantallaLogin {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(135deg,
                    var(--verde-oscuro),
                    var(--vino));
        }


        .login-caja {
            width: 430px;
            max-width: calc(100% - 40px);

            background: white;
            border-radius: 10px;

            box-shadow:
                0 20px 50px rgba(0, 0, 0, .25);

            overflow: hidden;
        }


        .login-encabezado {
            padding: 28px;
            text-align: center;

            border-bottom:
                1px solid var(--gris-borde);
        }


        .login-encabezado img {
            width: 125px;
            margin-bottom: 14px;
        }


        .login-encabezado h1 {
            margin: 0;

            font-size: 24px;
            color: var(--vino);
        }


        .login-encabezado p {
            margin:
                6px 0 0 0;

            color: var(--gris-texto);
        }


        .login-formulario {
            padding: 28px;
        }


        .campo {
            margin-bottom: 18px;
        }


        .campo label {
            display: block;

            margin-bottom: 7px;

            font-size: 14px;
            font-weight: bold;
        }


        .campo input {
            width: 100%;

            padding:
                12px 14px;

            border:
                1px solid #CCD2D8;

            border-radius: 6px;

            font-size: 15px;
        }


        .campo input:focus {
            outline: none;

            border-color:
                var(--vino-claro);
        }


        .boton-principal {
            width: 100%;

            padding: 13px;

            border: none;
            border-radius: 6px;

            background:
                var(--vino);

            color: white;

            font-size: 15px;
            font-weight: bold;

            cursor: pointer;
        }


        .boton-principal:hover {
            background:
                var(--vino-claro);
        }


        #mensajeLogin {
            min-height: 20px;

            margin-top: 14px;

            text-align: center;

            font-size: 14px;
            color: var(--rojo);
        }


        /*
        |--------------------------------------------------------------------------
        | SISTEMA
        |--------------------------------------------------------------------------
        */

        #sistemaMonitoreo {
            display: none;
            min-height: 100vh;
        }


        .barra-superior {
            height: 72px;

            background: white;

            border-bottom:
                1px solid var(--gris-borde);

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding:
                0 28px;

            position: fixed;
            top: 0;
            left: 190px;
            right: 0;

            z-index: 1000;
        }


        .barra-superior h1 {
            margin: 0;

            font-size: 20px;
            font-weight: 600;

            color: var(--vino);
        }


        .barra-superior-derecha {
            display: flex;
            align-items: center;
            gap: 18px;

            font-size: 14px;
        }


        .estado-sistema {
            display: flex;
            align-items: center;
            gap: 7px;

            color: var(--verde);
            font-weight: bold;
        }


        .punto-online {
            width: 9px;
            height: 9px;

            border-radius: 50%;

            background:
                var(--verde-estado);
        }


        .btn-salir {
            padding:
                8px 14px;

            border:
                1px solid var(--gris-borde);

            border-radius: 5px;

            background: white;

            color: var(--texto);

            cursor: pointer;
        }


        .btn-salir:hover {
            background:
                #F0F2F4;
        }


        /*
        |--------------------------------------------------------------------------
        | BARRA LATERAL
        |--------------------------------------------------------------------------
        */

        .barra-lateral {
            width: 190px;

            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;

            background: var(--vino);

            color: white;

            z-index: 1100;
        }


        .marca {
            height: 165px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            padding: 15px;

            text-align: center;

            border-bottom:
                1px solid rgba(255, 255, 255, .18);
        }


        .marca img {
            width: 72px;

            background: white;

            padding: 4px;

            border-radius: 5px;

            margin-bottom: 10px;
        }


        .marca-texto strong {
            display: block;

            font-size: 16px;

            letter-spacing: .3px;
        }


        .marca-texto span {
            display: block;

            margin-top: 4px;

            font-size: 10px;

            letter-spacing: .8px;

            color:
                rgba(255, 255, 255, .75);
        }


        .menu-titulo {
            padding:
                22px 20px 8px 20px;

            font-size: 11px;

            color:
                rgba(255, 255, 255, .55);

            text-transform: uppercase;

            letter-spacing: 1px;
        }


        .menu-opcion {
            padding:
                13px 20px;

            border-left:
                4px solid transparent;

            font-size: 15px;

            cursor: default;

            color:
                rgba(255, 255, 255, .85);
        }


        .menu-opcion.activa {
            background: var(--vino-claro);

            border-left-color:
                var(--dorado);

            color: white;

            font-weight: bold;
        }


        .menu-opcion-futura {
            opacity: .52;
        }


        /*
        |--------------------------------------------------------------------------
        | CONTENIDO
        |--------------------------------------------------------------------------
        */

        .contenido {
            margin-left: 190px;

            padding:
                96px 28px 35px 28px;
        }


        .titulo-pagina {
            margin-bottom: 22px;
        }


        .titulo-pagina h2 {
            margin: 0;

            font-size: 24px;
        }


        .titulo-pagina p {
            margin:
                6px 0 0 0;

            color:
                var(--gris-texto);

            font-size: 14px;
        }


        /*
        |--------------------------------------------------------------------------
        | INDICADORES
        |--------------------------------------------------------------------------
        */

        .indicadores {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 16px;

            margin-bottom: 20px;
        }


        .indicador {
            background: white;

            border:
                1px solid var(--gris-borde);

            border-radius: 8px;

            padding: 19px;

            min-height: 105px;

            position: relative;
        }


        .indicador-label {
            color:
                var(--gris-texto);

            font-size: 13px;

            margin-bottom: 12px;
        }


        .indicador-numero {
            font-size: 29px;

            font-weight: bold;
        }


        .indicador-linea {
            position: absolute;

            left: 0;
            top: 0;

            bottom: 0;

            width: 5px;

            border-radius:
                8px 0 0 8px;
        }


        .linea-total {
            background:
                var(--vino);
        }


        .linea-nueva {
            background:
                var(--rojo);
        }


        .linea-atencion {
            background:
                var(--azul);
        }


        .linea-cerrada {
            background:
                var(--verde-estado);
        }


        /*
        |--------------------------------------------------------------------------
        | MAPA
        |--------------------------------------------------------------------------
        */

        .panel {
            background: white;

            border:
                1px solid var(--gris-borde);

            border-radius: 8px;
        }


        .panel-encabezado {
            min-height: 58px;

            padding:
                15px 18px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            border-bottom:
                1px solid var(--gris-borde);
        }


        .panel-encabezado h3 {
            margin: 0;

            font-size: 16px;
        }


        .panel-encabezado span {
            color:
                var(--gris-texto);

            font-size: 12px;
        }


        .mapa-panel {
            margin-bottom: 20px;
        }


        #mapaMonitoreo {
            width: 100%;
            height: 460px;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLA
        |--------------------------------------------------------------------------
        */

        .seccion-inferior {
            display: grid;

            grid-template-columns:
                minmax(0, 1.5fr) minmax(330px, .7fr);

            gap: 20px;
        }


        .tabla-contenedor {
            overflow-x: auto;
        }


        table {
            width: 100%;

            border-collapse: collapse;

            font-size: 13px;
        }


        th {
            background:
                #F8F9FA;

            color:
                var(--gris-texto);

            text-align: left;

            padding:
                12px 14px;

            border-bottom:
                1px solid var(--gris-borde);

            font-size: 12px;

            text-transform: uppercase;
        }


        td {
            padding:
                13px 14px;

            border-bottom:
                1px solid var(--gris-borde);
        }


        tbody tr {
            cursor: pointer;
        }


        tbody tr:hover {
            background:
                #F8F9FA;
        }


        .estado {
            display: inline-block;

            padding:
                5px 8px;

            border-radius: 4px;

            font-size: 11px;
            font-weight: bold;
        }


        .estado-NUEVA {
            color:
                #9B1C1C;

            background:
                #FDE8E8;
        }


        .estado-EN_CAMINO {
            color:
                #174EA6;

            background:
                #E8F0FE;
        }


        .estado-EN_SITIO {
            color:
                #A14800;

            background:
                #FFF0D8;
        }


        .estado-CERRADA {
            color:
                #1B5E20;

            background:
                #E7F4E8;
        }


        /*
        |--------------------------------------------------------------------------
        | DETALLE
        |--------------------------------------------------------------------------
        */

        .detalle-contenido {
            padding: 18px;
        }


        .sin-seleccion {
            color:
                var(--gris-texto);

            text-align: center;

            padding:
                45px 20px;
        }


        .dato {
            margin-bottom: 15px;
        }


        .dato-etiqueta {
            display: block;

            margin-bottom: 4px;

            color:
                var(--gris-texto);

            font-size: 11px;

            text-transform: uppercase;
        }


        .dato-valor {
            font-size: 14px;

            font-weight: bold;
        }


        .historial {
            margin-top: 22px;

            border-top:
                1px solid var(--gris-borde);

            padding-top: 17px;
        }


        .historial h4 {
            margin:
                0 0 14px 0;
        }


        .historial-item {
            padding:
                10px 0 10px 13px;

            margin-bottom: 4px;

            border-left:
                3px solid var(--vino-claro);

            font-size: 12px;
        }


        .historial-item strong {
            display: block;

            margin-bottom: 3px;
        }


        .historial-item span {
            color:
                var(--gris-texto);
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1050px) {

            .indicadores {
                grid-template-columns:
                    1fr 1fr;
            }


            .seccion-inferior {
                grid-template-columns:
                    1fr;
            }

        }


        @media (max-width: 750px) {

            .barra-lateral {
                display: none;
            }


            .barra-superior {
                left: 0;
            }


            .contenido {
                margin-left: 0;
            }


            .indicadores {
                grid-template-columns:
                    1fr;
            }

        }

        .leaflet-interactive:focus {
            outline: none;
        }

        .marcador-policia-mapa {
            background: transparent;
            border: none;
        }

        .policia-circulo {
            width: 34px;
            height: 34px;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            color: white;

            font-size: 14px;
            font-weight: bold;

            border: 3px solid white;

            box-shadow:
                0 2px 7px rgba(0, 0, 0, .35);
        }

        .policia-disponible {
            background: #00897B;
        }

        .policia-servicio {
            background: #0D47A1;
        }

        .policia-desactualizado {
            background: #757575;
        }
    </style>
</head>


<body>


    <!-- =========================================================
     LOGIN DEL CENTRO DE MONITOREO
========================================================= -->

    <div id="pantallaLogin">

        <div class="login-caja">

            <div class="login-encabezado">

                <img
                    src="{{ asset('images/logoIxtapaluca2025.png') }}"
                    alt="Gobierno de Ixtapaluca">

                <h1>
                    ALERTA IXTA
                </h1>

                <p>
                    Centro de Monitoreo
                </p>

            </div>


            <div class="login-formulario">

                <div class="campo">

                    <label for="usuarioMonitoreo">
                        Usuario
                    </label>

                    <input
                        type="text"
                        id="usuarioMonitoreo"
                        autocomplete="username">

                </div>


                <div class="campo">

                    <label for="passwordMonitoreo">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        id="passwordMonitoreo"
                        autocomplete="current-password">

                </div>


                <button
                    type="button"
                    id="btnLoginMonitoreo"
                    class="boton-principal"
                    onclick="iniciarSesionMonitoreo()">

                    Iniciar sesión

                </button>


                <div id="mensajeLogin"></div>

            </div>

        </div>

    </div>



    <!-- =========================================================
     SISTEMA DE MONITOREO
========================================================= -->

    <div id="sistemaMonitoreo">


        <!-- BARRA LATERAL -->

        <aside class="barra-lateral">

            <div class="marca">

                <img
                    src="{{ asset('images/logoIxtapaluca2025.png') }}"
                    alt="Ixtapaluca">

                <div class="marca-texto">

                    <strong>
                        ALERTA IXTA
                    </strong>

                    <span>
                        SISTEMA MUNICIPAL
                    </span>

                </div>

            </div>


            <div class="menu-titulo">
                Operación
            </div>


            <div class="menu-opcion activa">
                Panel general
            </div>


            <div class="menu-opcion">
                Alertas
            </div>


            <div
                class="
                menu-opcion
                menu-opcion-futura">

                Policías

            </div>


            <div class="menu-titulo">
                Consulta
            </div>


            <div
                class="
                menu-opcion
                menu-opcion-futura">

                Historial

            </div>


            <div
                class="
                menu-opcion
                menu-opcion-futura">

                Reportes

            </div>


            <div
                class="
                menu-opcion
                menu-opcion-futura">

                Auditoría

            </div>

        </aside>



        <!-- BARRA SUPERIOR -->

        <header class="barra-superior">

            <h1>
                Centro de Monitoreo
            </h1>


            <div class="barra-superior-derecha">

                <div class="estado-sistema">

                    <span class="punto-online"></span>

                    Sistema conectado

                </div>


                <span id="nombrePersonal">
                    Personal autorizado
                </span>


                <button
                    type="button"
                    class="btn-salir"
                    onclick="cerrarSesionMonitoreo()">

                    Cerrar sesión

                </button>

            </div>

        </header>



        <!-- CONTENIDO -->

        <main class="contenido">


            <div class="titulo-pagina">

                <h2>
                    Panel general de operaciones
                </h2>

                <p>
                    Supervisión y seguimiento de alertas ciudadanas en tiempo real.
                </p>

            </div>



            <!-- INDICADORES -->

            <section class="indicadores">


                <div class="indicador">

                    <div
                        class="
                        indicador-linea
                        linea-total">
                    </div>

                    <div class="indicador-label">
                        Total de alertas
                    </div>

                    <div
                        class="indicador-numero"
                        id="totalAlertas">

                        0

                    </div>

                </div>



                <div class="indicador">

                    <div
                        class="
                        indicador-linea
                        linea-nueva">
                    </div>

                    <div class="indicador-label">
                        Alertas nuevas
                    </div>

                    <div
                        class="indicador-numero"
                        id="totalNuevas">

                        0

                    </div>

                </div>



                <div class="indicador">

                    <div
                        class="
                        indicador-linea
                        linea-atencion">
                    </div>

                    <div class="indicador-label">
                        En atención
                    </div>

                    <div
                        class="indicador-numero"
                        id="totalAtencion">

                        0

                    </div>

                </div>



                <div class="indicador">

                    <div
                        class="
                        indicador-linea
                        linea-cerrada">
                    </div>

                    <div class="indicador-label">
                        Alertas cerradas
                    </div>

                    <div
                        class="indicador-numero"
                        id="totalCerradas">

                        0

                    </div>

                </div>


            </section>



            <!-- MAPA -->

            <section class="panel mapa-panel">

                <div class="panel-encabezado">

                    <h3>
                        Mapa general de operaciones
                    </h3>

                    <span id="ultimaActualizacion">
                        Sin actualizar
                    </span>

                </div>

                <div id="mapaMonitoreo"></div>

            </section>



            <!-- PARTE INFERIOR -->

            <section class="seccion-inferior">


                <!-- TABLA -->

                <div class="panel">

                    <div class="panel-encabezado">

                        <h3>
                            Alertas registradas
                        </h3>

                        <span>
                            Seleccione una alerta para consultar su detalle
                        </span>

                    </div>


                    <div class="tabla-contenedor">

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        Folio
                                    </th>

                                    <th>
                                        Tipo
                                    </th>

                                    <th>
                                        Estado
                                    </th>

                                    <th>
                                        Prioridad
                                    </th>

                                </tr>

                            </thead>


                            <tbody id="tablaAlertas">

                                <tr>

                                    <td colspan="4">
                                        Cargando información...
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>



                <!-- DETALLE -->

                <div class="panel">

                    <div class="panel-encabezado">

                        <h3>
                            Detalle de la alerta
                        </h3>

                    </div>


                    <div
                        class="detalle-contenido"
                        id="detalleAlerta">


                        <div class="sin-seleccion">

                            Seleccione una alerta de la tabla.

                        </div>


                    </div>

                </div>


            </section>


        </main>

    </div>



    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>


    <script>
        const API =
            '/api';


        let tokenMonitoreo =
            sessionStorage.getItem(
                'alerta_ixta_token_monitoreo'
            );


        let mapaMonitoreo =
            null;


        let marcadorAlerta =
            null;

        let marcadoresAlertasActivas = [];

        let limiteIxtapaluca =
            null;

        let marcadoresPolicias = [];


        /*
        |--------------------------------------------------------------------------
        | INICIAR SESIÓN
        |--------------------------------------------------------------------------
        */

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


            mensaje.textContent =
                '';

            const boton =
                document.getElementById(
                    'btnLoginMonitoreo'
                );


            boton.disabled =
                true;


            boton.textContent =
                'Iniciando sesión...';


            boton.style.opacity =
                '.75';


            try {

                const respuesta =
                    await fetch(
                        API +
                        '/personal/login', {

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
                    personal.rol ===
                    'POLICIA'
                ) {

                    mensaje.textContent =
                        'Esta cuenta corresponde a personal policial.';

                    return;

                }


                if (
                    personal.rol ===
                    'PENDIENTE'
                ) {

                    mensaje.textContent =
                        'La cuenta todavía no está autorizada.';

                    return;

                }


                tokenMonitoreo =
                    datos.token;


                sessionStorage.setItem(
                    'alerta_ixta_token_monitoreo',
                    tokenMonitoreo
                );


                if (
                    personal.nombre
                ) {

                    sessionStorage.setItem(
                        'alerta_ixta_nombre_monitoreo',
                        personal.nombre
                    );

                }


                mostrarSistema();


            } catch (error) {

                mensaje.textContent =
                    'No fue posible conectar con el servidor.';

            } finally {

                boton.disabled =
                    false;


                boton.textContent =
                    'Iniciar sesión';


                boton.style.opacity =
                    '1';

            }

        }



        /*
        |--------------------------------------------------------------------------
        | MOSTRAR SISTEMA
        |--------------------------------------------------------------------------
        */

        function mostrarSistema() {

            document
                .getElementById(
                    'pantallaLogin'
                )
                .style.display =
                'none';


            document
                .getElementById(
                    'sistemaMonitoreo'
                )
                .style.display =
                'block';


            const nombre =
                sessionStorage.getItem(
                    'alerta_ixta_nombre_monitoreo'
                );


            if (nombre) {

                document
                    .getElementById(
                        'nombrePersonal'
                    )
                    .textContent =
                    nombre;

            }


            if (!mapaMonitoreo) {

                iniciarMapa();

            }


            cargarAlertas();

        }



        /*
        |--------------------------------------------------------------------------
        | MAPA
        |--------------------------------------------------------------------------
        */

        function iniciarMapa() {

            mapaMonitoreo =
                L.map(
                    'mapaMonitoreo'
                )
                .setView(
                    [
                        19.3132,
                        -98.8843
                    ],
                    12
                );


            L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {

                        maxZoom: 19,

                        attribution: '© OpenStreetMap'

                    }
                )
                .addTo(
                    mapaMonitoreo
                );


            cargarLimiteIxtapaluca();

        }

        async function cargarLimiteIxtapaluca() {

            try {

                const respuesta =
                    await fetch(
                        '/data/ixtapaluca.geojson'
                    );


                if (!respuesta.ok) {

                    console.error(
                        'No fue posible cargar el límite de Ixtapaluca.'
                    );

                    return;

                }


                const datos =
                    await respuesta.json();


                limiteIxtapaluca =
                    L.geoJSON(
                        datos, {

                            style: {

                                color: '#691C32',

                                weight: 3,

                                opacity: 1,

                                fillColor: '#9F2241',

                                fillOpacity: 0.06

                            }

                        }
                    )
                    .addTo(
                        mapaMonitoreo
                    );


                mapaMonitoreo.fitBounds(
                    limiteIxtapaluca.getBounds(), {

                        padding: [
                            20,
                            20
                        ]

                    }
                );

            } catch (error) {

                console.error(
                    'Error cargando el límite municipal:',
                    error
                );

            }

        }



        /*
        |--------------------------------------------------------------------------
        | CARGAR ALERTAS
        |--------------------------------------------------------------------------
        */

        async function cargarAlertas() {

            try {

                const respuesta =
                    await fetch(
                        API +
                        '/monitoreo/alertas', {

                            headers: {

                                'Authorization': 'Bearer ' +
                                    tokenMonitoreo,

                                'Accept': 'application/json'

                            }

                        }
                    );


                if (
                    respuesta.status ===
                    401
                ) {

                    cerrarSesionMonitoreo();

                    return;

                }


                const datos =
                    await respuesta.json();


                if (!respuesta.ok) {
                    return;
                }


                const alertas =
                    datos.alertas ?? [];


                actualizarIndicadores(
                    alertas
                );


                mostrarTablaAlertas(
                    alertas
                );

                mostrarAlertasActivasEnMapa(
                    alertas
                );

                cargarPolicias();

                actualizarHora();

            } catch (error) {

                console.error(
                    error
                );

            }

        }



        /*
        |--------------------------------------------------------------------------
        | INDICADORES
        |--------------------------------------------------------------------------
        */

        function actualizarIndicadores(
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
                    alerta =>
                    alerta.estado ===
                    'NUEVA'
                ).length;


            document
                .getElementById(
                    'totalAtencion'
                )
                .textContent =
                alertas.filter(
                    alerta =>
                    alerta.estado ===
                    'EN_CAMINO' ||
                    alerta.estado ===
                    'EN_SITIO'
                ).length;


            document
                .getElementById(
                    'totalCerradas'
                )
                .textContent =
                alertas.filter(
                    alerta =>
                    alerta.estado ===
                    'CERRADA'
                ).length;

        }



        /*
        |--------------------------------------------------------------------------
        | TABLA
        |--------------------------------------------------------------------------
        */

        function mostrarTablaAlertas(
            alertas
        ) {

            const tabla =
                document.getElementById(
                    'tablaAlertas'
                );


            tabla.innerHTML =
                '';


            if (
                alertas.length ===
                0
            ) {

                tabla.innerHTML =
                    `
                <tr>
                    <td colspan="4">
                        No existen alertas registradas.
                    </td>
                </tr>
                `;

                return;

            }


            alertas.forEach(
                function(alerta) {

                    const fila =
                        document.createElement(
                            'tr'
                        );


                    fila.innerHTML =
                        `
                    <td>
                        <strong>
                            ${alerta.folio}
                        </strong>
                    </td>

                    <td>
                        ${alerta.tipo}
                    </td>

                    <td>
                        <span
                            class="
                                estado
                                estado-${alerta.estado}
                            ">
                            ${alerta.estado}
                        </span>
                    </td>

                    <td>
                        ${alerta.prioridad}
                    </td>
                    `;


                    fila.onclick =
                        function() {

                            cargarDetalle(
                                alerta.id_alerta
                            );

                        };


                    tabla.appendChild(
                        fila
                    );

                }
            );

        }

        async function mostrarAlertasActivasEnMapa(
            alertas
        ) {

            marcadoresAlertasActivas
                .forEach(
                    function(marcador) {

                        mapaMonitoreo
                            .removeLayer(
                                marcador
                            );

                    }
                );


            marcadoresAlertasActivas = [];


            const alertasActivas =
                alertas.filter(
                    function(alerta) {

                        return alerta.estado !==
                            'CERRADA';

                    }
                );


            for (
                const alerta of
                    alertasActivas
            ) {

                try {

                    const respuesta =
                        await fetch(
                            API +
                            '/monitoreo/alertas/' +
                            alerta.id_alerta, {

                                headers: {

                                    'Authorization': 'Bearer ' +
                                        tokenMonitoreo,

                                    'Accept': 'application/json'

                                }

                            }
                        );


                    const datos =
                        await respuesta.json();


                    if (!respuesta.ok) {
                        continue;
                    }


                    const detalle =
                        datos.alerta;


                    if (
                        !detalle.ubicacion ||
                        !detalle.ubicacion.latitud ||
                        !detalle.ubicacion.longitud
                    ) {

                        continue;

                    }


                    const latitud =
                        parseFloat(
                            detalle.ubicacion.latitud
                        );


                    const longitud =
                        parseFloat(
                            detalle.ubicacion.longitud
                        );


                    let color =
                        '#616161';


                    if (
                        detalle.estado ===
                        'NUEVA'
                    ) {

                        color =
                            '#E53935';

                    }


                    if (
                        detalle.estado ===
                        'EN_ATENCION'
                    ) {

                        color =
                            '#8E24AA';

                    }


                    if (
                        detalle.estado ===
                        'EN_CAMINO'
                    ) {

                        color =
                            '#1976D2';

                    }


                    if (
                        detalle.estado ===
                        'EN_SITIO'
                    ) {

                        color =
                            '#FB8C00';

                    }


                    const marcador =
                        L.circleMarker(
                            [
                                latitud,
                                longitud
                            ], {

                                radius: 9,

                                color: '#FFFFFF',

                                weight: 3,

                                fillColor: color,

                                fillOpacity: 1

                            }
                        )
                        .addTo(
                            mapaMonitoreo
                        );


                    marcador.bindPopup(
                        `
                <strong>
                    ${detalle.folio}
                </strong>
                <br>

                ${detalle.tipo}
                <br>

                Estado:
                ${detalle.estado}
                `
                    );


                    marcador.on(
                        'click',
                        function() {

                            cargarDetalle(
                                detalle.id_alerta
                            );

                        }
                    );


                    marcadoresAlertasActivas
                        .push(
                            marcador
                        );


                } catch (error) {

                    console.error(
                        'Error cargando alerta en mapa:',
                        error
                    );

                }

            }

        }

        async function cargarPolicias() {

            try {

                const respuesta =
                    await fetch(
                        API +
                        '/monitoreo/policias', {

                            headers: {

                                'Authorization': 'Bearer ' +
                                    tokenMonitoreo,

                                'Accept': 'application/json'

                            }

                        }
                    );


                const datos =
                    await respuesta.json();


                if (!respuesta.ok) {
                    return;
                }


                marcadoresPolicias.forEach(
                    function(marcador) {

                        mapaMonitoreo.removeLayer(
                            marcador
                        );

                    }
                );


                marcadoresPolicias = [];


                const policias =
                    datos.policias ?? [];


                policias.forEach(
                    function(policia) {

                        if (
                            !policia.ubicacion ||
                            !policia.ubicacion.latitud ||
                            !policia.ubicacion.longitud
                        ) {
                            return;
                        }


                        const latitud =
                            parseFloat(
                                policia.ubicacion.latitud
                            );


                        const longitud =
                            parseFloat(
                                policia.ubicacion.longitud
                            );


                        let ubicacionReciente =
                            false;


                        if (
                            policia.ubicacion_actualizada_at
                        ) {

                            const ultimaUbicacion =
                                new Date(
                                    policia.ubicacion_actualizada_at
                                );


                            const diferencia =
                                Date.now() -
                                ultimaUbicacion.getTime();


                            ubicacionReciente =
                                diferencia <=
                                120000;

                        }


                        let claseEstado =
                            'policia-desactualizado';


                        let textoEstado =
                            'Ubicación desactualizada';


                        if (
                            ubicacionReciente &&
                            policia.disponible
                        ) {

                            claseEstado =
                                'policia-disponible';

                            textoEstado =
                                'Disponible';

                        }


                        if (
                            ubicacionReciente &&
                            !policia.disponible
                        ) {

                            claseEstado =
                                'policia-servicio';

                            textoEstado =
                                'En servicio';

                        }


                        const icono =
                            L.divIcon({

                                className: 'marcador-policia-mapa',

                                html: `
                                <div
                                    class="
                                        policia-circulo
                                        ${claseEstado}
                                    ">
                                    P
                                </div>
                                `,

                                iconSize: [
                                    34,
                                    34
                                ],

                                iconAnchor: [
                                    17,
                                    17
                                ]

                            });


                        const marcador =
                            L.marker(
                                [
                                    latitud,
                                    longitud
                                ], {

                                    icon: icono

                                }
                            )
                            .addTo(
                                mapaMonitoreo
                            );


                        let fechaTexto =
                            'Sin información';


                        if (
                            policia.ubicacion_actualizada_at
                        ) {

                            fechaTexto =
                                new Date(
                                    policia.ubicacion_actualizada_at
                                )
                                .toLocaleString(
                                    'es-MX'
                                );

                        }


                        marcador.bindPopup(
                            `
                    <strong>
                        ${policia.nombre}
                    </strong>

                    <br>

                    Estado:
                    ${textoEstado}

                    <br>

                    Última ubicación:
                    ${fechaTexto}
                    `
                        );


                        marcadoresPolicias.push(
                            marcador
                        );

                    }
                );


            } catch (error) {

                console.error(
                    'Error cargando policías:',
                    error
                );

            }

        }

        //DETALLE

        async function cargarDetalle(
            idAlerta
        ) {

            const respuesta =
                await fetch(
                    API +
                    '/monitoreo/alertas/' +
                    idAlerta, {

                        headers: {

                            'Authorization': 'Bearer ' +
                                tokenMonitoreo,

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


            const detalle =
                document.getElementById(
                    'detalleAlerta'
                );


            detalle.innerHTML =
                `
            <div class="dato">

                <span class="dato-etiqueta">
                    Folio
                </span>

                <span class="dato-valor">
                    ${alerta.folio}
                </span>

            </div>


            <div class="dato">

                <span class="dato-etiqueta">
                    Tipo de emergencia
                </span>

                <span class="dato-valor">
                    ${alerta.tipo}
                </span>

            </div>


            <div class="dato">

                <span class="dato-etiqueta">
                    Estado
                </span>

                <span
                    class="
                        estado
                        estado-${alerta.estado}
                    ">

                    ${alerta.estado}

                </span>

            </div>


            <div class="dato">

                <span class="dato-etiqueta">
                    Prioridad
                </span>

                <span class="dato-valor">
                    ${alerta.prioridad}
                </span>

            </div>


            <div class="dato">

                <span class="dato-etiqueta">
                    Policía asignado
                </span>

                <span
                    class="dato-valor"
                    id="detallePolicia">

                    Sin asignar

                </span>

            </div>


            <div class="historial">

                <h4>
                    Seguimiento
                </h4>

                <div id="historialAlerta">
                    Cargando...
                </div>

            </div>
            `;


            mostrarUbicacionAlerta(
                alerta
            );


            cargarHistorial(
                idAlerta
            );

        }



        /*
        |--------------------------------------------------------------------------
        | MOSTRAR UBICACIÓN
        |--------------------------------------------------------------------------
        */

        function mostrarUbicacionAlerta(
            alerta
        ) {

            if (
                !alerta.ubicacion ||
                !alerta.ubicacion.latitud ||
                !alerta.ubicacion.longitud
            ) {
                return;
            }


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
                    L.circleMarker(
                        [
                            latitud,
                            longitud
                        ], {

                            radius: 9,

                            color: '#9F2241',

                            fillColor: '#9F2241',

                            fillOpacity: .90

                        }
                    )
                    .addTo(
                        mapaMonitoreo
                    );

            } else {

                marcadorAlerta
                    .setLatLng(
                        [
                            latitud,
                            longitud
                        ]
                    );

            }


            marcadorAlerta
                .bindPopup(
                    `
                <strong>
                    ${alerta.folio}
                </strong>
                <br>
                ${alerta.tipo}
                <br>
                Estado:
                ${alerta.estado}
                `
                )
                .openPopup();


            mapaMonitoreo
                .setView(
                    [
                        latitud,
                        longitud
                    ],
                    16
                );

        }



        /*
        |--------------------------------------------------------------------------
        | HISTORIAL
        |--------------------------------------------------------------------------
        */

        async function cargarHistorial(
            idAlerta
        ) {

            const respuesta =
                await fetch(
                    API +
                    '/monitoreo/alertas/' +
                    idAlerta +
                    '/seguimiento', {

                        headers: {

                            'Authorization': 'Bearer ' +
                                tokenMonitoreo,

                            'Accept': 'application/json'

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
                    'historialAlerta'
                );


            contenedor.innerHTML =
                '';


            let policia =
                'Sin asignar';


            if (
                historial.length ===
                0
            ) {

                contenedor.innerHTML =
                    '<span>No existe seguimiento registrado.</span>';

            }


            historial.forEach(
                function(registro) {

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
                        `
                    <strong>
                        ${registro.accion}
                    </strong>

                    <span>
                        ${registro.operador ?? ''}
                    </span>

                    ${
                        registro.observaciones
                        ?
                        '<br><span>' +
                        registro.observaciones +
                        '</span>'
                        :
                        ''
                    }
                    `;


                    contenedor
                        .appendChild(
                            item
                        );

                }
            );


            const campoPolicia =
                document.getElementById(
                    'detallePolicia'
                );


            if (campoPolicia) {

                campoPolicia.textContent =
                    policia;

            }

        }



        /*
        |--------------------------------------------------------------------------
        | HORA DE ACTUALIZACIÓN
        |--------------------------------------------------------------------------
        */

        function actualizarHora() {

            const ahora =
                new Date();


            document
                .getElementById(
                    'ultimaActualizacion'
                )
                .textContent =
                'Última actualización: ' +
                ahora.toLocaleTimeString(
                    'es-MX', {

                        hour: '2-digit',

                        minute: '2-digit',

                        second: '2-digit'

                    }
                );

        }



        /*
        |--------------------------------------------------------------------------
        | CERRAR SESIÓN
        |--------------------------------------------------------------------------
        */

        function cerrarSesionMonitoreo() {

            sessionStorage.removeItem(
                'alerta_ixta_token_monitoreo'
            );


            sessionStorage.removeItem(
                'alerta_ixta_nombre_monitoreo'
            );


            location.reload();

        }



        /*
        |--------------------------------------------------------------------------
        | RECARGA AUTOMÁTICA
        |--------------------------------------------------------------------------
        */

        setInterval(
            function() {

                if (
                    tokenMonitoreo
                ) {

                    cargarAlertas();

                }

            },
            10000
        );



        /*
        |--------------------------------------------------------------------------
        | RECUPERAR SESIÓN
        |--------------------------------------------------------------------------
        */

        if (
            tokenMonitoreo
        ) {

            mostrarSistema();

        }
    </script>

</body>

</html>