<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ALERTA IXTA</title>

    <link rel="stylesheet" href="{{ asset('css/alerta.css') }}">
</head>

<body>

<div class="contenedor">

    <div class="app">

        <div class="logo">
            ALERTA IXTA
        </div>

        <h2>Hola, Usuario</h2>

        <p class="info">
            Selecciona el tipo de alerta y mantén presionado
            el botón para solicitar ayuda.
        </p>

        <div class="campo">

            <label>Tipo de alerta</label>

            <select>
                <option>Seguridad</option>
                <option>Emergencia médica</option>
                <option>Accidente</option>
                <option>Otro</option>
            </select>

        </div>

        <div class="sos-contenedor">

            <a
                href="{{ route('alerta.enviada') }}"
                class="sos"
            >
                SOS
            </a>

            <div class="instruccion">
                Mantén presionado durante 3 segundos
            </div>

        </div>

        <div class="ubicacion">
            📍 Ubicación disponible
        </div>

        <a href="#" class="boton boton-secundario">
            Mis alertas
        </a>

    </div>

</div>

</body>
</html>