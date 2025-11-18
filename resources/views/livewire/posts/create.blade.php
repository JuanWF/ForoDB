<div class="min-h-screen bg-zinc-50">
    <!-- Top bar -->
    <header class="sticky top-0 z-50 bg-white border-b-2 border-zinc-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('posts.index') }}" 
                   class="w-10 h-10 rounded-full bg-zinc-200 hover:bg-zinc-300 flex items-center justify-center text-xl transition-colors">
                    ←
                </a>
                <div class="text-2xl font-bold text-zinc-800">ForoDB</div>
            </div>
            <div class="flex-1 max-w-2xl mx-6"></div>
            <div class="flex items-center gap-5 text-zinc-600 text-xl">
                <a href="{{ route('posts.index') }}" title="Inicio" class="hover:text-teal-600">🏠</a>
                <a href="#" title="Notificaciones" class="hover:text-teal-600">🔔</a>
                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button" class="hover:text-teal-600 focus:outline-none" title="Perfil">
                            <div class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-white text-sm font-semibold">
                                {{ auth()->user()->initials() }}
                            </div>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 rounded-lg bg-white shadow-lg border border-zinc-200 py-1 z-50"
                             style="display: none;">
                            
                            <div class="px-4 py-3 border-b border-zinc-200">
                                <p class="text-sm font-semibold text-zinc-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-zinc-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center gap-2 px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50 transition-colors">
                                <span class="text-base">⚙️</span>
                                <span>Settings</span>
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" 
                                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50 transition-colors text-left">
                                    <span class="text-base">🚪</span>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <div class="max-w-6xl mx-auto p-6 space-y-6">

        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-zinc-800 mb-6">✍️ Crear nuevo post</h1>

            <form wire:submit.prevent="save" class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 space-y-5">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 mb-2">Título</label>
                    <input 
                        type="text" 
                        wire:model="title" 
                        class="w-full border border-zinc-300 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-teal-500" 
                        placeholder="¿De qué quieres hablar?">
                    @error('title')
                        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Body -->
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 mb-2">Contenido</label>
                    <textarea 
                        rows="8" 
                        wire:model="body" 
                        class="w-full border border-zinc-300 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none" 
                        placeholder="Comparte tus conocimientos, preguntas o experiencias..."></textarea>
                    @error('body')
                        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 mb-2">Tags</label>
                    <input 
                        type="text" 
                        wire:model="tags" 
                        class="w-full border border-zinc-300 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-teal-500" 
                        placeholder="MongoDB, SQL, MySQL, Eficiencia (separadas por comas)">
                    <p class="text-xs text-zinc-500 mt-1">Agrega tags relevantes separadas por comas</p>
                    @error('tags')
                        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md">
                        📝 Publicar post
                    </button>
                    <a 
                        href="{{ route('posts.index') }}" 
                        class="px-6 py-3 border border-zinc-300 hover:bg-zinc-50 rounded-lg font-semibold text-zinc-700 transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
