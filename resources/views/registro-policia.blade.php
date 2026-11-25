<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ALERTA IXTA - Registro policial</title>

    <link rel="stylesheet" href="{{ asset('css/alerta.css') }}?v=4">

    <style>
        .registro-policial [hidden] {
            display: none !important;
        }

        .registro-policial h2 {
            margin: 0 0 10px;
            color: #222222;
            font-size: 24px;
        }

        .registro-policial .registro-introduccion {
            margin: 0 0 26px;
            color: #62676C;
            font-size: 14px;
            line-height: 1.6;
        }

        .registro-policial fieldset {
            min-width: 0;
            margin: 0 0 24px;
            padding: 0 0 10px;
            border: 0;
            border-bottom: 1px solid #E5DDE0;
        }

        .registro-policial legend {
            margin-bottom: 18px;
            padding: 0;
            color: #222222;
            font-size: 16px;
            font-weight: 700;
        }

        .registro-policial fieldset:disabled {
            opacity: 0.65;
        }

        .registro-policial .registro-fila {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .registro-policial .campo input:focus {
            outline: 2px solid #9F2241;
            outline-offset: 2px;
        }

        .registro-policial .campo input[aria-invalid="true"] {
            border-color: #9F2241;
        }

        .registro-policial .registro-ayuda {
            display: block;
            margin-top: 6px;
            color: #62676C;
            font-size: 12px;
            line-height: 1.5;
        }

        .registro-policial [data-error]:empty,
        #mensajeRegistroPolicia:empty {
            display: none;
        }

        .registro-policial .boton {
            background: #691C32;
            color: #FFFFFF;
            font-weight: 600;
        }

        .registro-policial .boton:hover:not(:disabled) {
            background: #9F2241;
        }

        .registro-policial .boton:disabled {
            opacity: 0.65;
            cursor: wait;
        }

        .registro-policial .registro-pie {
            margin: 24px 0 10px;
            text-align: center;
            font-size: 13px;
        }

        .registro-policial .registro-pie a {
            color: #691C32;
            font-weight: 600;
        }

        #mensajeRegistroPolicia {
            margin: 0 0 20px;
            color: #691C32;
            font-size: 14px;
            line-height: 1.5;
        }

        #registroPoliciaExitoso {
            padding: 20px;
            border: 1px solid #E5DDE0;
            border-radius: 12px;
            background: #FAF8F9;
        }

        #registroPoliciaExitoso h3 {
            margin: 0 0 12px;
            font-size: 18px;
        }

        #registroPoliciaExitoso .boton {
            margin-top: 20px;
        }

        @media (max-width: 360px) {
            .registro-policial .registro-fila {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>

<body class="registro-policial">
    <div class="contenedor contenedor-app-movil">
        <div class="app app-movil">

            <header class="app-cabecera">
                <img
                    src="{{ asset('images/logoIxtapaluca2025.png') }}"
                    alt="Gobierno de Ixtapaluca"
                    class="app-cabecera-logo">

                <div class="app-cabecera-texto">
                    <h1 class="app-cabecera-titulo">ALERTA IXTA</h1>
                    <p class="app-cabecera-subtitulo">
                        Sistema Municipal de Alerta
                    </p>
                </div>

                <div aria-hidden="true"></div>
            </header>

            <main class="app-contenido">
                <h2>Registro policial</h2>

                <p id="introduccionRegistroPolicia" class="registro-introduccion">
                    Completa tus datos para crear tu cuenta policial.
                </p>

                <p
                    id="mensajeRegistroPolicia"
                    role="status"
                    tabindex="-1"></p>

                <form
                    id="formRegistroPolicia"
                    method="POST"
                    action="{{ url('/api/personal/registro-policia') }}"
                    onsubmit="enviarSolicitudRegistroPolicial(event)">

                    <fieldset>
                        <legend>Datos personales</legend>

                        <div class="campo">
                            <label for="registroNombrePolicia">
                                Nombre completo
                            </label>
                            <input
                                id="registroNombrePolicia"
                                name="nombre"
                                type="text"
                                autocomplete="name"
                                maxlength="100"
                                placeholder="Ingresa tu nombre completo"
                                required>
                            <small class="error" data-error="nombre"></small>
                        </div>

                        <div class="campo">
                            <label for="registroCurpPolicia">CURP</label>
                            <input
                                id="registroCurpPolicia"
                                name="curp"
                                type="text"
                                autocomplete="off"
                                minlength="18"
                                maxlength="18"
                                style="text-transform: uppercase;"
                                placeholder="Ingresa tu CURP"
                                required>
                            <small class="error" data-error="curp"></small>
                        </div>

                        <div class="campo">
                            <label for="registroTelefonoPolicia">Teléfono</label>
                            <input
                                id="registroTelefonoPolicia"
                                name="telefono"
                                type="tel"
                                inputmode="numeric"
                                autocomplete="tel"
                                minlength="10"
                                maxlength="10"
                                pattern="[0-9]{10}"
                                placeholder="10 dígitos"
                                required>
                            <small class="error" data-error="telefono"></small>
                        </div>

                        <div class="campo">
                            <label for="registroEmailPolicia">Correo electrónico</label>
                            <input
                                id="registroEmailPolicia"
                                name="email"
                                type="email"
                                autocomplete="email"
                                maxlength="150"
                                placeholder="Ingresa tu correo electrónico"
                                required>
                            <small class="error" data-error="email"></small>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Datos laborales</legend>

                        <div class="registro-fila">
                            <div class="campo">
                                <label for="registroEmpleadoPolicia">
                                    Número de empleado
                                </label>
                                <input
                                    id="registroEmpleadoPolicia"
                                    name="numero_empleado"
                                    type="text"
                                    maxlength="30"
                                    placeholder="Número de empleado"
                                    required>
                                <small
                                    class="error"
                                    data-error="numero_empleado"></small>
                            </div>

                            <div class="campo">
                                <label for="registroPlacaPolicia">
                                    Placa
                                </label>
                                <input
                                    id="registroPlacaPolicia"
                                    name="numero_placa"
                                    type="text"
                                    maxlength="30"
                                    placeholder="Número de placa"
                                    required>
                                <small
                                    class="error"
                                    data-error="numero_placa"></small>
                            </div>
                        </div>

                        <div class="campo">
                            <label for="registroCuipPolicia">CUIP</label>
                            <input
                                id="registroCuipPolicia"
                                name="cuip"
                                type="text"
                                maxlength="50"
                                placeholder="Clave Única de Identificación Permanente"
                                required>
                            <small class="error" data-error="cuip"></small>
                        </div>

                        <div class="campo">
                            <label for="registroAdscripcionPolicia">
                                Área de adscripción
                            </label>
                            <input
                                id="registroAdscripcionPolicia"
                                name="adscripcion"
                                type="text"
                                maxlength="120"
                                placeholder="Área o agrupamiento al que perteneces"
                                required>
                            <small class="error" data-error="adscripcion"></small>
                        </div>

                        <div class="campo">
                            <label for="registroPatrullaPolicia">
                                Unidad asignada
                            </label>
                            <input
                                id="registroPatrullaPolicia"
                                name="numero_patrulla"
                                type="text"
                                maxlength="30"
                                placeholder="Ingresa tu unidad asignada"
                                required>
                            <small
                                class="error"
                                data-error="numero_patrulla"></small>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Datos de acceso</legend>

                        <div class="campo">
                            <label for="registroPasswordPolicia">Contraseña</label>
                            <input
                                id="registroPasswordPolicia"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required>
                            <small class="registro-ayuda">
                                Al menos 8 caracteres, con letras y números.
                            </small>
                            <small class="error" data-error="password"></small>
                        </div>

                        <div class="campo">
                            <label for="registroConfirmacionPolicia">
                                Confirmar contraseña
                            </label>
                            <input
                                id="registroConfirmacionPolicia"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required>
                            <small
                                class="error"
                                data-error="password_confirmation"></small>
                        </div>
                    </fieldset>

                    <button
                        id="btnEnviarRegistroPolicia"
                        type="submit"
                        class="boton">
                        Registrarme
                    </button>

                    <p class="registro-pie">
                        ¿Ya tienes cuenta?
                        <a href="{{ route('policia') }}">Iniciar sesión</a>
                    </p>
                </form>

                <div id="registroPoliciaExitoso" hidden role="status">
                    <h3 id="tituloResultadoRegistroPolicia">Revisa tu correo</h3>

                    <p id="textoResultadoRegistroPolicia"></p>

                    <a href="{{ route('policia') }}" class="boton">
                        Ir al acceso policial
                    </a>
                </div>

            </main>
        </div>
    </div>

    <script>
        async function enviarSolicitudRegistroPolicial(evento) {
            evento.preventDefault();

            const formulario = evento.currentTarget;
            const boton = document.getElementById('btnEnviarRegistroPolicia');

            if (boton.disabled) return;

            const mensaje = document.getElementById('mensajeRegistroPolicia');
            const avisos = formulario.querySelectorAll('[data-error]');
            const grupos = formulario.querySelectorAll('fieldset');
            const datos = Object.fromEntries(new FormData(formulario));

            let primerError = null;

            avisos.forEach((aviso) => {
                aviso.textContent = '';
            });

            formulario.querySelectorAll('[aria-invalid]').forEach((campo) => {
                campo.removeAttribute('aria-invalid');
                campo.removeAttribute('aria-describedby');
            });

            boton.disabled = true;
            boton.textContent = 'Enviando…';

            grupos.forEach((grupo) => {
                grupo.disabled = true;
            });

            mensaje.style.color = '#62676C';
            mensaje.textContent = 'Enviando solicitud…';

            try {
                const respuesta = await fetch(formulario.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(datos)
                });

                const resultado = await respuesta.json().catch(() => null);

                if (!respuesta.ok) {
                    mensaje.style.color = '#9F2241';

                    if (respuesta.status === 422) {
                        const errores = resultado?.errors ?? {};

                        avisos.forEach((aviso) => {
                            const nombre = aviso.dataset.error;
                            const error = errores[nombre]?.[0];

                            if (!error) return;

                            aviso.textContent = error;
                            aviso.id = 'error-registro-policia-' + nombre;

                            const campo = formulario.elements.namedItem(nombre);

                            if (campo instanceof HTMLInputElement) {
                                campo.setAttribute('aria-invalid', 'true');
                                campo.setAttribute('aria-describedby', aviso.id);
                                primerError ??= campo;
                            }
                        });

                        mensaje.textContent = primerError ?
                            'Revisa los campos marcados.' :
                            resultado?.message ?? 'Revisa los datos de la solicitud.';
                    } else if (respuesta.status === 429) {
                        mensaje.textContent =
                            'Has enviado varias solicitudes. Espera un minuto y vuelve a intentar.';
                    } else {
                        mensaje.textContent =
                            'No se pudo confirmar el registro. Intenta nuevamente más tarde.';
                    }

                    return;
                }

                if (!resultado?.personal?.id_operador) {
                    mensaje.style.color = '#9F2241';
                    mensaje.textContent =
                        'No se pudo confirmar el registro. Revisa tu solicitud antes de volver a enviarla.';
                    return;
                }

                formulario.hidden = true;
                document.getElementById('introduccionRegistroPolicia').hidden = true;
                mensaje.textContent = '';

                document.getElementById('tituloResultadoRegistroPolicia').textContent =
                    resultado.correo_enviado ?
                    'Revisa tu correo' :
                    'Tu cuenta fue registrada';

                document.getElementById('textoResultadoRegistroPolicia').textContent =
                    resultado.correo_enviado ?
                    'Enviamos un enlace a tu correo. Presiona "Verificar mi correo" y después inicia sesión con tu CUIP y contraseña.' :
                    'No pudimos enviar el correo de verificación. No repitas el registro; podrás solicitar otro enlace desde el acceso policial.';

                document.getElementById('registroPoliciaExitoso').hidden = false;
            } catch (error) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent =
                    'No se pudo confirmar el envío. Revisa la conexión e intenta nuevamente.';
            } finally {
                grupos.forEach((grupo) => {
                    grupo.disabled = false;
                });

                boton.disabled = false;
                boton.textContent = 'Registrarme';

                if (primerError) {
                    primerError.focus();
                } else if (mensaje.textContent) {
                    mensaje.focus();
                }
            }
        }
    </script>
</body>

</html>