<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Legacy</title>
    
    @vite(['resources/css/legacy/container.css', 'resources/js/legacy/container.js'])
</head>
<body>
    <div id="app-legacy">
        @yield('content')
    </div>
</body>
</html>