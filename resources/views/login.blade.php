<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ALERTA IXTA - Inicio de sesión</title>

    <link rel="stylesheet" href="{{ asset('css/alerta.css') }}">
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

            <div class="subtitulo">
                Sistema Municipal de Alerta y Botón de Pánico
            </div>

            <h2>Iniciar sesión</h2>

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
                class="boton"
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

            <div class="aviso">

                ¿No tienes una cuenta?

                <a href="{{ route('registro') }}">
                    Regístrate
                </a>

            </div>

            <div class="aviso">
                Gobierno de Ixtapaluca 2025–2027
            </div>

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
                    '/api/login',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json'
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


        // Permitir iniciar sesión presionando Enter.
        document.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Enter') {
                    iniciarSesion();
                }

            }
        );

    </script>

</body>

</html>