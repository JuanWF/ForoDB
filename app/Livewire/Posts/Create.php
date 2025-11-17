<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    public string $title = '';
    public string $body = '';
    public string $tags = '';

    protected function rules()
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:5000'],
            'tags' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $tags = collect(explode(',', $this->tags))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $post = new Post();
        $post->title = trim($this->title);
        $post->body = trim($this->body);
        $post->author = [
            '_id' => (string) $user->_id,
            'name' => $user->name,
            'email' => $user->email,
        ];
        $post->tags = $tags;
        $post->comments = [];
        $post->reactions = [];
        $post->created_at = now();
        $post->updated_at = now();
        
        $post->save();
        
        // El _id ya debería estar disponible después de save()
        $postId = (string) $post->_id;

        return $this->redirect("/posts/{$postId}", navigate: true);
    }

    public function render()
    {
        return view('livewire.posts.create');
    }
}
