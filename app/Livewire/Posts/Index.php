<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('ForoDB - Posts')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Post::query()->orderByDesc('created_at');
        
        if (filled($this->search)) {
            $search = trim($this->search);
            $pattern = '.*' . preg_quote($search, '/') . '.*';
            $regex = new \MongoDB\BSON\Regex($pattern, 'i');
            
            $query->where(function ($w) use ($regex) {
                $w->where('title', 'regex', $regex)
                  ->orWhere('body', 'regex', $regex);
            });
        }
        
        $posts = $query->paginate(10);
        
        // Calcular tendencias de los tags
        $allPosts = Post::query()->get();
        $trending = collect();
        
        foreach ($allPosts as $post) {
            $tags = $post->tags;
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $trending[$tag] = ($trending[$tag] ?? 0) + 1;
                    }
                }
            }
        }
        
        $trending = $trending->sortDesc()->take(5);
        
        return view('livewire.posts.index', [
            'posts' => $posts,
            'trending' => $trending,
        ]);
    }
}
