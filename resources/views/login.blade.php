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
                    placeholder="Ingresa tu CURP"
                    maxlength="18">
            </div>

            <div class="campo">
                <label>Contraseña</label>

                <input
                    type="password"
                    placeholder="••••••••">
            </div>

            <a href="{{ route('inicio') }}" class="boton">
                Iniciar sesión
            </a>

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

</body>

</html>