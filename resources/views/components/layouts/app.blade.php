<x-layouts.posts :title="$title ?? null" :backUrl="route('posts.index')">
    {{ $slot }}
</x-layouts.posts>
