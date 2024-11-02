<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Gestión Remuneraciones</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'figtree', sans-serif;
            background-color: #f0f0f0; /* Cambia el color de fondo a uno más claro */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        .logo-container {
            position: relative;
            margin-bottom: 2rem;
        }
        .logo {
            max-width: 100%;
            height: auto;
            max-height: 120px; /* Ajusta la altura máxima según tu preferencia */
        }
        .login-container {
            position: relative;
            margin-top: 1rem; /* Espacio superior entre el logo y los botones */
        }
        .login-link {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            color: #ffffff; /* Color de texto para el enlace */
            background-color: #4a5568; /* Color de fondo para el botón */
            text-decoration: none;
            border-radius: 25px; /* Bordes redondeados */
            transition: background-color 0.3s ease;
            margin-bottom: 1rem; /* Espacio inferior entre los botones */
        }
        .login-link:hover {
            background-color: #2d3748; /* Color de fondo al pasar el mouse */
        }
        .version-info {
            font-size: 20px; /* Tamaño de fuente del texto de versión */
            color: #000000; /* Color de texto gris */
            margin-top: 0.5rem; /* Espacio superior */
        }
    </style>
</head>
<body class="antialiased">
    <div class="logo-container">
        <img src="{{ asset('img/index_03.jpg') }}" alt="Logo" class="logo">
    </div>
    <h1 style="font-size: 24px; margin-bottom: 1rem;">
        Sistema de Remuneraciones
    </h1>
    <div class="login-container">
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
                @auth
                    <a href="{{ url('/home') }}" class="login-link">Inicio</a>
                @else
                    <a href="{{ route('login') }}" class="login-link">Ingresar</a>
                @endauth
                <div class="version-info">
                    Ver 1.3 <br> Sintec Ltda.
                </div>
            </div>
        @endif
    </div>
</body>
</html>

