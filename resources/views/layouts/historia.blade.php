<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Plantilla de Consulta')</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
        }
        main {
            padding: 20px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
