<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
// Legacy controllers removed; Volt pages handle UI and actions

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Posts index as plain Blade view (no Livewire) to avoid hydration issues
Route::view('posts', 'livewire.posts.index')->name('posts.index');
// Post detail as plain Blade view (no Livewire), with 24-hex constraint
Route::view('posts/{id}', 'livewire.posts.show')
    ->where('id', '[a-f0-9]{24}')
    ->name('posts.show');

// Fallback non-Livewire comment create to avoid Livewire update 404s
use Illuminate\Http\Request;
use App\Models\Comment as CommentModel;
use Illuminate\Support\Facades\Auth as AuthFacade;
use App\Models\Reaction as ReactionModel;
use App\Models\Post as PostModel;

Route::post('posts/{id}/comments', function (Request $request, string $id) {
    $request->validate([
        'body' => ['required','string','max:2000'],
    ]);
    $user = AuthFacade::user();
    if (!$user) {
        return redirect()->route('login');
    }
    CommentModel::create([
        'post_id' => (string) $id,
        'user_id' => (string) $user->_id,
        'user_name' => $user->name,
        'body' => trim($request->input('body')),
        'created_at' => now(),
    ]);
    return back()->with('status', 'Comentario publicado');
})->where('id', '[a-f0-9]{24}')->name('posts.comment');

// Toggle reactions without Livewire
Route::post('reactions/toggle', function (Request $request) {
    $data = $request->validate([
        'type' => ['required','in:like,love,laugh,insightful'],
        'reactable_type' => ['required','in:post,comment'],
        'reactable_id' => ['required','regex:/^[a-f0-9]{24}$/'],
    ]);
    $user = AuthFacade::user();
    if (!$user) {
        return redirect()->route('login');
    }

    $map = [
        'post' => PostModel::class,
        'comment' => CommentModel::class,
    ];
    $class = $map[$data['reactable_type']];

    $existing = ReactionModel::query()->where([
        'user_id' => (string) $user->_id,
        'reactable_type' => $class,
        'reactable_id' => (string) $data['reactable_id'],
    ])->first();

    if ($existing && $existing->type === $data['type']) {
        $existing->delete();
    } else {
        if ($existing) $existing->delete();
        ReactionModel::create([
            'user_id' => (string) $user->_id,
            'reactable_type' => $class,
            'reactable_id' => (string) $data['reactable_id'],
            'type' => $data['type'],
            'created_at' => now(),
        ]);
    }

    return back();
})->name('reactions.toggle');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Create Post (Livewire Volt)
    Volt::route('posts/create', 'posts.create')
        ->name('posts.create');
});
