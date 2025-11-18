<div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
    <div class="flex flex-col lg:flex-row">
        <!-- Sidebar de navegación -->
        <div class="lg:w-64 border-b lg:border-b-0 lg:border-r border-zinc-200 bg-zinc-50">
            <div class="p-4">
                <h3 class="text-sm font-semibold text-zinc-500 uppercase tracking-wide mb-3">Settings</h3>
                <nav class="space-y-1">
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('profile.edit') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-zinc-700 hover:bg-zinc-100' }}"
                       wire:navigate>
                        <span>👤</span>
                        <span>{{ __('Profile') }}</span>
                    </a>
                    <a href="{{ route('user-password.edit') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('user-password.edit') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-zinc-700 hover:bg-zinc-100' }}"
                       wire:navigate>
                        <span>🔒</span>
                        <span>{{ __('Password') }}</span>
                    </a>
                    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                        <a href="{{ route('two-factor.show') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('two-factor.show') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-zinc-700 hover:bg-zinc-100' }}"
                           wire:navigate>
                            <span>🔐</span>
                            <span>{{ __('Two-Factor Auth') }}</span>
                        </a>
                    @endif
                    <a href="{{ route('appearance.edit') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('appearance.edit') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-zinc-700 hover:bg-zinc-100' }}"
                       wire:navigate>
                        <span>🎨</span>
                        <span>{{ __('Appearance') }}</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="flex-1 p-6 lg:p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-zinc-800">{{ $heading ?? '' }}</h2>
                <p class="text-sm text-zinc-600 mt-1">{{ $subheading ?? '' }}</p>
            </div>

            <div class="max-w-2xl">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
