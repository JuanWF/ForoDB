@extends('layouts.app')
@section('title', 'ForoDB - Posts')
@php
    use App\Models\Post;
@endphp
@section('content')
    @php
        // Build paginator with optional Mongo regex search (case-insensitive)
        $query = Post::query()->with('reactions')->orderByDesc('created_at');
        $rq = request('q');
        if (filled($rq)) {
            $search = trim((string) $rq);
            $pattern = '.*' . preg_quote($search, '/') . '.*';
            $regex = new \MongoDB\BSON\Regex($pattern, 'i');
            $query = $query->where(function ($w) use ($regex) {
                $w->where('title', 'regex', $regex)
                  ->orWhere('body', 'regex', $regex);
            });
        }
        $paginator = $query->paginate(10)->withQueryString();

        // Compute lightweight trending from current page
        $trending = collect();
        foreach ($paginator as $p) {
            foreach ((array) ($p->tags ?? []) as $t) {
                $trending[$t] = ($trending[$t] ?? 0) + 1;
            }
        }
        $trending = $trending->sortDesc()->take(5);
    @endphp
    <!-- Top bar -->
    <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-zinc-200 flex items-center justify-center">🌫️</div>
            <div class="text-xl font-semibold">ForoDB</div>
        </div>
        <div class="flex-1 max-w-xl mx-6">
            <form method="GET" action="{{ route('posts.index') }}">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search"
                       class="w-full border border-zinc-300 rounded-md px-3 py-1.5 text-sm">
            </form>
        </div>
        <div class="flex items-center gap-4 text-zinc-600">
            <span title="Inicio">🏠</span>
            <span title="Notificaciones">🔔</span>
            <span title="Perfil">👤</span>
        </div>
    </header>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <main class="lg:col-span-2 space-y-4">
        @foreach($paginator as $post)
            @php($commentsCount = $post->comments_count ?? 0)
            <article class="bg-white rounded-lg border border-zinc-200 p-5">
                <div class="flex items-start justify-between gap-4">
                    <a href="{{ route('posts.show', (string) $post->_id) }}" class="block">
                        <h2 class="text-xl font-semibold">{{ $post->title }}</h2>
                    </a>
                    <div class="text-sm text-zinc-500 whitespace-nowrap">{{ $post->created_at_human }}</div>
                </div>
                <div class="flex items-center gap-3 mt-2 text-zinc-700">
                    <div class="w-9 h-9 rounded-full bg-zinc-200"></div>
                    <div>
                        <div class="font-medium">{{ $post->author_name }}</div>
                        <div class="text-xs text-zinc-500">@usuario</div>
                    </div>
                </div>
                <div class="flex items-center gap-6 mt-4 text-zinc-700">
                    <div class="flex items-center gap-1"><span>⬆️</span> <span>{{ $post->score ?? 0 }}</span></div>
                    <div class="flex items-center gap-1"><span>💬</span> <span>{{ $commentsCount }}</span></div>
                </div>
            </article>
        @endforeach
        @if($paginator->isEmpty())
            <p class="text-sm text-zinc-600">No hay posts aún.</p>
        @endif

        <div class="pt-2">{{ $paginator->links() }}</div>
        </main>

        <aside class="space-y-3">
            <div class="bg-white rounded-lg border border-zinc-200 p-4">
                <div class="font-semibold mb-2">🔥 Tendencias</div>
                <ul class="text-sm text-zinc-700 space-y-1">
                    @forelse($trending as $tag => $count)
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
