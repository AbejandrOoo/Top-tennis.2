<section>
    <header class="mb-6">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="7" r="4" stroke="#15803d" stroke-width="2"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="#15803d" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Información del perfil</h2>
        </div>
        <p class="text-sm text-gray-400 ml-12">Actualiza tu nombre, correo e ícono de perfil.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5"
          x-data="{ emojiSeleccionado: '{{ old('emoji_perfil', $user->emoji_perfil ?? '') }}', pickerAbierto: false }">
        @csrf
        @method('patch')

        {{-- Nombre + Emoji en la misma fila --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="name" class="form-label">Nombre completo</label>
                <input id="name" name="name" type="text"
                       class="form-input {{ $errors->has('name') ? 'border-red-400' : '' }}"
                       value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                @error('name')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Ícono de perfil</label>
                <input type="hidden" name="emoji_perfil" :value="emojiSeleccionado">

                {{-- Botón disparador --}}
                <button type="button"
                        @click="pickerAbierto = !pickerAbierto"
                        class="flex items-center gap-3 w-full px-3 py-2 rounded-xl border-2 border-green-200 bg-green-50 hover:border-green-400 transition-colors cursor-pointer">
                    <span class="text-2xl" x-text="emojiSeleccionado || '🎾'"></span>
                    <span class="text-sm text-gray-500 flex-1 text-left" x-text="pickerAbierto ? 'Cerrar' : 'Cambiar ícono'"></span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="pickerAbierto ? 'rotate-180' : ''"
                         viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>

        {{-- Selector de emojis — se despliega al hacer click --}}
        <div x-show="pickerAbierto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="p-4 bg-gray-50 border border-gray-200 rounded-xl">

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Elige tu ícono</p>

            <div class="flex flex-wrap gap-2">
                @php
                    $emojis = [
                        '🎾' => 'Pelota de tenis',
                        '🏆' => 'Trofeo',
                        '🥇' => 'Medalla de oro',
                        '🏅' => 'Medalla',
                        '🎯' => 'Objetivo',
                        '💪' => 'Fuerza',
                        '🔥' => 'Fuego',
                        '⚡' => 'Rayo',
                        '🌟' => 'Estrella',
                        '✨' => 'Destellos',
                        '👑' => 'Corona',
                        '🦁' => 'León',
                        '🐆' => 'Leopardo',
                        '🦅' => 'Águila',
                        '🎽' => 'Camiseta',
                        '👟' => 'Zapatilla',
                    ];
                @endphp
                @foreach($emojis as $emoji => $titulo)
                    <button type="button"
                            title="{{ $titulo }}"
                            @click="emojiSeleccionado = '{{ $emoji }}'; pickerAbierto = false"
                            :class="emojiSeleccionado === '{{ $emoji }}'
                                ? 'border-green-500 bg-green-100 scale-110 shadow-sm'
                                : 'border-gray-200 bg-white hover:border-green-300 hover:bg-green-50'"
                            class="w-11 h-11 rounded-xl border-2 text-2xl flex items-center justify-center transition-all duration-150 cursor-pointer select-none">
                        {{ $emoji }}
                    </button>
                @endforeach

                {{-- Quitar ícono --}}
                <button type="button"
                        title="Sin ícono (usa 👋 por defecto)"
                        @click="emojiSeleccionado = ''; pickerAbierto = false"
                        :class="emojiSeleccionado === ''
                            ? 'border-gray-400 bg-gray-200'
                            : 'border-gray-200 bg-white hover:border-gray-300'"
                        class="w-11 h-11 rounded-xl border-2 text-xs text-gray-400 flex items-center justify-center transition-all duration-150 cursor-pointer font-bold">
                    ✕
                </button>
            </div>

            <p class="text-xs text-gray-400 mt-3">
                Vista previa:
                <span class="font-semibold text-gray-700">
                    Hola, {{ $user->name }} <span x-text="emojiSeleccionado || '👋'"></span>
                </span>
            </p>
        </div>

        {{-- Email (solo lectura) --}}
        <div>
            <label class="form-label">Correo electrónico</label>
            <div class="flex items-center gap-2 px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 text-sm">
                <svg class="w-4 h-4 text-gray-400 shrink-0" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M3 8l9 6 9-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <span>{{ $user->email }}</span>
                <span class="ml-auto text-xs bg-gray-200 text-gray-500 px-2 py-0.5 rounded-full">No editable</span>
            </div>
            <p class="mt-1.5 text-xs text-gray-400">El correo es tu identificador único y no puede modificarse.</p>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-primary py-2 px-6">Guardar cambios</button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-sm text-green-600 font-semibold">
                    ✓ Guardado correctamente
                </p>
            @endif
        </div>
    </form>
</section>
