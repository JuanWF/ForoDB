<div class="max-w-6xl mx-auto p-6 space-y-6">
    <!-- Top bar -->
        <header class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('posts.index') }}" 
                   class="w-10 h-10 rounded-full bg-zinc-200 hover:bg-zinc-300 flex items-center justify-center text-xl transition-colors">
                    ←
                </a>
                <div class="text-2xl font-bold text-zinc-800">ForoDB</div>
            </div>
            <div class="flex-1 max-w-xl mx-6"></div>
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
            <main class="lg:col-span-2 space-y-5">
                <!-- Post detail -->
                <article class="bg-white rounded-xl border border-zinc-200 shadow-sm">
                    <div class="p-6">
                        <!-- Post header -->
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <h1 class="text-3xl font-bold text-zinc-800 flex-1">{{ $post->title }}</h1>
                            <div class="text-sm text-zinc-500 whitespace-nowrap">
                                {{ $post->created_at_human }}
                            </div>
                        </div>

                        <!-- Author info -->
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100">
                            @php
                                $authorName = is_array($post->author) ? ($post->author['name'] ?? 'Usuario') : 'Usuario';
                                $authorEmail = is_array($post->author) ? ($post->author['email'] ?? 'usuario@example.com') : 'usuario@example.com';
                                $authorInitial = substr($authorName, 0, 1);
                                $username = explode('@', $authorEmail)[0];
                            @endphp
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                {{ $authorInitial }}
                            </div>
                            <div>
                                <div class="font-bold text-zinc-800">{{ $authorName }}</div>
                                <div class="text-sm text-zinc-500">{{ $username }}</div>
                            </div>
                        </div>

                        <!-- Post body -->
                        <div class="prose max-w-none text-zinc-700 mb-6 leading-relaxed">
                            {!! nl2br(e($post->body)) !!}
                        </div>

                        <!-- Tags -->
                        @if(!empty($post->tags))
                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach($post->tags as $tag)
                                    <span class="px-3 py-1 bg-teal-50 text-teal-700 text-sm rounded-full border border-teal-200 font-medium">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Reactions -->
                        @auth
                            <div class="mb-4 pb-4 border-b border-zinc-100">
                                <div class="flex items-center gap-2">
                                    @php($reactionsGrouped = $post->reactions_grouped)
                                    @php($userReaction = collect($post->reactions ?? [])->firstWhere('user_id', (string) auth()->id()))
                                    @php($userReactionType = $userReaction['type'] ?? null)

                                    @foreach(['like' => '👍', 'love' => '❤️', 'laugh' => '😄', 'insightful' => '💡'] as $type => $emoji)
                                        @php($count = $reactionsGrouped[$type] ?? 0)
                                        @php($isActive = $userReactionType === $type)
                                        <button 
                                            wire:click="toggleReaction('{{ $type }}')"
                                            class="px-3 py-2 border rounded-lg text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-teal-50 border-teal-400 text-teal-700 shadow-sm' : 'border-zinc-300 hover:bg-zinc-50 text-zinc-700' }}">
                                            <span class="mr-1 text-base">{{ $emoji }}</span>
                                            <span>{{ $count }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endauth

                        <!-- Stats -->
                        <div class="flex items-center gap-6 text-zinc-600">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">⬆️</span>
                                <span class="font-bold text-lg">{{ $post->score }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xl">💬</span>
                                <span class="font-bold text-lg">{{ $post->comments_count }}</span>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Comments section -->
                <section class="space-y-4">
                    <h2 class="text-2xl font-bold text-zinc-800">Comentarios</h2>

                    @auth
                        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-5">
                            <div class="mb-3 text-sm font-semibold text-zinc-700">Escribe un comentario</div>
                            <form wire:submit.prevent="addComment" class="space-y-3">
                                <textarea 
                                    wire:model="commentBody" 
                                    rows="3" 
                                    class="w-full border border-zinc-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none" 
                                    placeholder="Comparte tu opinión..."></textarea>
                                @error('commentBody')
                                    <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold text-sm transition-colors">
                                    Publicar comentario
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-white rounded-xl border border-zinc-200 p-8 text-center">
                            <p class="text-zinc-600 mb-4">Inicia sesión para comentar</p>
                            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold transition-colors">
                                Iniciar sesión
                            </a>
                        </div>
                    @endauth

                    <!-- Comments list -->
                    @if(session('status'))
                        <div class="bg-teal-50 border border-teal-200 text-teal-700 px-4 py-3 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif

                    @php($comments = $post->comments ?? [])
                    @forelse($comments as $index => $comment)
                        @php($commentId = $comment['_id'] ?? $index)
                        <article class="bg-white rounded-xl border border-zinc-200 shadow-sm p-5">
                            <!-- Comment header -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-zinc-400 to-zinc-600 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ substr($comment['user_name'] ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-zinc-800">{{ $comment['user_name'] ?? 'Usuario' }}</div>
                                        <div class="text-xs text-zinc-500">
                                            {{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Comment body -->
                            <div class="text-zinc-700 mb-3 leading-relaxed">
                                {!! nl2br(e($comment['body'] ?? '')) !!}
                            </div>

                            <!-- Comment reactions -->
                            @auth
                                <div class="flex items-center gap-2 pt-2 border-t border-zinc-100">
                                    @php($commentReactions = $comment['reactions'] ?? [])
                                    @php($commentReactionsGrouped = collect($commentReactions)->groupBy('type')->map->count()->toArray())
                                    @php($userCommentReaction = collect($commentReactions)->firstWhere('user_id', (string) auth()->id()))
                                    @php($userCommentReactionType = $userCommentReaction['type'] ?? null)

                                    @foreach(['like' => '👍', 'love' => '❤️', 'laugh' => '😄', 'insightful' => '💡'] as $type => $emoji)
                                        @php($count = $commentReactionsGrouped[$type] ?? 0)
                                        @php($isActive = $userCommentReactionType === $type)
                                        <button 
                                            wire:click="toggleCommentReaction('{{ $commentId }}', '{{ $type }}')"
                                            class="px-2 py-1 border rounded-lg text-xs font-medium transition-all duration-200 {{ $isActive ? 'bg-teal-50 border-teal-400 text-teal-700' : 'border-zinc-300 hover:bg-zinc-50 text-zinc-700' }}">
                                            <span class="mr-1">{{ $emoji }}</span>
                                            <span>{{ $count }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endauth
                        </article>
                    @empty
                        <div class="bg-white rounded-xl border border-zinc-200 p-12 text-center">
                            <div class="text-5xl mb-3">💬</div>
                            <p class="text-zinc-600">No hay comentarios todavía</p>
                            <p class="text-zinc-500 text-sm mt-1">¡Sé el primero en comentar!</p>
                        </div>
                    @endforelse
                </section>
            </main>

            <!-- Sidebar -->
            <aside class="space-y-4">
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-5">
                    <div class="flex items-center gap-2 font-bold text-lg mb-4">
                        <span>🔥</span>
                        <span class="text-zinc-800">Tendencias</span>
                    </div>
                    <ul class="space-y-2">
                        @forelse($trending as $tag => $count)
                            <li class="text-zinc-700 font-medium">• {{ $tag }}</li>
                        @empty
                            <li class="text-zinc-500 text-sm">Sin tendencias</li>
                        @endforelse
                    </ul>
                </div>

                @auth
                    <a href="{{ route('posts.create') }}" 
                       class="block w-full px-4 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-center font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                        ➕ Nuevo Post
                    </a>
                @endauth
            </aside>
        </div>
</div>
