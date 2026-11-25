<!DOCTYPE html>
<html lang="es">

<style>
    .app .boton.boton-login {
        display: block;
        width: 270px;
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
        background-color: #691C32 !important;
        color: white;
    }

    .app .boton.boton-login:hover {
        background-color: #9F2241 !important;
    }

    .app #btnReenviarCorreo {
        margin-top: 14px;
        font-size: 14px;
    }

    .app #btnReenviarCorreo[hidden] {
        display: none !important;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ALERTA IXTA - Inicio de sesión</title>

    <link rel="stylesheet" href="{{ asset('css/alerta.css') }}?v=4">
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

            <main class="app-contenido">

                <h2>Iniciar sesión</h2>

                @if (session('success'))
                <p role="status" style="color: #235B4E;">
                    {{ session('success') }}
                </p>
                @endif

                <div class="campo">
                    <label>CURP</label>

                    <input
                        type="text"
                        id="curp"
                        placeholder="Ingresa tu CURP"
                        maxlength="18"
                        autocomplete="username">
                </div>

                <div class="campo">
                    <label>Contraseña</label>

                    <input
                        type="password"
                        id="password"
                        placeholder="••••••••"
                        autocomplete="current-password">
                </div>

                <button
                    type="button"
                    class="boton boton-login"
                    id="btnLogin"
                    onclick="iniciarSesion()">
                    Iniciar sesión
                </button>

                <div
                    id="mensajeLogin"
                    style="
                    margin-top: 15px;
                    text-align: center;
                    font-size: 14px;
                    font-weight: bold;
                ">
                </div>

                <button
                    type="button"
                    class="boton boton-login"
                    id="btnReenviarCorreo"
                    onclick="reenviarCorreoVerificacion()"
                    hidden>
                    Reenviar correo de verificación
                </button>

                <div class="aviso">

                    ¿No tienes una cuenta?

                    <a href="{{ route('registro') }}">
                        Regístrate
                    </a>

                </div>

                <div class="aviso">
                    Gobierno de Ixtapaluca 2025–2027
                </div>
            </main>

        </div>

    </div>


    <script>
        async function iniciarSesion() {

            const curpInput =
                document.getElementById('curp');

            const passwordInput =
                document.getElementById('password');

            const mensaje =
                document.getElementById('mensajeLogin');

            const boton =
                document.getElementById('btnLogin');


            const curp =
                curpInput.value
                .trim()
                .toUpperCase();

            const password =
                passwordInput.value;


            mensaje.textContent = '';
            document.getElementById('btnReenviarCorreo').hidden = true;


            if (curp === '' || password === '') {

                mensaje.style.color = '#9F2241';

                mensaje.textContent =
                    'Ingresa tu CURP y contraseña.';

                return;
            }


            boton.disabled = true;
            boton.textContent = 'Iniciando sesión...';


            try {

                const respuesta = await fetch(
                    '/api/login', {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',

                            'Accept': 'application/json'
                        },

                        body: JSON.stringify({
                            curp: curp,
                            password: password
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
                        'CURP o contraseña incorrectos.';
                    document.getElementById('btnReenviarCorreo').hidden =
                        datos.codigo !== 'CORREO_SIN_VERIFICAR';

                    return;
                }


                // Guardar token de autenticación.
                sessionStorage.setItem(
                    'alerta_ixta_token',
                    datos.token
                );


                mensaje.style.color =
                    '#235B4E';

                mensaje.textContent =
                    'Inicio de sesión correcto.';


                // Ir a la pantalla principal.
                window.location.href =
                    "{{ route('inicio') }}";


            } catch (error) {

                mensaje.style.color =
                    '#9F2241';

                mensaje.textContent =
                    'No fue posible conectar con el servidor.';

            } finally {

                boton.disabled = false;
                boton.textContent =
                    'Iniciar sesión';

            }

        }

        async function reenviarCorreoVerificacion() {
            const boton = document.getElementById('btnReenviarCorreo');
            const mensaje = document.getElementById('mensajeLogin');

            boton.disabled = true;
            mensaje.style.color = '#235B4E';
            mensaje.textContent = 'Enviando correo...';

            try {
                const respuesta = await fetch('/api/correo-verificacion/reenviar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        curp: document.getElementById('curp').value.trim().toUpperCase(),
                        password: document.getElementById('password').value
                    })
                });

                const datos = await respuesta.json();

                mensaje.style.color = respuesta.ok ? '#235B4E' : '#9F2241';
                mensaje.textContent = datos.message ??
                    'No se pudo solicitar otro correo. Intenta nuevamente.';
            } catch (error) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent =
                    'No fue posible conectar con el servidor.';
            } finally {
                boton.disabled = false;
            }
        }


        // Permitir iniciar sesión presionando Enter.
        document.addEventListener(
            'keydown',
            function(event) {

                if (event.key === 'Enter') {
                    iniciarSesion();
                }

            }
        );
    </script>

</body>

</html>