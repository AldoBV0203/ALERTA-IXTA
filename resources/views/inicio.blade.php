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

        .tarjeta-notificacion-sos {
            position: relative;
            padding: 16px;
            padding-right: 56px;
            margin-top: 16px;
            border: 1px solid #691C32;
            border-radius: 12px;
            background: #fff7f9;
        }

        .tarjeta-notificacion-sos[hidden],
        .tarjeta-notificacion-sos p[hidden] {
            display: none;
        }

        .tarjeta-notificacion-sos h3 {
            margin: 0 0 8px;
            color: #691C32;
        }

        .tarjeta-notificacion-sos p {
            margin: 6px 0;
        }

        .invitacion-notificaciones-sos {
            width: min(360px, calc(100% - 32px));
            max-height: calc(100% - 32px);
            padding: 20px;
            border: 1px solid #691C32;
            border-radius: 14px;
            background: #fff7f9;
            color: #171717;
            box-sizing: border-box;
        }

        .invitacion-notificaciones-sos::backdrop {
            background: rgba(0, 0, 0, 0.5);
        }

        .invitacion-notificaciones-sos h3 {
            margin: 0 0 10px;
            color: #691C32;
        }

        .invitacion-notificaciones-sos p {
            margin: 8px 0 12px;
        }

        .acciones-permiso-sos {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .acciones-permiso-sos button {
            min-height: 44px;
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: #691C32;
            color: white;
            cursor: pointer;
        }

        .acciones-permiso-sos button:last-child {
            background: #eee7e9;
            color: #691C32;
        }

        .acciones-notificacion-sos {
            margin-top: 12px;
        }

        .acciones-notificacion-sos button {
            min-height: 44px;
            padding: 10px 16px;
            border: 0;
            border-radius: 8px;
            background: #691C32;
            color: white;
            cursor: pointer;
        }

        .cerrar-notificacion-sos {
            position: absolute;
            top: 10px;
            right: 10px;
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            padding: 0;
            border: 1px solid #691C32;
            border-radius: 50%;
            background: white;
            color: #691C32;
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
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

        /* Pantallas independientes del ciudadano */
        #panelCuentaCiudadano,
        #panelGruposCiudadano,
        #panelHistorialCiudadano {
            display: none;
        }

        /* Mostrar únicamente el contenido de la sección seleccionada */
        #pantallaContenido.modo-cuenta> :not(#panelCuentaCiudadano),
        #pantallaContenido.modo-grupos> :not(#panelGruposCiudadano),
        #pantallaContenido.modo-historial> :not(#panelHistorialCiudadano) {
            display: none !important;
        }

        #pantallaContenido.modo-cuenta>#panelCuentaCiudadano,
        #pantallaContenido.modo-grupos>#panelGruposCiudadano,
        #pantallaContenido.modo-historial>#panelHistorialCiudadano {
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

        #panelCuentaCiudadano>.boton-cerrar-sesion {
            margin-top: 200px;
            margin-bottom: 15px;
        }

        /* Tarjetas de contactos confirmados. */
        .tarjeta-contacto-confianza {
            background: white;
            border: 1px solid #DDDDDD;
            border-left: 4px solid #691C32;
            border-radius: 10px;
            padding: 14px;
            margin-top: 12px;
        }

        .tarjeta-contacto-confianza strong {
            color: #691C32;
            font-size: 14px;
        }

        .tarjeta-contacto-confianza p {
            color: #555555;
            font-size: 12px;
            margin: 8px 0 0;
        }

        /* Cuadros del círculo de confianza*/
        #listaContactosConfianza {
            background: #FFFFFF;
            border: 1px solid #DDDDDD;
            border-radius: 10px;
            padding: 10px;
            min-height: 75px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        /* Contactos dentro del cuadro */
        #listaContactosConfianza .tarjeta-contacto-confianza {
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 10px 5px;
            margin: 0;
        }

        /* Separación entre contactos */
        #listaContactosConfianza .tarjeta-contacto-confianza+.tarjeta-contacto-confianza {
            border-top: 1px solid #EEEEEE;
        }


        .solicitud-confianza+.solicitud-confianza {
            border-top: 1px solid #EEEEEE;
        }

        .solicitud-confianza strong {
            display: block;
            color: #691C32;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .botones-solicitud-confianza {
            display: flex;
            gap: 10px;
        }

        .botones-solicitud-confianza button {
            flex: 1;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .boton-aceptar-confianza {
            background: #691C32;
            color: white;
        }

        .boton-rechazar-confianza {
            background: #EEEEEE;
            color: #333333;
        }

        .botones-solicitud-confianza button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Generación de códigos de confianza */
        .panel-codigo-confianza {
            background: #FFFFFF;
            border: 1px solid #DDDDDD;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }

        .panel-codigo-confianza h3 {
            margin-top: 0;
        }

        .panel-codigo-confianza p {
            font-size: 13px;
            color: #555555;
            line-height: 1.5;
        }

        .app .boton.boton-codigo-confianza {
            background-color: #691C32 !important;
            color: #FFFFFF;
        }

        .app .boton.boton-codigo-confianza:hover {
            background-color: #9F2241 !important;
        }

        #resultadoCodigoConfianza {
            margin-top: 15px;
            padding: 12px;
            background: #F8E8EC;
            border-radius: 8px;
            text-align: center;
        }

        #valorCodigoConfianza {
            color: #691C32;
            font-size: 18px;
            letter-spacing: 1px;
            overflow-wrap: anywhere;
        }

        #mensajeCodigoConfianza {
            margin-bottom: 0;
        }

        /* Formulario para agregar contactos de confianza */
        .panel-introducir-codigo {
            background: #FFFFFF;
            border: 1px solid #DDDDDD;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }

        .panel-introducir-codigo h3 {
            margin-top: 0;
        }

        .panel-introducir-codigo p {
            color: #555555;
            font-size: 13px;
            line-height: 1.5;
        }

        #codigoContactoConfianza {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #CCCCCC;
            border-radius: 8px;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .app .boton.boton-enviar-confianza {
            background-color: #691C32 !important;
            color: #FFFFFF;
        }

        .app .boton.boton-enviar-confianza:hover {
            background-color: #9F2241 !important;
        }

        #mensajeSolicitudConfianza {
            margin-bottom: 0;
        }

        /* Botón para eliminar contactos de confianza */
        .boton-eliminar-confianza {
            display: block;
            margin-top: 12px;
            padding: 8px 12px;
            background: transparent;
            border: 1px solid #9F2241;
            border-radius: 8px;
            color: #691C32;
            font-size: 12px;
            cursor: pointer;
        }

        .boton-eliminar-confianza:hover {
            background: #F8E8EC;
        }

        .boton-eliminar-confianza:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        #formReenviarIne {
            margin-top: 12px;
        }

        #nuevaFotoIne {
            background: white;
        }

        #nuevaFotoIne::file-selector-button {
            margin-right: 8px;
            padding: 7px 10px;
            border: 1px solid var(--vino-principal);
            border-radius: 6px;
            background: white;
            color: var(--vino-oscuro);
            cursor: pointer;
        }

        #btnReenviarIne {
            margin-top: 10px;
            background: var(--vino-oscuro);
        }

        #btnReenviarIne:hover:not(:disabled) {
            background: var(--vino-principal);
        }

        #btnReenviarIne:disabled {
            opacity: 0.65;
            cursor: wait;
        }

        /* Pantalla de grupos */
        .cabecera-grupos {
            margin-bottom: 24px;
        }

        .cabecera-grupos h2 {
            margin: 0;
            color: #252A30;
            font-size: 24px;
            line-height: 1.25;
        }

        .cabecera-grupos p {
            margin: 8px 0 0;
            color: #62676C;
            font-size: 14px;
            line-height: 1.5;
        }

        .listado-grupos {
            display: grid;
            gap: 12px;
        }

        .acceso-grupo {
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr) 18px;
            align-items: center;
            gap: 14px;
            width: 100%;
            min-height: 96px;
            padding: 16px;
            border: 1px solid #E5DDE0;
            border-radius: 12px;
            background: #FFFFFF;
            font: inherit;
            text-align: left;
            cursor: pointer;
        }

        .acceso-grupo:hover {
            border-color: #9F2241;
            background: #FCF8F9;
        }

        .acceso-grupo:focus-visible {
            outline: 2px solid #9F2241;
            outline-offset: 3px;
        }

        .icono-grupo {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #F6ECEF;
            color: #691C32;
        }

        .icono-grupo svg,
        .flecha-grupo {
            width: 24px;
            height: 24px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .texto-grupo strong {
            display: block;
            color: #252A30;
            font-size: 15px;
            line-height: 1.4;
        }

        .texto-grupo>span {
            display: block;
            margin-top: 5px;
            color: #62676C;
            font-size: 13px;
            line-height: 1.4;
        }

        .flecha-grupo {
            width: 18px;
            height: 18px;
            color: #92979C;
        }

        .volver-grupos {
            display: inline-flex;
            align-items: center;
            margin: 0 0 20px;
            padding: 6px 0;
            border: none;
            background: transparent;
            color: #691C32;
            font: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .volver-grupos:hover {
            text-decoration: underline;
        }

        .volver-grupos:focus-visible {
            outline: 2px solid #9F2241;
            outline-offset: 4px;
        }

        #panelCuentaCiudadano>.boton-cerrar-sesion {
            margin-top: 32px;
        }

        /* Herramientas compartidas de Grupos */
        .herramienta-grupo {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #E8E0E3;
        }

        .herramienta-grupo h3 {
            margin: 0;
            color: #252A30;
            font-size: 16px;
            line-height: 1.4;
        }

        .herramienta-grupo>p:not([role="status"]) {
            margin: 8px 0 16px;
            color: #62676C;
            font-size: 13px;
            line-height: 1.6;
        }

        #panelGruposCiudadano .campo label {
            color: #252A30;
            font-size: 13px;
            font-weight: 600;
        }

        #panelGruposCiudadano .campo input,
        #panelGruposCiudadano .campo select {
            width: 100%;
            min-height: 46px;
            box-sizing: border-box;
            margin: 6px 0 0;
            padding: 12px;
            border: 1px solid #D8D3D5;
            border-radius: 8px;
            background: #FFFFFF;
            color: #252A30;
            font: inherit;
            font-size: 14px;
        }

        #panelGruposCiudadano .campo input:focus-visible,
        #panelGruposCiudadano .campo select:focus-visible {
            outline: 2px solid #9F2241;
            outline-offset: 2px;
        }

        /* Listas de contactos */
        #listaContactosConfianza,
        #listaContactosVecinos {
            min-height: 75px;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #E5DDE0;
            border-radius: 12px;
            background: #FFFFFF;
            box-sizing: border-box;
        }

        #listaContactosConfianza .tarjeta-contacto-confianza,
        #listaContactosVecinos .tarjeta-contacto-confianza {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            column-gap: 12px;
            row-gap: 4px;
            margin: 0;
            padding: 14px 4px;
            border: none;
            border-radius: 0;
            background: transparent;
        }

        #listaContactosConfianza .tarjeta-contacto-confianza+.tarjeta-contacto-confianza,
        #listaContactosVecinos .tarjeta-contacto-confianza+.tarjeta-contacto-confianza {
            border-top: 1px solid #EEE8EB;
        }

        #panelGruposCiudadano .tarjeta-contacto-confianza strong {
            grid-column: 1;
            color: #252A30;
            overflow-wrap: anywhere;
        }

        #panelGruposCiudadano .tarjeta-contacto-confianza p {
            grid-column: 1;
            margin: 0;
        }

        #panelGruposCiudadano .boton-eliminar-confianza {
            grid-column: 2;
            grid-row: 1 / 3;
            margin: 0;
            padding: 8px 10px;
            font-size: 12px;
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

                <dialog
                    id="invitacionNotificacionesSos"
                    class="invitacion-notificaciones-sos"
                    aria-labelledby="tituloPermisoNotificacionesSos">
                    <h3 id="tituloPermisoNotificacionesSos">
                        Recibe notificaciones SOS
                    </h3>
                    <p>Entérate si un familiar o vecino de tus círculos crea una alerta.</p>
                    <p id="estadoNotificacionesSos" role="status"></p>

                    <div class="acciones-permiso-sos">
                        <button type="button" onclick="activarNotificacionesSos()">
                            Activar notificaciones
                        </button>
                        <button type="button" onclick="omitirNotificacionesSos()">
                            Ahora no
                        </button>
                    </div>
                </dialog>

                <section
                    id="tarjetaNotificacionSos"
                    class="tarjeta-notificacion-sos"
                    hidden>

                    <button
                        type="button"
                        class="cerrar-notificacion-sos"
                        aria-label="Cerrar vista de la alerta"
                        title="Cerrar vista"
                        onclick="cerrarVistaNotificacionSos()">
                        ×
                    </button>

                    <h3 id="tituloNotificacionSos">Alerta SOS</h3>
                    <p id="circuloNotificacionSos"></p>

                    <!-- Se conservan estos identificadores porque el JavaScript aún los actualiza. -->
                    <p id="datosNotificacionSos" hidden></p>
                    <p id="ubicacionNotificacionSos" hidden></p>

                    <div class="acciones-notificacion-sos">
                        <button
                            type="button"
                            onclick="centrarNotificacionSos()">
                            Seguir ubicación
                        </button>
                    </div>
                </section>

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
                        id="btnConfirmarAtencionCiudadano"
                        class="boton"
                        onclick="generarQrAtencionCiudadano()"
                        style="display: none; margin-top: 10px; background: var(--vino-principal);">
                        Generar QR de atención
                    </button>

                    <p id="mensajeConfirmacionAtencion" class="info" role="status" hidden></p>

                    <div
                        id="panelQrAtencion"
                        hidden
                        style="background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 16px; margin-top: 12px; text-align: center;">

                        <strong>QR de atención</strong>

                        <div
                            id="imagenQrAtencion"
                            style="display: flex; justify-content: center; padding: 16px 0;">
                        </div>

                        <p id="folioQrAtencion" class="info"></p>

                        <p
                            id="vigenciaQrAtencion"
                            class="info"
                            role="status">
                        </p>

                        <p class="info">
                            Muestra este QR al policía para finalizar la atención.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="btnCancelarAlerta"
                        class="boton boton-cancelar-alerta"
                        onclick="cancelarAlerta()"
                        style="display: none;">
                        Cancelar alerta
                    </button>

                    <p id="estadoSolicitudCancelacion" class="info"></p>

                    <form
                        id="formConfirmarCancelacion"
                        onsubmit="confirmarCancelacion(event)"
                        hidden>

                        <label for="codigoCancelacion">Código de cancelación</label>

                        <input
                            id="codigoCancelacion"
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            placeholder="Seis dígitos"
                            style="display: block; margin: 8px 0; padding: 8px;"
                            required>

                        <button
                            id="btnConfirmarCancelacion"
                            type="submit"
                            class="boton boton-cancelar-alerta">
                            Confirmar cancelación
                        </button>

                        <p id="mensajeConfirmacionCancelacion" class="info" role="status"></p>
                    </form>

                </div>

                <section id="panelCuentaCiudadano" class="estado-alerta">

                    <h2>Mi cuenta</h2>

                    <p>
                        <strong>Nombre:</strong>
                        <span id="nombreCuentaCiudadano">Ciudadano</span>
                    </p>

                    <p>
                        <strong>Rol:</strong>
                        Ciudadano
                    </p>

                    <div>
                        <p>
                            <strong>Verificación de identidad:</strong>
                            <span id="estadoVerificacionCiudadano">Consultando...</span>
                        </p>

                        <p id="motivoRechazoIne" class="info" hidden></p>

                        <form
                            id="formReenviarIne"
                            class="campo"
                            onsubmit="reenviarFotoIne(event)"
                            hidden>

                            @include('componentes.selector-foto', [
                            'idCampoFoto' => 'nuevaFotoIne',
                            'nombreCampoFoto' => 'ine_foto',
                            'etiquetaFoto' => 'Nueva foto del INE',
                            ])

                            <button
                                id="btnReenviarIne"
                                type="submit"
                                class="boton"
                                style="display: none;">
                                Enviar
                            </button>
                        </form>

                        <p id="mensajeReenvioIne" class="info" role="status"></p>
                    </div>

                    <button
                        type="button"
                        class="boton boton-cerrar-sesion"
                        onclick="cerrarSesion()">
                        Cerrar sesión
                    </button>

                </section>

                <section id="panelGruposCiudadano">

                    <div id="vistaListadoGrupos">
                        <header class="cabecera-grupos">
                            <h2>Grupos</h2>
                            <p>Administra tus contactos de confianza y vecinos.</p>
                        </header>

                        <div class="listado-grupos">

                            <button
                                type="button"
                                class="acceso-grupo"
                                onclick="abrirGrupoCiudadano('confianza')">

                                <span class="icono-grupo" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <circle cx="9" cy="8" r="3"></circle>
                                        <path d="M3 20v-2a6 6 0 0 1 12 0v2"></path>
                                        <path d="M16 5a3 3 0 0 1 0 6"></path>
                                        <path d="M17 14a5 5 0 0 1 4 5v1"></path>
                                    </svg>
                                </span>

                                <span class="texto-grupo">
                                    <strong>Círculo de confianza</strong>
                                    <span>Familiares y personas de confianza</span>
                                </span>

                                <svg class="flecha-grupo" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="m9 5 7 7-7 7"></path>
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="acceso-grupo"
                                onclick="abrirGrupoCiudadano('vecinos')">

                                <span class="icono-grupo" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="m3 10 9-7 9 7"></path>
                                        <path d="M5 9v12h14V9"></path>
                                        <path d="M9 21v-7h6v7"></path>
                                    </svg>
                                </span>

                                <span class="texto-grupo">
                                    <strong>Círculo de vecinos</strong>
                                    <span>Personas de tu comunidad</span>
                                </span>

                                <svg class="flecha-grupo" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="m9 5 7 7-7 7"></path>
                                </svg>
                            </button>

                        </div>

                        <div class="herramienta-grupo">
                            <h3>Mi código de invitación</h3>

                            <p>
                                Comparte este código con otra persona registrada.
                                Ella elegirá el círculo donde se vincularán.
                            </p>

                            <button
                                type="button"
                                id="btnGenerarCodigoConfianza"
                                class="boton boton-codigo-confianza"
                                onclick="generarCodigoConfianza()">
                                Generar código
                            </button>

                            <div id="resultadoCodigoConfianza" style="display: none;">
                                <strong id="valorCodigoConfianza"></strong>
                                <p>Válido durante 15 minutos.</p>
                            </div>

                            <p
                                id="mensajeCodigoConfianza"
                                class="info"
                                role="status">
                            </p>
                        </div>

                        <div class="herramienta-grupo">
                            <h3>Agregar contacto</h3>
                            <p>Elige un círculo e introduce el código de la otra persona.</p>

                            <div class="campo">
                                <label for="tipoCirculoContacto">Círculo</label>

                                <select id="tipoCirculoContacto">
                                    <option value="">Selecciona un círculo</option>
                                    <option value="confianza">Círculo de confianza</option>
                                    <option value="vecinos">Círculo de vecinos</option>
                                </select>
                            </div>

                            <div class="campo">
                                <label for="codigoContactoConfianza">Código de invitación</label>

                                <input
                                    type="text"
                                    id="codigoContactoConfianza"
                                    placeholder="IXTA-XXXXXXXXXXXX"
                                    maxlength="17"
                                    autocomplete="off"
                                    spellcheck="false">
                            </div>

                            <button
                                type="button"
                                id="btnEnviarSolicitudConfianza"
                                class="boton boton-enviar-confianza"
                                onclick="enviarSolicitudConfianza()">
                                Vincular contacto
                            </button>

                            <p
                                id="mensajeSolicitudConfianza"
                                class="info"
                                role="status">
                            </p>
                        </div>
                    </div>

                    <div id="vistaCirculoCiudadano" style="display: none;">

                        <button
                            type="button"
                            class="volver-grupos"
                            onclick="volverGruposCiudadano()">
                            ← Volver a Grupos
                        </button>

                        <header class="cabecera-grupos">
                            <h2>Círculo de confianza</h2>
                            <p>Consulta y administra tus contactos de confianza.</p>
                        </header>

                        <div id="listaContactosConfianza"></div>
                    </div>

                    <div id="vistaVecinosCiudadano" style="display: none;">

                        <button
                            type="button"
                            class="volver-grupos"
                            onclick="volverGruposCiudadano()">
                            ← Volver a Grupos
                        </button>

                        <header class="cabecera-grupos">
                            <h2>Círculo de vecinos</h2>
                            <p>Consulta y administra tus vecinos vinculados.</p>
                        </header>

                        <div id="listaContactosVecinos" class="lista-vecinos"></div>
                    </div>

                </section>

                <section id="panelHistorialCiudadano">

                    <div id="vistaHistorialCiudadano">
                        <header class="cabecera-grupos">
                            <h2>Historial</h2>
                            <p>Consulta tus alertas anteriores.</p>
                        </header>

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
                    aria-current="page"
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
                    data-seccion="grupos"
                    onclick="navegarCiudadano('grupos')">
                    Grupos
                </button>

                <button
                    type="button"
                    data-seccion="historial"
                    onclick="navegarCiudadano('historial')">
                    Historial
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

    <script src="{{ asset('js/vendor/qrcodejs/qrcode.min.js') }}"></script>

    <script>
        const API = '/api';

        const token =
            sessionStorage.getItem(
                'alerta_ixta_token'
            );

        const CLAVE_OMITIR_NOTIFICACIONES_SOS =
            'alerta_ixta_omitir_notificaciones_sos';

        function estadoPermisoNotificacionesSos(mensaje) {
            const estado = document.getElementById('estadoNotificacionesSos');

            if (estado) estado.textContent = mensaje;
        }

        function ocultarInvitacionNotificacionesSos() {
            const invitacion = document.getElementById('invitacionNotificacionesSos');

            if (invitacion?.open) invitacion.close();
        }

        let idUsuarioNotificacionesSos = null;

        function omitirNotificacionesSos() {
            sessionStorage.setItem(
                CLAVE_OMITIR_NOTIFICACIONES_SOS + '_' + idUsuarioNotificacionesSos,
                '1'
            );
            ocultarInvitacionNotificacionesSos();
        }

        function convertirClavePublicaPush(clave) {
            const base64 = clave.replace(/-/g, '+').replace(/_/g, '/');
            const relleno = '='.repeat((4 - base64.length % 4) % 4);
            const binario = atob(base64 + relleno);

            return Uint8Array.from(binario, caracter => caracter.charCodeAt(0));
        }

        async function registrarSuscripcionNotificacionesSos() {
            const respuestaClave = await fetch(
                API + '/suscripciones-push/clave-publica', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                }
            );

            if (!respuestaClave.ok) {
                throw new Error('No se pudo obtener la clave pública.');
            }

            const {
                publicKey
            } = await respuestaClave.json();
            await navigator.serviceWorker.register('/sw-sos.js');
            const registro = await navigator.serviceWorker.ready;

            let suscripcion = await registro.pushManager.getSubscription();

            if (!suscripcion) {
                suscripcion = await registro.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: convertirClavePublicaPush(publicKey)
                });
            }

            const respuestaRegistro = await fetch(
                API + '/suscripciones-push', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(suscripcion.toJSON())
                }
            );

            if (!respuestaRegistro.ok) {
                throw new Error('No se pudo registrar este navegador.');
            }

            localStorage.setItem(
                'alerta_ixta_notificaciones_confirmadas_' + idUsuarioNotificacionesSos,
                '1'
            );

            ocultarInvitacionNotificacionesSos();
            estadoPermisoNotificacionesSos(
                'Notificaciones activadas en este navegador.'
            );
        }

        async function activarNotificacionesSos() {
            if (!window.isSecureContext ||
                !('serviceWorker' in navigator) ||
                !('PushManager' in window) ||
                !('Notification' in window)) {
                estadoPermisoNotificacionesSos(
                    'Este navegador o dirección no permite notificaciones.'
                );
                return;
            }

            try {
                // Se solicita directamente desde el clic en «Activar».
                const permiso = Notification.permission === 'granted' ?
                    'granted' :
                    await Notification.requestPermission();

                if (permiso !== 'granted') {
                    estadoPermisoNotificacionesSos(
                        'Permiso no concedido. Puedes cambiarlo en el navegador.'
                    );
                    return;
                }

                estadoPermisoNotificacionesSos('Activando notificaciones...');
                await registrarSuscripcionNotificacionesSos();
            } catch (error) {
                console.error(error);
                estadoPermisoNotificacionesSos(
                    'No se pudieron activar. Inténtalo de nuevo.'
                );
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            if (!token) return;

            if (!window.isSecureContext ||
                !('serviceWorker' in navigator) ||
                !('PushManager' in window) ||
                !('Notification' in window)) {
                estadoPermisoNotificacionesSos(
                    'Las notificaciones requieren un navegador compatible y una dirección segura.'
                );
                return;
            }

            try {
                const registro = await navigator.serviceWorker.getRegistration('/');
                const suscripcion = await registro?.pushManager.getSubscription();

                const respuesta = await fetch(API + '/suscripciones-push/estado', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        endpoint: suscripcion?.endpoint ?? null
                    })
                });

                if (!respuesta.ok) {
                    throw new Error('No se pudo comprobar la suscripción.');
                }

                const estado = await respuesta.json();
                idUsuarioNotificacionesSos = estado.id_usuario;

                const confirmada = localStorage.getItem(
                    'alerta_ixta_notificaciones_confirmadas_' +
                    idUsuarioNotificacionesSos
                ) === '1';

                if (Notification.permission === 'granted' &&
                    estado.registrada && confirmada) {
                    estadoPermisoNotificacionesSos(
                        'Notificaciones activadas en este navegador.'
                    );
                    return;
                }

                // Una suscripción antigua creada automáticamente espera la
                // confirmación de esta cuenta antes de recibir más alertas.
                if (estado.registrada && !confirmada && suscripcion) {
                    await fetch(API + '/suscripciones-push', {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            endpoint: suscripcion.endpoint
                        })
                    });
                }

                estadoPermisoNotificacionesSos(
                    Notification.permission === 'denied' ?
                    'Permiso bloqueado. Actívalo en la configuración de Chrome.' :
                    'Aún no están activadas para esta cuenta.'
                );

                if (sessionStorage.getItem(
                        CLAVE_OMITIR_NOTIFICACIONES_SOS + '_' +
                        idUsuarioNotificacionesSos
                    ) !== '1') {
                    document.getElementById('invitacionNotificacionesSos')
                        .showModal();
                }
            } catch (error) {
                console.error(error);
                estadoPermisoNotificacionesSos(
                    'No se pudo comprobar si las notificaciones están activadas.'
                );
            }
        });

        let mapa = null;

        let marcadorCiudadano = null;

        let marcadorPolicia = null;
        let marcadorContactoSos = null;
        let idNotificacionSosActiva = null;
        let intervaloNotificacionSos = null;
        let seguirContactoSos = false;

        let latitudActual = null;

        let longitudActual = null;

        let precisionActual = null;

        let idAlertaActual = null;
        let estadoAlertaActual = null;
        let enviandoSolicitudCancelacion = false;

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


        // Conservar la alerta solicitada incluso si hay un token ya vencido.
        const destinoSos = window.location.pathname + window.location.search;

        if (/^\/inicio\?notificacion=[1-9]\d*$/.test(destinoSos)) {
            sessionStorage.setItem(
                'alerta_ixta_destino_sos',
                destinoSos
            );
        }

        // Si no existe token, regresar al login.
        if (!token) {
            window.location.replace('/');
        }


        document.addEventListener(
            'DOMContentLoaded',
            async function() {

                iniciarMapa();
                obtenerUbicacion();

                await cargarUsuario();

                await cargarTiposAlerta();

                await cargarMisAlertas();

                const idNotificacion = new URLSearchParams(
                    window.location.search
                ).get('notificacion');

                if (idNotificacion && /^\d+$/.test(idNotificacion)) {
                    await mostrarNotificacionSos(idNotificacion);
                }

            }
        );

        // Solicitar una ubicación nueva al recuperar la conexión.
        window.addEventListener('online', function() {

            // Conservar el punto manual durante las pruebas.
            if (ubicacionManualCiudadano) {
                actualizarUbicacionCiudadano();
                return;
            }

            // Esperar una lectura nueva antes de volver a enviar.
            ultimaLecturaGPSCiudadano = null;

            obtenerUbicacion();
        });

        // Marcar la pestaña seleccionada.
        function marcarPestanaCiudadano(seccion) {
            document.querySelectorAll('#navCiudadano button')
                .forEach(function(boton) {
                    const seleccionado = boton.dataset.seccion === seccion;

                    boton.classList.toggle('activo', seleccionado);

                    if (seleccionado) {
                        boton.setAttribute('aria-current', 'page');
                    } else {
                        boton.removeAttribute('aria-current');
                    }
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

                const historial = alertas.filter(function(alerta) {
                    return ['CERRADA', 'CANCELADA', 'PENDIENTE_CONFIRMACION'].includes(alerta.estado);
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

        // Enviar una solicitud de confianza mediante un código.
        async function enviarSolicitudConfianza() {
            const campo = document.getElementById('codigoContactoConfianza');
            const boton = document.getElementById('btnEnviarSolicitudConfianza');
            const mensaje = document.getElementById('mensajeSolicitudConfianza');
            const selector = document.getElementById('tipoCirculoContacto');

            const tipo = selector?.value ?? 'confianza';
            const codigo = campo.value.trim().toUpperCase();

            if (boton.disabled) {
                return;
            }

            if (!['confianza', 'vecinos'].includes(tipo)) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent = 'Selecciona el círculo donde agregarás al contacto.';
                return;
            }

            if (!/^IXTA-[0-9A-F]{12}$/.test(codigo)) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent = 'Introduce un código de invitación válido.';
                return;
            }

            const ruta = tipo === 'vecinos' ?
                '/circulo-vecinos/vincular' :
                '/circulo-confianza/vincular';

            boton.disabled = true;

            if (selector) {
                selector.disabled = true;
            }

            mensaje.style.color = '#252A30';
            mensaje.textContent = 'Vinculando contacto...';

            try {
                const respuesta = await fetch(API + ruta, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        codigo
                    })
                });

                const datos = await respuesta.json();

                if (!respuesta.ok) {
                    mensaje.style.color = '#9F2241';
                    mensaje.textContent = datos.errors?.codigo?.[0] ??
                        datos.message ??
                        'No se pudo vincular al contacto.';
                    return;
                }

                campo.value = '';
                mensaje.style.color = '#235B4E';
                mensaje.textContent = tipo === 'vecinos' ?
                    'Contacto vinculado al círculo de vecinos.' :
                    'Contacto vinculado al círculo de confianza.';

                if (tipo === 'confianza') {
                    await cargarContactosConfianza();
                }
            } catch (error) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent = 'No fue posible conectar con el servidor.';
            } finally {
                boton.disabled = false;

                if (selector) {
                    selector.disabled = false;
                }
            }
        }

        // Generar un código temporal de confianza.
        async function generarCodigoConfianza() {

            const boton = document.getElementById(
                'btnGenerarCodigoConfianza'
            );

            const resultado = document.getElementById(
                'resultadoCodigoConfianza'
            );

            const valor = document.getElementById(
                'valorCodigoConfianza'
            );

            const mensaje = document.getElementById(
                'mensajeCodigoConfianza'
            );

            boton.disabled = true;
            boton.textContent = 'Generando código...';

            resultado.style.display = 'none';
            valor.textContent = '';
            mensaje.textContent = '';

            try {

                const respuesta = await fetch(
                    API + '/circulo-confianza/codigo', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    }
                );

                const datos = await respuesta.json();

                if (!respuesta.ok) {

                    mensaje.style.color = '#9F2241';

                    mensaje.textContent =
                        datos.message ??
                        'No fue posible generar el código.';

                    return;
                }

                // Mostrar el código devuelto por Laravel.
                valor.textContent = datos.codigo;

                resultado.style.display = 'block';

            } catch (error) {

                console.error(
                    'Error generando código de confianza:',
                    error
                );

                mensaje.style.color = '#9F2241';

                mensaje.textContent =
                    'No fue posible conectar con el servidor.';

            } finally {

                boton.disabled = false;
                boton.textContent = 'Generar otro código';

            }
        }

        // Eliminar una vinculación de confianza.
        async function eliminarContactoConfianza(id, nombre, boton) {

            const confirmar = window.confirm(
                '¿Deseas eliminar a ' + nombre +
                ' de tu círculo de confianza? ' +
                'La vinculación se eliminará para ambos ciudadanos.'
            );

            if (!confirmar) {
                return;
            }

            boton.disabled = true;
            boton.textContent = 'Eliminando...';

            try {

                const respuesta = await fetch(
                    API + '/circulo-confianza/contactos/' + id, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    }
                );

                const datos = await respuesta.json();

                if (!respuesta.ok) {

                    alert(
                        datos.message ??
                        'No fue posible eliminar el contacto.'
                    );

                    return;
                }

                // Actualizar la lista después de eliminar.
                await cargarContactosConfianza();

            } catch (error) {

                console.error(
                    'Error eliminando contacto:',
                    error
                );

                alert('No fue posible conectar con el servidor.');

            } finally {

                boton.disabled = false;
                boton.textContent = 'Eliminar contacto';

            }
        }

        // Consultar los contactos confirmados del ciudadano.
        async function cargarContactosConfianza() {

            const lista = document.getElementById(
                'listaContactosConfianza'
            );

            lista.textContent = 'Cargando contactos...';

            try {

                const respuesta = await fetch(
                    API + '/circulo-confianza/contactos', {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!respuesta.ok) {

                    lista.textContent =
                        'No fue posible consultar tus contactos.';

                    return;
                }

                const datos = await respuesta.json();

                const contactos = datos.contactos ?? [];

                if (!Array.isArray(contactos)) {

                    lista.textContent =
                        'No fue posible mostrar tus contactos.';

                    return;
                }

                // Limpiar la lista anterior.
                lista.replaceChildren();

                if (contactos.length === 0) {
                    return;
                }

                // Crear una tarjeta por cada contacto.
                contactos.forEach(function(contacto) {

                    const tarjeta = document.createElement('div');

                    tarjeta.className = 'tarjeta-contacto-confianza';

                    const nombre = document.createElement('strong');

                    nombre.textContent =
                        contacto.nombre ?? 'Ciudadano';

                    const estado = document.createElement('p');

                    estado.textContent = 'Contacto confirmado';

                    tarjeta.appendChild(nombre);

                    tarjeta.appendChild(estado);

                    // Crear el botón para eliminar el contacto.
                    const botonEliminar = document.createElement('button');

                    botonEliminar.type = 'button';

                    botonEliminar.className = 'boton-eliminar-confianza';

                    botonEliminar.textContent = 'Eliminar contacto';

                    botonEliminar.addEventListener('click', function() {

                        eliminarContactoConfianza(
                            contacto.id_vinculo,
                            contacto.nombre,
                            botonEliminar
                        );

                    });

                    tarjeta.appendChild(botonEliminar);

                    lista.appendChild(tarjeta);

                });

            } catch (error) {

                console.error(
                    'Error consultando contactos:',
                    error
                );

                lista.textContent =
                    'No fue posible conectar con el servidor.';

            }
        }

        let consultaVecinosActual = 0;

        // Consultar los vecinos vinculados al ciudadano.
        async function cargarContactosVecinos() {
            const lista = document.getElementById('listaContactosVecinos');
            const consulta = ++consultaVecinosActual;

            lista.textContent = 'Cargando vecinos...';

            try {
                const respuesta = await fetch(
                    API + '/circulo-vecinos/contactos', {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    }
                );

                const datos = await respuesta.json();

                if (consulta !== consultaVecinosActual) {
                    return;
                }

                if (!respuesta.ok || !Array.isArray(datos.contactos)) {
                    lista.textContent = 'No fue posible consultar tus vecinos.';
                    return;
                }

                lista.replaceChildren();

                if (datos.contactos.length === 0) {
                    const mensaje = document.createElement('p');
                    mensaje.className = 'info';
                    mensaje.textContent = 'Todavía no tienes vecinos vinculados.';
                    lista.appendChild(mensaje);
                    return;
                }

                datos.contactos.forEach(function(vecino) {
                    const tarjeta = document.createElement('div');
                    tarjeta.className = 'tarjeta-contacto-confianza';

                    const nombre = document.createElement('strong');
                    nombre.textContent = vecino.nombre ?? 'Ciudadano';

                    const estado = document.createElement('p');
                    estado.textContent = 'Vecino vinculado';

                    const boton = document.createElement('button');
                    boton.type = 'button';
                    boton.className = 'boton-eliminar-confianza';
                    boton.textContent = 'Eliminar';

                    boton.setAttribute(
                        'aria-label',
                        'Eliminar vínculo con ' + (vecino.nombre ?? 'este vecino')
                    );

                    boton.addEventListener('click', function() {
                        eliminarContactoVecinos(
                            vecino.id_vinculo,
                            vecino.nombre,
                            boton
                        );
                    });

                    tarjeta.append(nombre, estado, boton);
                    lista.appendChild(tarjeta);
                });
            } catch (error) {
                if (consulta === consultaVecinosActual) {
                    lista.textContent = 'No fue posible conectar con el servidor.';
                }
            }
        }

        // Eliminar el vínculo de vecinos para ambas cuentas.
        async function eliminarContactoVecinos(id, nombre, boton) {
            if (boton.disabled) {
                return;
            }

            const confirmar = window.confirm(
                '¿Eliminar el vínculo con ' + (nombre ?? 'este vecino') +
                '? Se eliminará del círculo de vecinos de ambas cuentas.'
            );

            if (!confirmar) {
                return;
            }

            const tarjeta = boton.closest('.tarjeta-contacto-confianza');

            tarjeta.querySelector('.error-eliminar-vecino')?.remove();

            function mostrarError(texto) {
                const aviso = document.createElement('p');
                aviso.className = 'info error-eliminar-vecino';
                aviso.setAttribute('role', 'status');
                aviso.style.color = '#9F2241';
                aviso.style.gridColumn = '1 / -1';
                aviso.textContent = texto;
                tarjeta.appendChild(aviso);
            }

            boton.disabled = true;

            try {
                const respuesta = await fetch(
                    API + '/circulo-vecinos/contactos/' + encodeURIComponent(id), {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    }
                );

                const datos = await respuesta.json();

                if (!respuesta.ok) {
                    mostrarError(
                        respuesta.status >= 500 ?
                        'No fue posible eliminar el vínculo. Intenta nuevamente.' :
                        datos.message ?? 'No se pudo eliminar el vínculo.'
                    );
                    return;
                }

                await cargarContactosVecinos();
            } catch (error) {
                mostrarError('No fue posible conectar con el servidor.');
            } finally {
                boton.disabled = false;
            }
        }

        function abrirGrupoCiudadano(tipo) {
            if (!['confianza', 'vecinos'].includes(tipo)) {
                return;
            }

            const vistas = {
                confianza: 'vistaCirculoCiudadano',
                vecinos: 'vistaVecinosCiudadano'
            };

            navegarCiudadano('grupos');

            document.getElementById('vistaListadoGrupos')
                .style.display = 'none';

            document.getElementById(vistas[tipo])
                .style.display = 'block';

            if (tipo === 'confianza') {
                cargarContactosConfianza();
            } else {
                cargarContactosVecinos();
            }
        }

        function volverGruposCiudadano() {
            navegarCiudadano('grupos');
        }

        function volverCuentaCiudadano() {
            const cuenta = document.getElementById('panelCuentaCiudadano');

            Array.from(cuenta.children).forEach(function(elemento) {
                elemento.style.removeProperty('display');
            });

            document.getElementById('pantallaContenido').scrollTo({
                top: 0,
                behavior: 'auto'
            });
        }

        // Navegar entre Inicio, SOS y Cuenta.
        function navegarCiudadano(seccion) {
            const secciones = ['inicio', 'sos', 'grupos', 'historial', 'cuenta'];

            if (!secciones.includes(seccion)) {
                return;
            }

            const contenido = document.getElementById('pantallaContenido');

            contenido.classList.remove(
                'modo-cuenta',
                'modo-grupos',
                'modo-historial'
            );

            const modos = {
                cuenta: 'modo-cuenta',
                grupos: 'modo-grupos',
                historial: 'modo-historial'
            };

            if (modos[seccion]) {
                contenido.classList.add(modos[seccion]);
            }

            marcarPestanaCiudadano(seccion);

            if (seccion === 'cuenta') {
                volverCuentaCiudadano();
                cargarUsuario();
            }

            if (seccion === 'historial') {
                document.getElementById('vistaHistorialCiudadano')
                    .style.display = 'block';

                cargarHistorialCiudadano();
            }

            if (seccion === 'grupos') {
                document.getElementById('vistaListadoGrupos')
                    .style.display = 'block';

                document.querySelectorAll(
                    '#vistaCirculoCiudadano, #vistaVecinosCiudadano'
                ).forEach(function(vista) {
                    vista.style.display = 'none';
                });
            }

            if (seccion === 'inicio' || seccion === 'sos') {
                if (mapa) {
                    requestAnimationFrame(function() {
                        mapa.invalidateSize();
                    });
                }
            }

            if (seccion === 'sos') {
                requestAnimationFrame(function() {
                    document.getElementById('btnSOS').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                });
            } else {
                contenido.scrollTo({
                    top: 0,
                    behavior: 'auto'
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

            mapa.on('dragstart', function() {
                if (idNotificacionSosActiva !== null) {
                    seguirContactoSos = false;
                }
            });

        }

        async function mostrarNotificacionSos(id) {
            if (!/^\d+$/.test(String(id))) {
                return;
            }

            if (intervaloNotificacionSos !== null) {
                clearInterval(intervaloNotificacionSos);
            }

            idNotificacionSosActiva = Number(id);
            seguirContactoSos = true;

            navegarCiudadano('inicio');

            await actualizarNotificacionSos();

            if (idNotificacionSosActiva === Number(id)) {
                intervaloNotificacionSos = setInterval(
                    actualizarNotificacionSos,
                    5000
                );
            }
        }

        async function actualizarNotificacionSos() {
            const id = idNotificacionSosActiva;

            if (id === null) {
                return;
            }

            try {
                const respuesta = await fetch(
                    API + '/notificaciones-sos/' + id, {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    }
                );

                if (!respuesta.ok) {
                    throw new Error('No se pudo consultar la alerta del contacto.');
                }

                const datos = (await respuesta.json()).alerta;

                // Si se cerró la tarjeta durante la consulta, no volver a mostrarla.
                if (idNotificacionSosActiva !== id) {
                    return;
                }
                sessionStorage.removeItem('alerta_ixta_destino_sos');


                const latitud = Number(datos.ubicacion.latitud);
                const longitud = Number(datos.ubicacion.longitud);

                if (!Number.isFinite(latitud) || !Number.isFinite(longitud)) {
                    throw new Error('La ubicación de esta alerta no está disponible.');
                }

                document.getElementById('tituloNotificacionSos').textContent =
                    'Alerta SOS de ' + datos.nombre;

                document.getElementById('circuloNotificacionSos').textContent =
                    datos.circulo === 'CONFIANZA' ?
                    'Contacto de tu círculo de confianza' :
                    'Contacto de tu círculo de vecinos';

                document.getElementById('datosNotificacionSos').textContent =
                    'Folio: ' + datos.folio +
                    ' · Tipo: ' + (datos.tipo ?? 'No especificado') +
                    ' · Estado: ' + datos.estado;

                document.getElementById('ubicacionNotificacionSos').textContent =
                    'El marcador violeta muestra su última ubicación registrada.';

                document.getElementById('tarjetaNotificacionSos').hidden = false;

                const punto = [latitud, longitud];

                if (!marcadorContactoSos) {
                    marcadorContactoSos = L.circleMarker(punto, {
                        radius: 12,
                        color: '#ffffff',
                        weight: 3,
                        fillColor: '#702a9c',
                        fillOpacity: 1
                    }).addTo(mapa);

                    const nombre = document.createElement('span');
                    nombre.textContent = datos.nombre;
                    marcadorContactoSos.bindPopup(nombre);
                } else {
                    marcadorContactoSos.setLatLng(punto);
                }

                if (seguirContactoSos) {
                    mapa.setView(punto, 16);
                }

                if (!['NUEVA', 'EN_CAMINO', 'EN_SITIO']
                    .includes(datos.estado)) {
                    clearInterval(intervaloNotificacionSos);
                    intervaloNotificacionSos = null;

                    document.getElementById('ubicacionNotificacionSos')
                        .textContent =
                        'La alerta terminó. El marcador muestra la última ubicación registrada.';
                }
            } catch (error) {
                if (idNotificacionSosActiva !== id) {
                    return;
                }

                document.getElementById('tarjetaNotificacionSos').hidden = false;
                document.getElementById('ubicacionNotificacionSos')
                    .textContent = error.message;
            }
        }

        function centrarNotificacionSos() {
            if (!marcadorContactoSos) {
                return;
            }

            seguirContactoSos = true;
            mapa.setView(marcadorContactoSos.getLatLng(), 16);
        }

        function cerrarVistaNotificacionSos() {
            idNotificacionSosActiva = null;
            seguirContactoSos = false;

            if (intervaloNotificacionSos !== null) {
                clearInterval(intervaloNotificacionSos);
                intervaloNotificacionSos = null;
            }

            if (marcadorContactoSos) {
                mapa.removeLayer(marcadorContactoSos);
                marcadorContactoSos = null;
            }

            document.getElementById('tarjetaNotificacionSos').hidden = true;

            const direccion = new URL(window.location.href);
            direccion.searchParams.delete('notificacion');

            history.replaceState(
                null,
                '',
                direccion.pathname + direccion.search
            );

            if (latitudActual !== null && longitudActual !== null) {
                mapa.setView([latitudActual, longitudActual], 16);
            }
        }

        let consultaUsuario = null;

        function actualizarUsuarioEnCuenta() {
            const cuentaAbierta = document.getElementById('pantallaContenido')
                .classList.contains('modo-cuenta');

            if (cuentaAbierta && !document.hidden) {
                cargarUsuario();
            }
        }

        setInterval(actualizarUsuarioEnCuenta, 5000);
        document.addEventListener('visibilitychange', actualizarUsuarioEnCuenta);

        async function cargarUsuario() {
            const botonIne = document.getElementById('btnReenviarIne');

            if (consultaUsuario || botonIne.disabled) {
                return;
            }

            const consulta = new AbortController();
            consultaUsuario = consulta;

            try {
                const respuesta = await fetch(API + '/usuario', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    cache: 'no-store',
                    signal: consulta.signal
                });

                if (consulta.signal.aborted) {
                    return;
                }

                if (respuesta.status === 401) {
                    sessionStorage.removeItem('alerta_ixta_token');
                    window.location.href = '/';
                    return;
                }

                if (!respuesta.ok) {
                    console.warn('No se pudo consultar el usuario:', respuesta.status);
                    return;
                }

                const datos = await respuesta.json();
                const usuario = datos?.usuario ?? datos?.user ?? datos;
                const estadoIdentidad = usuario?.estatus_verificacion;
                const sinFotoIne = estadoIdentidad === 'PENDIENTE' && usuario.tiene_foto_ine === false;

                const estadosIdentidad = {
                    PENDIENTE: sinFotoIne ? 'INE sin subir' : 'Pendiente de revisión',
                    VERIFICADA: 'Verificada',
                    RECHAZADA: 'Foto rechazada'
                };
                const estadoNuevo = estadosIdentidad[estadoIdentidad];

                if (consulta.signal.aborted || !estadoNuevo) {
                    return;
                }

                const estadoEnPantalla = document.getElementById('estadoVerificacionCiudadano');

                if (estadoEnPantalla.textContent.trim() !== estadoNuevo) {
                    document.getElementById('mensajeReenvioIne').textContent = '';
                    estadoEnPantalla.textContent = estadoNuevo;
                }

                const motivoIne = document.getElementById('motivoRechazoIne');
                motivoIne.hidden = estadoIdentidad !== 'RECHAZADA' || !usuario.ine_motivo_rechazo;
                motivoIne.textContent = motivoIne.hidden ? '' : 'Motivo: ' + usuario.ine_motivo_rechazo;

                const formularioIne = document.getElementById('formReenviarIne');
                formularioIne.hidden = !sinFotoIne && estadoIdentidad !== 'RECHAZADA';
                formularioIne.querySelector('label').textContent =
                    sinFotoIne ? 'Foto del INE' : 'Nueva foto del INE';

                actualizarBotonEnvioIne();

                if (usuario.name) {
                    document.getElementById('saludo').textContent = 'Hola, ' + usuario.name;
                    document.getElementById('nombreCuentaCiudadano').textContent = usuario.name;
                }
            } catch (error) {
                if (!consulta.signal.aborted) {
                    console.error('Error al consultar usuario:', error);
                }
            } finally {
                consultaUsuario = null;
            }
        }

        function actualizarBotonEnvioIne() {
            const foto = document.getElementById('nuevaFotoIne').files[0];
            const boton = document.getElementById('btnReenviarIne');

            boton.textContent = 'Enviar';
            boton.style.display = foto ? 'block' : 'none';
        }

        document.getElementById('formReenviarIne')
            .addEventListener('change', actualizarBotonEnvioIne);

        document.getElementById('formReenviarIne')
            .addEventListener('reset', () => {
                document.getElementById('btnReenviarIne').style.display = 'none';
            });

        async function reenviarFotoIne(evento) {
            evento.preventDefault();

            const formulario = evento.currentTarget;
            const boton = document.getElementById('btnReenviarIne');
            const mensaje = document.getElementById('mensajeReenvioIne');
            const foto = document.getElementById('nuevaFotoIne').files[0];

            if (boton.disabled || !foto) {
                return;
            }

            if (foto.size > 2 * 1024 * 1024) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent = 'Selecciona una imagen JPG o PNG de hasta 2 MB.';
                return;
            }

            boton.disabled = true;
            consultaUsuario?.abort();
            mensaje.style.color = '#252A30';
            mensaje.textContent = 'Enviando foto...';

            try {
                const respuesta = await fetch(API + '/usuario/ine/reenviar', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: new FormData(formulario)
                });

                if (respuesta.status === 401) {
                    sessionStorage.removeItem('alerta_ixta_token');
                    window.location.href = '/';
                    return;
                }

                const datos = await respuesta.json();

                if (!respuesta.ok) {
                    mensaje.style.color = '#9F2241';
                    mensaje.textContent =
                        datos.errors?.ine_foto?.[0] ??
                        datos.message ??
                        'No se pudo enviar la foto.';
                    return;
                }

                formulario.reset();
                formulario.hidden = true;
                document.getElementById('estadoVerificacionCiudadano').textContent = 'Pendiente de revisión';
                document.getElementById('motivoRechazoIne').hidden = true;

                mensaje.style.color = '#235B4E';
                mensaje.textContent = datos.message;
            } catch (error) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent = 'No fue posible conectar con el servidor.';
            } finally {
                boton.disabled = false;
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
        let idGPSCiudadano = null;
        let ultimaLecturaGPSCiudadano = null;

        // Obtener y mantener actualizada la ubicación del ciudadano.
        function obtenerUbicacion() {

            const texto = document.getElementById('textoUbicacion');

            // Respetar la ubicación seleccionada manualmente.
            if (ubicacionManualCiudadano) {
                return;
            }

            if (!navigator.geolocation) {
                texto.textContent = 'Ubicación no disponible';
                return;
            }

            // Evitar varios seguimientos si se llama otra vez a esta función.
            if (idGPSCiudadano !== null) {
                navigator.geolocation.clearWatch(idGPSCiudadano);
                idGPSCiudadano = null;
            }

            texto.textContent = '📍 Obteniendo ubicación...';

            idGPSCiudadano = navigator.geolocation.watchPosition(

                function(posicion) {

                    if (ubicacionManualCiudadano) {
                        return;
                    }

                    latitudActual = posicion.coords.latitude;
                    longitudActual = posicion.coords.longitude;
                    precisionActual = posicion.coords.accuracy;
                    ultimaLecturaGPSCiudadano = posicion.timestamp;

                    texto.textContent =
                        '📍 Ubicación disponible - precisión aproximada: ' +
                        Math.round(precisionActual) +
                        ' metros';

                    actualizarMarcadorCiudadano(
                        latitudActual,
                        longitudActual
                    );
                    actualizarUbicacionCiudadano();
                },

                function(error) {

                    if (ubicacionManualCiudadano) {
                        return;
                    }

                    if (error.code === 1) {
                        texto.textContent = 'Permiso de ubicación denegado.';
                    } else if (error.code === 2) {
                        texto.textContent = 'No se pudo determinar la ubicación.';
                    } else if (error.code === 3) {
                        texto.textContent =
                            'La ubicación tardó demasiado. Esperando una nueva lectura...';
                    } else {
                        texto.textContent = 'No se pudo obtener la ubicación.';
                    }
                },

                {
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 0
                }
            );
        }

        // Permitir corregir manualmente la ubicación durante las pruebas.
        function corregirUbicacionManual() {

            // Enfocar el mapa sin desplazar el panel.
            mapa.getContainer().focus({
                preventScroll: true
            });

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


            // Mientras vemos la alerta de un contacto, el GPS propio
            // actualiza nuestro marcador sin quitar el mapa de esa alerta.
            if (idNotificacionSosActiva === null) {
                mapa.setView(
                    [
                        latitud,
                        longitud
                    ],
                    16
                );
            }

        }

        async function enviarUbicacionCiudadano() {

            if (
                idAlertaActual === null ||
                !['NUEVA', 'EN_CAMINO', 'EN_SITIO'].includes(estadoAlertaActual) ||
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
                idAlertaActual === null ||
                latitudActual === null ||
                longitudActual === null
            ) {
                return;
            }

            // En modo automático, esperar a recibir una ubicación.
            if (
                !ubicacionManualCiudadano &&
                ultimaLecturaGPSCiudadano === null
            ) {
                return;
            }

            // Enviar la última posición conocida.
            // watchPosition actualizará las coordenadas cuando reciba otra.
            enviarUbicacionCiudadano();
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

                estadoAlertaActual = datos.alerta.estado;

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

        async function confirmarCancelacion(evento) {
            evento.preventDefault();

            const idAlerta = idAlertaActual;
            const boton = document.getElementById('btnConfirmarCancelacion');
            const campo = document.getElementById('codigoCancelacion');
            const mensaje = document.getElementById('mensajeConfirmacionCancelacion');

            if (idAlerta === null || boton.disabled) {
                return;
            }

            boton.disabled = true;
            mensaje.textContent = 'Verificando código...';

            try {
                const respuesta = await fetch(
                    API + '/mis-alertas/' + idAlerta + '/confirmar-cancelacion', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            codigo: campo.value
                        })
                    }
                );

                const datos = await respuesta.json();

                if (!respuesta.ok) {
                    mensaje.textContent =
                        datos.message ?? 'No fue posible confirmar la cancelación.';

                    if (Number.isInteger(datos.intentos_restantes)) {
                        mensaje.textContent +=
                            ' Intentos restantes: ' + datos.intentos_restantes + '.';
                    }

                    return;
                }

                if (idAlertaActual === idAlerta) {
                    mostrarEstado({
                        ...datos.alerta,
                        tipo: document.getElementById('tipoActual').textContent.trim()
                    });

                    const mensajeAlerta = document.getElementById('mensajeAlerta');
                    mensajeAlerta.style.color = '#235B4E';
                    mensajeAlerta.textContent = datos.message;
                }
            } catch (error) {
                mensaje.textContent = 'No fue posible conectar con el servidor.';
            } finally {
                boton.disabled = false;
            }
        }

        async function cancelarAlerta() {

            if (!idAlertaActual || enviandoSolicitudCancelacion) {
                return;
            }

            const idAlertaSolicitud = idAlertaActual;

            const motivoEscrito = window.prompt(
                'Escribe el motivo de la solicitud. La alerta seguirá activa mientras el operador la revisa.'
            );

            if (motivoEscrito === null) {
                return;
            }

            const motivo = motivoEscrito.trim();
            const mensaje = document.getElementById('mensajeAlerta');
            const boton = document.getElementById('btnCancelarAlerta');

            if (motivo.length === 0 || motivo.length > 1000) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent =
                    'Escribe un motivo de entre 1 y 1000 caracteres.';
                return;
            }

            enviandoSolicitudCancelacion = true;
            boton.disabled = true;
            boton.textContent = 'Enviando solicitud...';

            try {

                const respuesta = await fetch(
                    API + '/mis-alertas/' + idAlertaSolicitud + '/cancelar', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            motivo: motivo
                        })
                    }
                );

                const datos = await respuesta.json();

                if (!respuesta.ok) {
                    mensaje.style.color = '#9F2241';
                    mensaje.textContent =
                        datos.message ?? 'No fue posible enviar la solicitud.';
                    return;
                }

                mensaje.textContent = '';

            } catch (error) {

                console.error(
                    'Error enviando solicitud de cancelación:',
                    error
                );

                mensaje.style.color = '#9F2241';
                mensaje.textContent =
                    'No fue posible conectar con el servidor.';

            } finally {

                enviandoSolicitudCancelacion = false;
                await consultarEstadoAlerta();

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


                    estadoAlertaActual = alertas[0].estado;
                    await consultarEstadoAlerta();

                    if (['CERRADA', 'CANCELADA'].includes(estadoAlertaActual)) {
                        idAlertaActual = null;
                        return;
                    }

                    iniciarActualizacionAutomatica();

                    if (['NUEVA', 'EN_CAMINO', 'EN_SITIO'].includes(estadoAlertaActual)) {
                        iniciarSeguimientoUbicacionCiudadano();
                        actualizarUbicacionCiudadano();
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

        let idAtencionConfirmadaCiudadano = null;

        let temporizadorQrAtencion = null;

        function mostrarConfirmacionAtencion(alerta) {
            const boton = document.getElementById('btnConfirmarAtencionCiudadano');
            const mensaje = document.getElementById('mensajeConfirmacionAtencion');
            const panel = document.getElementById('panelQrAtencion');
            const imagen = document.getElementById('imagenQrAtencion');
            const vigencia = document.getElementById('vigenciaQrAtencion');
            const idAlerta = String(alerta.id_alerta);

            clearTimeout(temporizadorQrAtencion);
            temporizadorQrAtencion = null;

            if (mensaje.dataset.idAlerta !== idAlerta) {
                mensaje.dataset.idAlerta = idAlerta;
                mensaje.textContent = '';
            }

            if (alerta.atencion_confirmada === true) {
                idAtencionConfirmadaCiudadano = idAlerta;
            }

            if (
                qrAtencionActual &&
                qrAtencionActual.id_alerta !== idAlerta
            ) {
                qrAtencionActual = null;
                imagen.replaceChildren();
            }

            if (alerta.estado !== 'EN_SITIO') {
                qrAtencionActual = null;
                imagen.replaceChildren();
                panel.hidden = true;
                boton.style.display = 'none';

                mensaje.style.color = '#235B4E';
                mensaje.textContent = alerta.estado === 'CERRADA' ?
                    'Atención finalizada. La alerta quedó cerrada.' :
                    '';

                mensaje.hidden = !mensaje.textContent;
                return;
            }

            boton.disabled = generandoQrAtencion;
            boton.textContent = generandoQrAtencion ?
                'Generando QR...' :
                qrAtencionActual ?
                'Generar nuevo QR' :
                'Generar QR de atención';

            if (!qrAtencionActual) {
                panel.hidden = true;
                boton.style.display = 'block';
                mensaje.hidden = !mensaje.textContent;
                return;
            }

            const vencimiento = Date.parse(qrAtencionActual.expira_at);
            const vigente = vencimiento > Date.now();

            panel.hidden = false;
            boton.style.display = vigente ? 'none' : 'block';

            if (!vigente) {
                imagen.replaceChildren();
                vigencia.textContent = 'Este QR venció. Genera uno nuevo.';
            } else {
                vigencia.textContent =
                    'Vence a las ' + new Date(vencimiento).toLocaleTimeString('es-MX');

                temporizadorQrAtencion = setTimeout(function() {
                    if (String(idAlertaActual) === idAlerta) {
                        mostrarConfirmacionAtencion({
                            id_alerta: idAlertaActual,
                            estado: estadoAlertaActual
                        });
                    }
                }, vencimiento - Date.now() + 100);
            }

            mensaje.hidden = !mensaje.textContent;
        }

        let generandoQrAtencion = false;
        let qrAtencionActual = null;

        async function generarQrAtencionCiudadano() {
            const idAlerta = idAlertaActual;
            const boton = document.getElementById('btnConfirmarAtencionCiudadano');
            const mensaje = document.getElementById('mensajeConfirmacionAtencion');
            const imagen = document.getElementById('imagenQrAtencion');

            if (
                !idAlerta ||
                generandoQrAtencion ||
                estadoAlertaActual !== 'EN_SITIO'
            ) {
                return;
            }

            mensaje.hidden = false;

            if (typeof QRCode === 'undefined') {
                mensaje.style.color = '#9F2241';
                mensaje.textContent = 'No se pudo cargar el generador del QR. Recarga la página.';
                return;
            }

            generandoQrAtencion = true;
            boton.disabled = true;
            mensaje.style.color = '#252A30';
            mensaje.textContent = 'Generando QR...';

            try {
                const respuesta = await fetch(
                    API + '/mis-alertas/' + idAlerta + '/qr-atencion', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    }
                );

                const datos = await respuesta.json();

                if (
                    idAlertaActual !== idAlerta ||
                    estadoAlertaActual !== 'EN_SITIO'
                ) {
                    return;
                }

                if (!respuesta.ok) {
                    mensaje.style.color = '#9F2241';
                    mensaje.textContent = datos.message ?? 'No se pudo generar el QR.';
                    return;
                }

                const vencimiento = Date.parse(datos.qr?.expira_at);

                if (
                    typeof datos.qr?.contenido !== 'string' ||
                    String(datos.qr.id_alerta) !== String(idAlerta) ||
                    !Number.isFinite(vencimiento) ||
                    vencimiento <= Date.now()
                ) {
                    throw new Error('La respuesta del QR no es válida.');
                }

                imagen.replaceChildren();

                new QRCode(imagen, {
                    text: datos.qr.contenido,
                    width: 200,
                    height: 200,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });

                imagen.removeAttribute('title');

                qrAtencionActual = {
                    id_alerta: String(idAlerta),
                    folio: datos.qr.folio,
                    expira_at: datos.qr.expira_at
                };

                document.getElementById('folioQrAtencion').textContent =
                    'Folio: ' + datos.qr.folio;

                document.getElementById('vigenciaQrAtencion').textContent =
                    'Vence a las ' + new Date(vencimiento).toLocaleTimeString('es-MX');

                document.getElementById('panelQrAtencion').hidden = false;

                mensaje.textContent = '';
                mensaje.hidden = true;
            } catch (error) {
                if (
                    idAlertaActual === idAlerta &&
                    estadoAlertaActual === 'EN_SITIO'
                ) {
                    mensaje.style.color = '#9F2241';
                    mensaje.textContent = 'No fue posible mostrar el QR. Intenta nuevamente.';
                    mensaje.hidden = false;
                }
            } finally {
                generandoQrAtencion = false;
                boton.disabled = false;

                if (idAlertaActual) {
                    mostrarConfirmacionAtencion({
                        id_alerta: idAlertaActual,
                        estado: estadoAlertaActual
                    });
                }
            }
        }


        // Mostrar estado de la alerta.
        function mostrarEstado(alerta) {

            if (alerta.id_alerta !== idAlertaActual) {
                return;
            }

            mostrarConfirmacionAtencion(alerta);

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


            estadoAlertaActual = alerta.estado;

            document.getElementById('estado').textContent =
                alerta.estado === 'PENDIENTE_CONFIRMACION' ?
                'Pendiente de confirmación' :
                alerta.estado;

            if (alerta.estado === 'PENDIENTE_CONFIRMACION') {
                detenerSeguimientoUbicacionCiudadano();
            }

            const btnCancelarAlerta =
                document.getElementById('btnCancelarAlerta');

            const estadoSolicitud =
                document.getElementById('estadoSolicitudCancelacion');

            const alertaActiva = ['NUEVA', 'EN_CAMINO', 'EN_SITIO'].includes(alerta.estado);
            const puedeCancelar = alertaActiva &&
                idAtencionConfirmadaCiudadano !== String(alerta.id_alerta);

            const solicitud = alerta.solicitud_cancelacion;

            const codigoVigente =
                solicitud?.estado === 'CODIGO_GENERADO' &&
                Date.parse(solicitud.codigo_expira_at) > Date.now();

            const formulario = document.getElementById('formConfirmarCancelacion');
            formulario.hidden = !puedeCancelar || !codigoVigente;

            if (formulario.hidden) {
                formulario.reset();
                document.getElementById('mensajeConfirmacionCancelacion').textContent = '';
            }

            const solicitudPendiente = Boolean(
                solicitud && ['PENDIENTE', 'CODIGO_GENERADO'].includes(solicitud.estado)
            );

            btnCancelarAlerta.style.display =
                puedeCancelar ? 'block' : 'none';

            btnCancelarAlerta.disabled =
                enviandoSolicitudCancelacion || solicitudPendiente;

            btnCancelarAlerta.textContent = enviandoSolicitudCancelacion ?
                'Enviando solicitud...' :
                (solicitudPendiente ?
                    'Solicitud en proceso' :
                    'Solicitar cancelación');

            estadoSolicitud.textContent = '';

            if (puedeCancelar && solicitud) {

                if (solicitud.estado === 'PENDIENTE') {
                    estadoSolicitud.textContent =
                        'El operador revisará tu solicitud. La atención continúa activa.';

                } else if (solicitud.estado === 'CODIGO_GENERADO') {
                    estadoSolicitud.textContent = codigoVigente ?
                        'Ingresa el código que te dio el operador para confirmar la cancelación.' :
                        'El código venció. Contacta al operador para obtener uno nuevo.';

                } else if (solicitud.estado === 'RECHAZADA') {
                    estadoSolicitud.textContent =
                        'El operador rechazó la solicitud. La atención continúa activa.';
                }
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


            actualizarPoliciaEnMapa(
                alertaActiva ? alerta : {
                    policia: null
                }
            );

            if (alerta.estado === 'CERRADA' || alerta.estado === 'CANCELADA') {
                clearInterval(intervaloEstado);
                intervaloEstado = null;

                detenerSeguimientoUbicacionCiudadano();
                idAlertaActual = null;
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

                if ('serviceWorker' in navigator) {
                    const registro = await navigator.serviceWorker.getRegistration('/');
                    const suscripcion = await registro?.pushManager.getSubscription();

                    if (suscripcion) {
                        await fetch(API + '/suscripciones-push', {
                            method: 'DELETE',
                            headers: {
                                'Authorization': 'Bearer ' + token,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                endpoint: suscripcion.endpoint
                            })
                        });

                        await suscripcion.unsubscribe();
                    }
                }
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