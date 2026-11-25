<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALERTA IXTA - Revisa tu correo</title>
    <link rel="stylesheet" href="{{ asset('css/alerta.css') }}?v=4">

    <style>
        .registro-correo-aviso {
            border: 1px solid #e5d9dd;
            border-radius: 12px;
            padding: 22px;
            line-height: 1.6;
        }

        .registro-correo-aviso h2 {
            margin-top: 0;
            color: #691C32;
        }

        .registro-correo-aviso p {
            margin-bottom: 16px;
        }

        .registro-correo-enlace {
            display: block;
            padding: 14px;
            border-radius: 8px;
            background: #691C32;
            color: #ffffff;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
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
                    <h1 class="app-cabecera-titulo">ALERTA IXTA</h1>
                    <p class="app-cabecera-subtitulo">
                        Sistema Municipal de Alerta
                    </p>
                </div>

                <div aria-hidden="true"></div>
            </header>

            <main class="app-contenido">
                <div class="registro-correo-aviso" role="status">
                    @if (session('resultado_correo') === 'enviado')
                        <h2>Revisa tu correo</h2>

                        <p>
                            Tu cuenta fue registrada. Te enviamos un mensaje
                            para confirmar tu dirección de correo electrónico.
                        </p>

                        <p>
                            Abre el mensaje de ALERTA IXTA y presiona
                            <strong>"Verificar mi correo"</strong>.
                            Después podrás iniciar sesión con tu CURP y contraseña.
                        </p>

                        <p>
                            Si no aparece, revisa la carpeta de correo no deseado.
                        </p>
                    @else
                        <h2>Tu cuenta fue registrada</h2>

                        <p>
                            No pudimos enviar el correo de verificación.
                            No vuelvas a registrar la misma cuenta.
                        </p>

                        <p>
                            Estamos preparando la opción para solicitar
                            un nuevo enlace desde el inicio de sesión.
                        </p>
                    @endif

                    <a class="registro-correo-enlace" href="{{ route('login') }}">
                        Ir al inicio de sesión
                    </a>
                </div>
            </main>
        </div>
    </div>
</body>

</html>