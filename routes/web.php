<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Livewire\Posts\Index as PostsIndex;
use App\Livewire\Posts\Show as PostsShow;
use App\Livewire\Posts\Create as PostsCreate;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('posts.index');
    }
    return view('welcome');
})->name('home');

// Rutas del foro con Livewire
Route::get('posts', PostsIndex::class)->name('posts.index');

Route::get('posts/{id}', PostsShow::class)
    ->where('id', '[a-f0-9]{24}')
    ->name('posts.show');

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

    // Crear Post (Livewire)
    Route::get('posts/create', PostsCreate::class)->name('posts.create');
});
