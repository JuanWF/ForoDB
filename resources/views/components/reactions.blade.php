@props(['reactableType', 'reactableId', 'counts' => []])

@php
    $map = [
        'post' => \App\Models\Post::class,
        'comment' => \App\Models\Comment::class,
    ];
    $active = null;
    if (auth()->check()) {
        $class = $map[strtolower($reactableType)] ?? null;
        if ($class) {
            $active = \App\Models\Reaction::query()->where([
                'user_id' => (string) auth()->id(),
                'reactable_type' => $class,
                'reactable_id' => (string) $reactableId,
            ])->value('type');
        }
    }
    $allowed = ['like' => '👍', 'love' => '❤️', 'laugh' => '😄', 'insightful' => '💡'];
@endphp

<div class="flex items-center gap-3">
    @foreach($allowed as $type => $emoji)
        @php($isActive = $active === $type)
        <form method="POST" action="{{ route('reactions.toggle') }}">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="reactable_type" value="{{ $reactableType }}">
            <input type="hidden" name="reactable_id" value="{{ $reactableId }}">
            <button type="submit"
                class="px-2 py-1 border rounded-md text-sm hover:bg-zinc-800/5 {{ $isActive ? 'bg-emerald-50 border-emerald-400 text-emerald-700' : '' }}">
                <span class="mr-1">{{ $emoji }}</span>
                <span class="count" data-type="{{ $type }}">{{ $counts[$type] ?? 0 }}</span>
            </button>
        </form>
    @endforeach
    </div>
