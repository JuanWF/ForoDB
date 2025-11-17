<?php

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use function Livewire\Volt\{state, rules};

state([
    'title' => '',
    'body' => '',
    'tags' => '', // comma-separated
]);

rules([
    'title' => ['required','string','max:150'],
    'body' => ['required','string','max:5000'],
    'tags' => ['nullable','string','max:200'],
]);

$save = function () {
    $this->validate();

    $user = Auth::user();

    $tags = collect(explode(',', (string) $this->tags))
        ->map(fn ($t) => trim($t))
        ->filter()
        ->unique()
        ->values()
        ->all();

    $post = Post::create([
        'title' => trim($this->title),
        'body' => trim($this->body),
        'author_id' => (string) $user->_id,
        'author_name' => $user->name,
        'tags' => $tags,
        'score' => 0,
        'comments_count' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('posts.show', (string) $post->_id)
        ->with('status', 'Post creado');
};
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nuevo Post - ForoDB</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    @livewireScripts
    <script defer src="https://unpkg.com/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-zinc-50 text-zinc-900">
<div class="max-w-6xl mx-auto p-6 space-y-6">
    <!-- Top bar -->
    <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('posts.index') }}" class="w-8 h-8 rounded-full bg-zinc-200 flex items-center justify-center">←</a>
            <div class="text-xl font-semibold">ForoDB</div>
        </div>
        <div class="flex-1 max-w-xl mx-6"></div>
        <div class="flex items-center gap-4 text-zinc-600">
            <span title="Inicio">🏠</span>
            <span title="Notificaciones">🔔</span>
            <span title="Perfil">👤</span>
        </div>
    </header>

    <h1 class="text-xl font-semibold">Crear nuevo post</h1>

    <form wire:submit="save" class="bg-white rounded-lg border border-zinc-200 p-5 space-y-4">
        <div>
            <label class="block text-sm text-zinc-700">Título</label>
            <input type="text" wire:model.defer="title" class="w-full border border-zinc-300 rounded-md p-2" placeholder="Título del post">
            @error('title')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm text-zinc-700">Contenido</label>
            <textarea rows="6" wire:model.defer="body" class="w-full border border-zinc-300 rounded-md p-2" placeholder="Contenido del post..."></textarea>
            @error('body')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm text-zinc-700">Tags (separadas por coma)</label>
            <input type="text" wire:model.defer="tags" class="w-full border border-zinc-300 rounded-md p-2" placeholder="mongodb, performance, índices">
            @error('tags')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white rounded-md text-sm">Publicar</button>
            <a href="{{ route('posts.index') }}" class="px-3 py-1.5 border border-zinc-300 rounded-md text-sm">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
