<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ALERTA IXTA - Alerta registrada</title>

    <link rel="stylesheet" href="{{ asset('css/alerta.css') }}">
</head>

<body>

<div class="contenedor">

    <div class="app">

        <div class="logo">
            ALERTA IXTA
        </div>

        <h2 style="text-align: center;">
            Alerta registrada
        </h2>

        <p class="info" style="text-align: center;">
            Tu solicitud fue registrada correctamente
            en el ambiente de prueba.
        </p>

        <div class="tarjeta">

            <div class="etiqueta">
                Folio
            </div>

            <p>
                <strong>IXTA-2026-000001</strong>
            </p>


            <div class="etiqueta">
                Tipo de alerta
            </div>

            <p>
                Seguridad
            </p>


            <div class="etiqueta">
                Hora
            </div>

            <p>
                10:35
            </p>


            <div class="etiqueta">
                Estado
            </div>

            <p class="estado">
                NUEVA
            </p>


            <div class="etiqueta">
                Ubicación
            </div>

            <p>
                Ubicación obtenida correctamente
            </p>

        </div>

        <a href="{{ route('inicio') }}" class="boton">
            Regresar
        </a>

        <div class="aviso">
            Este prototipo no sustituye al 911 ni a los
            canales oficiales de emergencia.
        </div>

    </div>

</div>

</body>
</html>