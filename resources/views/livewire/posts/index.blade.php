<div class="max-w-6xl mx-auto p-6 space-y-6">
    <!-- Top bar -->
        <header class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-teal-500 flex items-center justify-center text-white text-xl">
                    ☁️
                </div>
                <div class="text-2xl font-bold text-zinc-800">ForoDB</div>
            </div>
            <div class="flex-1 max-w-xl mx-6">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="🔍 Search"
                        class="w-full border border-zinc-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>
            <div class="flex items-center gap-5 text-zinc-600 text-xl">
                <a href="{{ route('posts.index') }}" title="Inicio" class="hover:text-teal-600">🏠</a>
                <a href="#" title="Notificaciones" class="hover:text-teal-600">🔔</a>
                @auth
                    <a href="{{ route('dashboard') }}" title="Perfil" class="hover:text-teal-600">
                        <div class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-white text-sm font-semibold">
                            {{ auth()->user()->initials() }}
                        </div>
                    </a>
                @else
                    <a href="{{ route('login') }}" title="Login" class="hover:text-teal-600">👤</a>
                @endauth
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main content -->
            <main class="lg:col-span-2 space-y-4">
                @forelse($posts as $post)
                    <article class="bg-white rounded-xl border border-zinc-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="p-5">
                            <!-- Post header -->
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <a href="{{ route('posts.show', (string) $post->_id) }}" class="flex-1 group">
                                    <h2 class="text-xl font-bold text-zinc-800 group-hover:text-teal-600 transition-colors">
                                        {{ $post->title }}
                                    </h2>
                                </a>
                                <div class="text-sm text-zinc-500 whitespace-nowrap">
                                    {{ $post->created_at_human }}
                                </div>
                            </div>

                            <!-- Author info -->
                            <div class="flex items-center gap-3 mb-4">
                                @php
                                    $authorName = is_array($post->author) ? ($post->author['name'] ?? 'Usuario') : 'Usuario';
                                    $authorEmail = is_array($post->author) ? ($post->author['email'] ?? 'usuari o@example.com') : 'usuario@example.com';
                                    $authorInitial = substr($authorName, 0, 1);
                                    $username = explode('@', $authorEmail)[0];
                                @endphp
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white font-semibold shadow-sm">
                                    {{ $authorInitial }}
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-800">{{ $authorName }}</div>
                                    <div class="text-xs text-zinc-500">{{ $username }}</div>
                                </div>
                            </div>                            <!-- Post preview -->
                            @if(!empty($post->body))
                                <div class="text-zinc-700 text-sm mb-4 line-clamp-2">
                                    {{ Str::limit($post->body, 150) }}
                                </div>
                            @endif

                            <!-- Tags -->
                            @if(!empty($post->tags))
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach(array_slice($post->tags, 0, 3) as $tag)
                                        <span class="px-2 py-1 bg-teal-50 text-teal-700 text-xs rounded-full border border-teal-200">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Stats -->
                            <div class="flex items-center gap-6 text-zinc-600 text-sm">
                                <div class="flex items-center gap-2 hover:text-teal-600 transition-colors">
                                    <span class="text-lg">⬆️</span>
                                    <span class="font-semibold">{{ $post->score }}</span>
                                </div>
                                <div class="flex items-center gap-2 hover:text-teal-600 transition-colors">
                                    <span class="text-lg">💬</span>
                                    <span class="font-semibold">{{ $post->comments_count }}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-xl border border-zinc-200 p-12 text-center">
                        <div class="text-6xl mb-4">📝</div>
                        <p class="text-zinc-600 text-lg mb-2">No hay posts todavía</p>
                        <p class="text-zinc-500 text-sm">¡Sé el primero en crear uno!</p>
                    </div>
                @endforelse

                <div class="pt-4">
                    {{ $posts->links() }}
                </div>
            </main>

            <!-- Sidebar -->
            <aside class="space-y-4">
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-5">
                    <div class="flex items-center gap-2 font-bold text-lg mb-4">
                        <span>🔥</span>
                        <span class="text-zinc-800">Tendencias</span>
                    </div>
                    <ul class="space-y-3">
                        @forelse($trending as $tag => $count)
                            <li class="flex items-center justify-between">
                                <span class="text-zinc-700 font-medium">• {{ $tag }}</span>
                                <span class="text-xs text-zinc-500 bg-zinc-100 px-2 py-1 rounded-full">{{ $count }}</span>
                            </li>
                        @empty
                            <li class="text-zinc-500 text-sm">Sin tendencias aún</li>
                        @endforelse
                    </ul>
                </div>

                @auth
                    <a href="{{ route('posts.create') }}" 
                       class="block w-full px-4 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-center font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                        ➕ Nuevo Post
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="block w-full px-4 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-center font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                        Iniciar sesión
                    </a>
                @endauth
            </aside>
        </div>
</div>
