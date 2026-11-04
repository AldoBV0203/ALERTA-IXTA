<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        ALERTA IXTA - Registro
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/alerta.css') }}?v=4">

    <style>
        .boton-registro {
            background: #691C32;
            color: white;
        }

        .boton-registro:hover {
            background: #9F2241;
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

                <h2>
                    Crear cuenta
                </h2>


                {{-- Mostrar mensaje de errores generales --}}

                @if ($errors->any())

                <div class="mensaje-error">

                    Revisa los datos ingresados.

                </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('registro.store') }}">

                    @csrf


                    <div class="campo">

                        <label>
                            Nombre completo
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ingresa tu nombre completo"
                            required>

                        @error('name')

                        <small class="error">
                            {{ $message }}
                        </small>

                        @enderror

                    </div>


                    <div class="campo">

                        <label>
                            CURP
                        </label>

                        <input
                            type="text"
                            name="curp"
                            value="{{ old('curp') }}"
                            placeholder="Ingresa tu CURP"
                            minlength="18"
                            maxlength="18"
                            required
                            style="text-transform: uppercase;">

                        @error('curp')

                        <small class="error">
                            {{ $message }}
                        </small>

                        @enderror

                    </div>


                    <div class="campo">

                        <label>
                            Teléfono
                        </label>

                        <input
                            type="tel"
                            name="telefono"
                            value="{{ old('telefono') }}"
                            placeholder="10 dígitos"
                            maxlength="10"
                            required>

                        @error('telefono')

                        <small class="error">
                            {{ $message }}
                        </small>

                        @enderror

                    </div>


                    <div class="campo">

                        <label>
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Opcional">

                        @error('email')

                        <small class="error">
                            {{ $message }}
                        </small>

                        @enderror

                    </div>


                    <div class="campo">

                        <label>
                            Contraseña
                        </label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Mínimo 8 caracteres"
                            required>

                        @error('password')

                        <small class="error">
                            {{ $message }}
                        </small>

                        @enderror

                    </div>


                    <div class="campo">

                        <label>
                            Confirmar contraseña
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            placeholder="Repite tu contraseña"
                            required>

                    </div>


                    <button
                        type="submit"
                        class="boton boton-registro">
                        Registrarme
                    </button>

                </form>


                <div class="aviso">

                    ¿Ya tienes una cuenta?

                    <a href="{{ route('login') }}">
                        Iniciar sesión
                    </a>

                </div>

            </main>

        </div>

    </div>

</body>

</html>