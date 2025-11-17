<div class="max-w-6xl mx-auto p-6 space-y-6">
    <!-- Top bar -->
        <header class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('posts.index') }}" 
                   class="w-10 h-10 rounded-full bg-zinc-200 hover:bg-zinc-300 flex items-center justify-center text-xl transition-colors">
                    ←
                </a>
                <div class="text-2xl font-bold text-zinc-800">ForoDB</div>
            </div>
            <div class="flex-1 max-w-xl mx-6"></div>
            <div class="flex items-center gap-5 text-zinc-600 text-xl">
                <a href="{{ route('posts.index') }}" title="Inicio" class="hover:text-teal-600">🏠</a>
                <a href="#" title="Notificaciones" class="hover:text-teal-600">🔔</a>
                @auth
                    <a href="{{ route('dashboard') }}" title="Perfil" class="hover:text-teal-600">
                        <div class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-white text-sm font-semibold">
                            {{ auth()->user()->initials() }}
                        </div>
                    </a>
                @endauth
            </div>
        </header>

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
