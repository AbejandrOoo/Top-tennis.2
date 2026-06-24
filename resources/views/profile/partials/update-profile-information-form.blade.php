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
          x-data="{ emojiSeleccionado: '{{ old('emoji_perfil', $user->emoji_perfil ?? '') }}' }">
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
                {{-- Input oculto que guarda el emoji seleccionado --}}
                <input type="hidden" name="emoji_perfil" :value="emojiSeleccionado">

                {{-- Botón que muestra el emoji actual --}}
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl border-2 border-green-200 bg-green-50 flex items-center justify-center text-2xl select-none"
                         x-text="emojiSeleccionado || '🎾'">
                    </div>
                    <div class="flex-1 text-xs text-gray-400 leading-relaxed">
                        Este ícono aparecerá en tu saludo del panel
                    </div>
                </div>
            </div>
        </div>

        {{-- Selector de emojis de tenis --}}
        <div>
            <label class="form-label mb-2 block">Elige tu ícono</label>
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
                            @click="emojiSeleccionado = '{{ $emoji }}'"
                            :class="emojiSeleccionado === '{{ $emoji }}'
                                ? 'border-green-500 bg-green-50 scale-110 shadow-sm'
                                : 'border-gray-200 bg-white hover:border-green-300 hover:bg-green-50'"
                            class="w-11 h-11 rounded-xl border-2 text-2xl flex items-center justify-center transition-all duration-150 cursor-pointer select-none">
                        {{ $emoji }}
                    </button>
                @endforeach

                {{-- Botón quitar --}}
                <button type="button"
                        title="Sin ícono (usar 👋 por defecto)"
                        @click="emojiSeleccionado = ''"
                        :class="emojiSeleccionado === ''
                            ? 'border-gray-400 bg-gray-100'
                            : 'border-gray-200 bg-white hover:border-gray-300'"
                        class="w-11 h-11 rounded-xl border-2 text-xs text-gray-400 flex items-center justify-center transition-all duration-150 cursor-pointer font-semibold">
                    ✕
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-2">
                Vista previa del saludo:
                <span class="font-semibold text-gray-700">
                    Hola, {{ $user->name }} <span x-text="emojiSeleccionado || '👋'"></span>
                </span>
            </p>
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="form-label">Correo electrónico</label>
            <input id="email" name="email" type="email"
                   class="form-input {{ $errors->has('email') ? 'border-red-400' : '' }}"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                    Tu correo no está verificado.
                    <button form="send-verification" class="underline font-semibold hover:text-yellow-900 ml-1">
                        Reenviar verificación
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-green-600 font-medium">¡Enlace enviado!</p>
                    @endif
                </div>
            @endif
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
