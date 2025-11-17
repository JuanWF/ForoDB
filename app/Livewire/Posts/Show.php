<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

#[Layout('layouts.app')]
class Show extends Component
{
    #[Locked]
    public string $postId;
    
    public string $commentBody = '';

    protected function rules()
    {
        return [
            'commentBody' => ['required', 'string', 'max:2000'],
        ];
    }

    public function mount(string $id)
    {
        $this->postId = $id;
    }

    public function getPostProperty()
    {
        return Post::findOrFail($this->postId);
    }

    public function addComment()
    {
        $this->validate();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        $post = Post::findOrFail($this->postId);
        
        $post->addComment(
            userId: (string) $user->_id,
            userName: $user->name,
            body: trim($this->commentBody)
        );

        $this->commentBody = '';
        
        session()->flash('status', 'Comentario publicado');
    }

    public function toggleReaction(string $type)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        $post = Post::findOrFail($this->postId);
        
        $post->toggleReaction(
            userId: (string) $user->_id,
            userName: $user->name,
            type: $type
        );
    }

    public function toggleCommentReaction(string|int $commentId, string $type)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        $post = Post::findOrFail($this->postId);
        $comments = $post->comments ?? [];
        
        if (is_numeric($commentId)) {
            $commentId = $comments[$commentId]['_id'] ?? (string)$commentId;
        }
        
        $post->toggleCommentReaction(
            commentId: (string)$commentId,
            userId: (string) $user->_id,
            userName: $user->name,
            type: $type
        );
    }

    public function render()
    {
        $post = $this->post;
        
        $trending = collect($post->tags ?? [])
            ->mapWithKeys(fn($tag) => [$tag => 1]);

        return view('livewire.posts.show', [
            'post' => $post,
            'trending' => $trending,
        ]);
    }
}
