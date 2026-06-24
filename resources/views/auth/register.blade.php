<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Cuenta — Top Tennis Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Figtree', ui-sans-serif, sans-serif; }
        .input-field {
            width: 100%; padding: .65rem .9rem .65rem 2.4rem;
            border: 1.5px solid #e5e7eb; border-radius: .625rem;
            font-size: .95rem; outline: none; transition: border-color .2s, box-shadow .2s;
            background: #fff;
        }
        .input-field:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.15); }
        .input-field.error { border-color: #f87171; }
        .btn-green {
            width: 100%; padding: .75rem;
            background: linear-gradient(135deg, #4ade80, #22c55e);
            color: #14532d; font-weight: 700; font-size: 1rem;
            border: none; border-radius: 9999px; cursor: pointer;
            transition: opacity .2s;
        }
        .btn-green:hover { opacity: .88; }
        .panel { background: #fff; border-radius: 1.25rem; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 2.5rem; }

        .deco { position: fixed; opacity: .07; pointer-events: none; }
        .deco-tl { top: 3%;  left: 1%; }
        .deco-tr { top: 5%;  right: 1%; }
        .deco-bl { bottom: 5%; left: 2%; }
        .deco-br { bottom: 3%; right: 2%; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col overflow-x-hidden">

    {{-- Pelota arriba izquierda --}}
    <svg class="deco deco-tl" width="130" height="130" viewBox="0 0 80 80" fill="none">
        <circle cx="40" cy="40" r="34" stroke="#15803d" stroke-width="4"/>
        <path d="M12 28 Q40 18 68 28" stroke="#15803d" stroke-width="2.5" fill="none"/>
        <path d="M12 52 Q40 62 68 52" stroke="#15803d" stroke-width="2.5" fill="none"/>
    </svg>

    {{-- Raqueta arriba derecha --}}
    <svg class="deco deco-tr" width="150" height="150" viewBox="0 0 100 100" fill="none"
         style="transform: rotate(-20deg);">
        <ellipse cx="50" cy="38" rx="28" ry="32" stroke="#15803d" stroke-width="4"/>
        <line x1="50" y1="6"  x2="50" y2="70" stroke="#15803d" stroke-width="2.5"/>
        <line x1="22" y1="38" x2="78" y2="38" stroke="#15803d" stroke-width="2.5"/>
        <line x1="28" y1="18" x2="72" y2="58" stroke="#15803d" stroke-width="1.5"/>
        <line x1="28" y1="58" x2="72" y2="18" stroke="#15803d" stroke-width="1.5"/>
        <line x1="22" y1="28" x2="78" y2="28" stroke="#15803d" stroke-width="1.2"/>
        <line x1="22" y1="48" x2="78" y2="48" stroke="#15803d" stroke-width="1.2"/>
        <rect x="45" y="68" width="10" height="22" rx="5" fill="#15803d"/>
    </svg>

    {{-- Cancha abajo izquierda --}}
    <svg class="deco deco-bl" width="180" height="110" viewBox="0 0 170 110" fill="none">
        <rect x="5" y="5" width="160" height="100" stroke="#15803d" stroke-width="3" rx="2"/>
        <line x1="85"  y1="5"  x2="85"  y2="105" stroke="#15803d" stroke-width="2"/>
        <line x1="35"  y1="5"  x2="35"  y2="105" stroke="#15803d" stroke-width="1.5"/>
        <line x1="135" y1="5"  x2="135" y2="105" stroke="#15803d" stroke-width="1.5"/>
        <line x1="35"  y1="55" x2="135" y2="55"  stroke="#15803d" stroke-width="1.5"/>
        <rect x="82"   y="1"   width="6" height="108" fill="#15803d"/>
    </svg>

    {{-- Raqueta abajo derecha --}}
    <svg class="deco deco-br" width="140" height="140" viewBox="0 0 100 100" fill="none"
         style="transform: rotate(45deg);">
        <ellipse cx="50" cy="38" rx="28" ry="32" stroke="#15803d" stroke-width="4"/>
        <line x1="50" y1="6"  x2="50" y2="70" stroke="#15803d" stroke-width="2.5"/>
        <line x1="22" y1="38" x2="78" y2="38" stroke="#15803d" stroke-width="2.5"/>
        <line x1="28" y1="18" x2="72" y2="58" stroke="#15803d" stroke-width="1.5"/>
        <line x1="28" y1="58" x2="72" y2="18" stroke="#15803d" stroke-width="1.5"/>
        <line x1="22" y1="28" x2="78" y2="28" stroke="#15803d" stroke-width="1.2"/>
        <line x1="22" y1="48" x2="78" y2="48" stroke="#15803d" stroke-width="1.2"/>
        <rect x="45" y="68" width="10" height="22" rx="5" fill="#15803d"/>
    </svg>

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-100 relative z-10">
        <div class="max-w-6xl mx-auto px-6 py-4">
            <a href="/" class="flex items-center gap-2.5 w-fit">
                <div class="w-8 h-8 rounded-full border-4 border-green-400 flex items-center justify-center">
                    <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                </div>
                <span class="font-bold text-sm tracking-widest uppercase text-gray-900">Top Tennis Digital</span>
            </a>
        </div>
    </nav>

    {{-- Contenido --}}
    <div class="flex-1 flex items-center justify-center px-4 py-12 relative z-10">
        <div class="w-full max-w-md">

            {{-- Header con raqueta SVG --}}
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <svg width="56" height="56" viewBox="0 0 100 100" fill="none">
                        <circle cx="50" cy="50" r="48" fill="#dcfce7" stroke="#22c55e" stroke-width="3"/>
                        <ellipse cx="50" cy="36" rx="22" ry="26" stroke="#15803d" stroke-width="4"/>
                        <line x1="50" y1="10" x2="50" y2="62" stroke="#15803d" stroke-width="3"/>
                        <line x1="28" y1="36" x2="72" y2="36" stroke="#15803d" stroke-width="3"/>
                        <line x1="33" y1="18" x2="67" y2="54" stroke="#15803d" stroke-width="2"/>
                        <line x1="33" y1="54" x2="67" y2="18" stroke="#15803d" stroke-width="2"/>
                        <rect x="46" y="62" width="8" height="20" rx="4" fill="#15803d"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">Crear cuenta gratis</h1>
                <p class="text-gray-400 text-sm mt-1">Reservá tu primera cancha en menos de 2 minutos</p>
            </div>

            <div class="panel">
                <form method="POST" action="{{ route('register') }}" x-data="{ password: '', confirm: '' }">
                    @csrf

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nombre completo
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                            <input
                                id="name" name="name" type="text"
                                value="{{ old('name') }}"
                                class="input-field {{ $errors->has('name') ? 'error' : '' }}"
                                placeholder="Juan Pérez"
                                required autofocus autocomplete="name"
                            >
                        </div>
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Correo electrónico
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="2"/><path d="M2 8l10 6 10-6" stroke="currentColor" stroke-width="2"/></svg>
                            </span>
                            <input
                                id="email" name="email" type="email"
                                value="{{ old('email') }}"
                                class="input-field {{ $errors->has('email') ? 'error' : '' }}"
                                placeholder="tu@correo.com"
                                required autocomplete="username"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contraseña --}}
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Contraseña
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                            <input
                                id="password" name="password" type="password"
                                x-model="password"
                                class="input-field {{ $errors->has('password') ? 'error' : '' }}"
                                placeholder="Mínimo 8 caracteres"
                                required autocomplete="new-password"
                            >
                        </div>
                        {{-- Barra de fortaleza --}}
                        <div class="mt-1.5 flex gap-1">
                            <div class="flex-1 h-1 rounded-full transition-colors duration-300"
                                :class="password.length === 0 ? 'bg-gray-200' : password.length < 6 ? 'bg-red-400' : password.length < 10 ? 'bg-yellow-400' : 'bg-green-400'">
                            </div>
                            <div class="flex-1 h-1 rounded-full transition-colors duration-300"
                                :class="password.length >= 6 ? (password.length < 10 ? 'bg-yellow-400' : 'bg-green-400') : 'bg-gray-200'">
                            </div>
                            <div class="flex-1 h-1 rounded-full transition-colors duration-300"
                                :class="password.length >= 10 ? 'bg-green-400' : 'bg-gray-200'">
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1" x-text="
                            password.length === 0 ? '' :
                            password.length < 6   ? 'Contraseña débil' :
                            password.length < 10  ? 'Contraseña media' :
                            'Contraseña fuerte ✓'
                        "></p>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Confirmar contraseña
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <input
                                id="password_confirmation" name="password_confirmation" type="password"
                                x-model="confirm"
                                class="input-field {{ $errors->has('password_confirmation') ? 'error' : '' }}"
                                placeholder="Repetí tu contraseña"
                                required autocomplete="new-password"
                            >
                        </div>
                        <p class="mt-1.5 text-xs text-red-500"
                           x-show="confirm.length > 0 && password !== confirm">
                            Las contraseñas no coinciden
                        </p>
                        @error('password_confirmation')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-green">
                        Crear mi cuenta
                    </button>
                </form>

                {{-- Separador raqueta --}}
                <div class="flex items-center gap-3 my-5">
                    <div class="flex-1 h-px bg-gray-100"></div>
                    <svg width="18" height="18" viewBox="0 0 80 80" fill="none" class="text-gray-300">
                        <circle cx="40" cy="40" r="34" stroke="currentColor" stroke-width="5"/>
                        <path d="M10 28 Q40 16 70 28" stroke="currentColor" stroke-width="3" fill="none"/>
                        <path d="M10 52 Q40 64 70 52" stroke="currentColor" stroke-width="3" fill="none"/>
                    </svg>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                <p class="text-center text-sm text-gray-500">
                    ¿Ya tienes cuenta?
                    <a href="{{ route('login') }}" class="text-green-600 font-semibold hover:underline">
                        Iniciar sesión
                    </a>
                </p>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                <a href="/" class="hover:text-green-600 transition-colors">← Volver al inicio</a>
            </p>
        </div>
    </div>

</body>
</html>
