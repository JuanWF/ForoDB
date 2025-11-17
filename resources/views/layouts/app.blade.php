<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ForoDB')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    @livewireScripts
</head>
<body class="bg-zinc-50 text-zinc-900">
<div class="max-w-6xl mx-auto p-6 space-y-6">
    @yield('content')
    </div>
</body>
</html>
