<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ForoDB' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-zinc-50 text-zinc-900 antialiased">
    {{ $slot }}
</body>
</html>
