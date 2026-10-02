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

        <div class="logo">
            ALERTA IXTA
        </div>

        <div class="subtitulo">
            Sistema Municipal de Alerta
        </div>

        <h2>Iniciar sesión</h2>

        <div class="campo">
            <label>Correo electrónico</label>

            <input
                type="email"
                placeholder="usuario@correo.com"
            >
        </div>

        <div class="campo">
            <label>Contraseña</label>

            <input
                type="password"
                placeholder="••••••••"
            >
        </div>

        <a href="{{ route('inicio') }}" class="boton">
            Iniciar sesión
        </a>

        <div class="aviso">
            Prototipo académico - ambiente de prueba
        </div>

    </div>

</div>

</body>
</html>