@extends('layouts.app')
@section('title', 'Post - ForoDB')
@php($post = \App\Models\Post::with('reactions')->findOrFail(request()->route('id')))
@section('content')

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <main class="lg:col-span-2 space-y-4">
            <article class="bg-white rounded-lg border border-zinc-200 p-5">
                <div class="flex items-start justify-between gap-4">
                    <h1 class="text-2xl font-semibold">{{ $post->title }}</h1>
                    <div class="text-sm text-zinc-500 whitespace-nowrap">{{ $post->created_at_human }}</div>
                </div>
                <div class="flex items-center gap-3 mt-2 text-zinc-700">
                    <div class="w-9 h-9 rounded-full bg-zinc-200"></div>
                    <div>
                        <div class="font-medium">{{ $post->author_name }}</div>
                        <div class="text-xs text-zinc-500">@usuario</div>
                    </div>
                </div>
                <div class="prose max-w-none text-zinc-800 mt-4">{!! nl2br(e($post->body)) !!}</div>
                @php($group = $post->reactions->groupBy('type')->map->count()->toArray())
                <div class="mt-4">
                    @auth
                        <x-reactions :reactable-type="'post'" :reactable-id="(string) $post->_id" :counts="$group" />
                    @endauth
                </div>
                <div class="flex items-center gap-6 mt-4 text-zinc-700">
                    <div class="flex items-center gap-1"><span>⬆️</span> <span>{{ $post->score ?? 0 }}</span></div>
                    <div class="flex items-center gap-1"><span>💬</span> <span>{{ ($post->comments_count ?? 0) + 0 }}</span></div>
                </div>
            </article>

            <section class="space-y-3">
                <h2 class="text-lg font-semibold">Comentarios</h2>
                @auth
                    <form method="POST" action="{{ route('posts.comment', request()->route('id')) }}" class="bg-white rounded-lg border border-zinc-200 p-4 space-y-3">
                        @csrf
                        <label class="block text-sm text-zinc-700">Nuevo comentario</label>
                        <textarea name="body" rows="3" class="w-full border border-zinc-300 rounded-md p-2" placeholder="Escribe tu comentario..."></textarea>
                        @error('body')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white rounded-md text-sm">Publicar</button>
                    </form>
                @endauth

                @php($comments = \App\Models\Comment::where('post_id', (string) request()->route('id'))->with('reactions')->orderBy('created_at')->paginate(10))
                @forelse($comments as $comment)
                    @php($cgroup = $comment->reactions->groupBy('type')->map->count()->toArray())
                    <article class="bg-white rounded-lg border border-zinc-200 p-4">
                        <div class="flex items-center justify-between text-sm text-zinc-600">
                            <div>{{ $comment->user_name }}</div>
                            <div>{{ $comment->created_at_human }}</div>
                        </div>
                        <div class="mt-2 text-zinc-800">{!! nl2br(e($comment->body)) !!}</div>
                        <div class="mt-3">
                            @auth
                                <x-reactions :reactable-type="'comment'" :reactable-id="(string) $comment->_id" :counts="$cgroup" />
                            @endauth
                        </div>
                        <div class="flex items-center gap-6 mt-3 text-zinc-700">
                            <div class="flex items-center gap-1"><span>⬆️</span> <span>{{ $cgroup['like'] ?? 0 }}</span></div>
                            <div class="flex items-center gap-1"><span>💬</span> <span>0</span></div>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-zinc-600">No hay comentarios todavía.</p>
                @endforelse
                <div class="pt-2">{{ $comments->links() }}</div>
            </section>
        </main>

        <aside class="space-y-3">
            <div class="bg-white rounded-lg border border-zinc-200 p-4">
                <div class="font-semibold mb-2">🔥 Tendencias</div>
                <ul class="text-sm text-zinc-700 space-y-1">
                    @php($counts = collect($post->tags ?? [])->mapWithKeys(fn($t) => [$t => 1]))
                    @forelse($counts as $tag => $n)
                        <li>• {{ $tag }}</li>
                    @empty
                        <li class="text-zinc-500">Sin tendencias</li>
                    @endforelse
                </ul>
                @auth
                    <a href="{{ route('posts.create') }}" class="mt-4 inline-block px-3 py-1.5 bg-emerald-600 text-white rounded-md text-sm">+ Nuevo Post</a>
                @endauth
            </div>
        </aside>
    </div>
@endsection
