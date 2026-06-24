<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión — Top Tennis Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Figtree', ui-sans-serif, sans-serif; }
        .input-field {
            width: 100%; padding: .65rem .9rem;
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

        /* Decoraciones flotantes */
        .deco { position: fixed; opacity: .07; pointer-events: none; }
        .deco-tl { top: 5%;  left: 2%; }
        .deco-tr { top: 8%;  right: 2%; }
        .deco-bl { bottom: 8%; left: 3%; }
        .deco-br { bottom: 5%; right: 2%; }
        .deco-mc { top: 45%; left: 48%; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col overflow-x-hidden">

    {{-- Decoraciones SVG tenis --}}

    {{-- Raqueta arriba izquierda --}}
    <svg class="deco deco-tl" width="140" height="140" viewBox="0 0 100 100" fill="none">
        <ellipse cx="50" cy="38" rx="28" ry="32" stroke="#15803d" stroke-width="4"/>
        <line x1="50" y1="6"  x2="50" y2="70" stroke="#15803d" stroke-width="2.5"/>
        <line x1="22" y1="38" x2="78" y2="38" stroke="#15803d" stroke-width="2.5"/>
        <line x1="28" y1="18" x2="72" y2="58" stroke="#15803d" stroke-width="1.5"/>
        <line x1="28" y1="58" x2="72" y2="18" stroke="#15803d" stroke-width="1.5"/>
        <line x1="22" y1="28" x2="78" y2="28" stroke="#15803d" stroke-width="1.2"/>
        <line x1="22" y1="48" x2="78" y2="48" stroke="#15803d" stroke-width="1.2"/>
        <rect x="45" y="68" width="10" height="22" rx="5" fill="#15803d"/>
    </svg>

    {{-- Pelota arriba derecha --}}
    <svg class="deco deco-tr" width="120" height="120" viewBox="0 0 80 80" fill="none">
        <circle cx="40" cy="40" r="34" stroke="#15803d" stroke-width="4"/>
        <path d="M12 28 Q40 18 68 28" stroke="#15803d" stroke-width="2.5" fill="none"/>
        <path d="M12 52 Q40 62 68 52" stroke="#15803d" stroke-width="2.5" fill="none"/>
    </svg>

    {{-- Cancha abajo izquierda --}}
    <svg class="deco deco-bl" width="170" height="110" viewBox="0 0 170 110" fill="none">
        <rect x="5" y="5" width="160" height="100" stroke="#15803d" stroke-width="3" rx="2"/>
        <line x1="85"  y1="5"  x2="85"  y2="105" stroke="#15803d" stroke-width="2"/>
        <line x1="35"  y1="5"  x2="35"  y2="105" stroke="#15803d" stroke-width="1.5"/>
        <line x1="135" y1="5"  x2="135" y2="105" stroke="#15803d" stroke-width="1.5"/>
        <line x1="35"  y1="55" x2="135" y2="55"  stroke="#15803d" stroke-width="1.5"/>
        <line x1="85"  y1="25" x2="85"  y2="85"  stroke="#15803d" stroke-width="1.5"/>
        <rect x="82"   y="1"   width="6" height="108" fill="#15803d"/>
    </svg>

    {{-- Raqueta abajo derecha (rotada) --}}
    <svg class="deco deco-br" width="130" height="130" viewBox="0 0 100 100" fill="none"
         style="transform: rotate(35deg);">
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

            {{-- Header con pelota de tenis SVG --}}
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="28" r="26" fill="#dcfce7" stroke="#22c55e" stroke-width="3"/>
                        <path d="M8 20 Q28 12 48 20" stroke="#22c55e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                        <path d="M8 36 Q28 44 48 36" stroke="#22c55e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">Bienvenido de vuelta</h1>
                <p class="text-gray-400 text-sm mt-1">Ingresá a tu cuenta para gestionar tus reservas</p>
            </div>

            <div class="panel">

                @if (session('status'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm rounded-lg">
                        {{ session('warning') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

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
                                class="input-field pl-9 {{ $errors->has('email') ? 'error' : '' }}"
                                placeholder="tu@correo.com"
                                required autofocus autocomplete="username"
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
                                class="input-field pl-9 {{ $errors->has('password') ? 'error' : '' }}"
                                placeholder="••••••••"
                                required autocomplete="current-password"
                            >
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-300 accent-green-500">
                            <span class="text-sm text-gray-600">Recordarme</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-sm text-green-600 hover:text-green-700 font-medium">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-green">
                        Iniciar Sesión
                    </button>
                </form>

                {{-- Separador con icono raqueta --}}
                <div class="flex items-center gap-3 my-5">
                    <div class="flex-1 h-px bg-gray-100"></div>
                    <svg width="18" height="18" viewBox="0 0 100 100" fill="none" class="text-gray-300">
                        <ellipse cx="50" cy="36" rx="28" ry="32" stroke="currentColor" stroke-width="6"/>
                        <line x1="50" y1="4"  x2="50" y2="68" stroke="currentColor" stroke-width="4"/>
                        <line x1="22" y1="36" x2="78" y2="36" stroke="currentColor" stroke-width="4"/>
                        <rect x="45" y="66" width="10" height="24" rx="5" fill="currentColor"/>
                    </svg>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                @if (Route::has('register'))
                    <p class="text-center text-sm text-gray-500">
                        ¿No tenés cuenta?
                        <a href="{{ route('register') }}" class="text-green-600 font-semibold hover:underline">
                            Registrate gratis
                        </a>
                    </p>
                @endif
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                <a href="/" class="hover:text-green-600 transition-colors">← Volver al inicio</a>
            </p>
        </div>
    </div>

</body>
</html>
