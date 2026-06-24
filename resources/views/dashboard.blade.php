<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel — Top Tennis Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Figtree', sans-serif; background: #f3f4f6; }

        .grid-pattern {
            background-image:
                linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* court SVG colors */
        .court-grass  { background: linear-gradient(135deg,#166534,#15803d); }
        .court-arcilla { background: linear-gradient(135deg,#92400e,#b45309); }
        .court-sintetica { background: linear-gradient(135deg,#1e40af,#2563eb); }
        .court-default { background: linear-gradient(135deg,#065f46,#059669); }

        .tab-active {
            background: #16a34a;
            color: #fff;
            box-shadow: 0 1px 4px rgba(22,163,74,.3);
        }
        .tab-inactive { color: #6b7280; }
        .tab-inactive:hover { color: #111827; }

        .stat-icon { color: #4ade80; }
        .stat-num  { font-size: 2rem; font-weight: 800; color: #111827; line-height: 1; }
        .stat-lbl  { font-size: .8rem; color: #6b7280; margin-top: .25rem; }

        .flash { animation: fadeout 4s forwards; }
        @keyframes fadeout { 0%,80%{opacity:1} 100%{opacity:0} }
    </style>
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="bg-green-900 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-green-400 flex items-center justify-center shrink-0">
                    <svg width="22" height="22" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="28" r="26" fill="#dcfce7" stroke="#22c55e" stroke-width="3"/>
                        <path d="M6 22 Q28 13 50 22" stroke="#22c55e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                        <path d="M6 34 Q28 43 50 34" stroke="#22c55e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-extrabold text-sm tracking-widest uppercase leading-none">Top Tennis Digital</p>
                    <p class="text-green-300 text-xs mt-0.5">
                        {{ Auth::user()->name }}
                        <span class="mx-1">·</span>
                        {{ ucfirst(Auth::user()->rol->value ?? 'usuario') }}
                    </p>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-3">
                @if(in_array(Auth::user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]))
                    <a href="{{ route('canchas.index') }}" class="hidden sm:block text-green-200 hover:text-white text-sm font-semibold transition-colors">Canchas</a>
                    <a href="{{ route('tarifas.index') }}" class="hidden sm:block text-green-200 hover:text-white text-sm font-semibold transition-colors">Tarifas</a>
                    <a href="{{ route('horarios.index') }}" class="hidden sm:block text-green-200 hover:text-white text-sm font-semibold transition-colors">Horarios</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="hidden sm:block text-green-200 hover:text-white text-sm font-semibold transition-colors">Perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 bg-green-800 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors border border-green-700">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M16 17l5-5-5-5M21 12H9M13 7V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h6a2 2 0 002-2v-2"
                                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- ===== FLASH MESSAGES ===== --}}
@if(session('success') || session('error') || session('warning'))
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <div class="flash flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                <span>✕</span> {{ session('error') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="flash flex items-center gap-2 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-700 text-sm">
                <span>⚠</span> {{ session('warning') }}
            </div>
        @endif
    </div>
@endif

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- ===== TABS (solo Cliente) ===== --}}
    @if(Auth::user()->rol === \App\Enums\Rol::Cliente)
        <div x-data="{ tab: 'inicio' }">

            <div class="flex items-center gap-1 bg-white rounded-2xl p-1.5 shadow-sm border border-gray-100 w-fit">
                <button @click="tab = 'inicio'"
                        :class="tab === 'inicio' ? 'tab-active' : 'tab-inactive'"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                    Inicio
                </button>
                <button @click="tab = 'reservar'"
                        :class="tab === 'reservar' ? 'tab-active' : 'tab-inactive'"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Reservar
                </button>
                <button @click="tab = 'mis-reservas'"
                        :class="tab === 'mis-reservas' ? 'tab-active' : 'tab-inactive'"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 10h16M4 14h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Mis Reservas
                </button>
            </div>

            {{-- Tab: Inicio --}}
            <div x-show="tab === 'inicio'" class="space-y-6 mt-6">
                @include('dashboard._cliente_inicio')
            </div>

            {{-- Tab: Reservar --}}
            <div x-show="tab === 'reservar'" class="mt-6">
                @include('dashboard._reservar')
            </div>

            {{-- Tab: Mis Reservas --}}
            <div x-show="tab === 'mis-reservas'" class="mt-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Mis Reservas</h2>
                        <a href="{{ route('horarios.index') }}" class="text-sm text-green-600 font-semibold hover:underline">Ver todas</a>
                    </div>
                    <p class="text-gray-400 text-sm">Gestiona y consulta el historial de todas tus reservas.</p>
                    <a href="{{ route('horarios.index') }}"
                       class="mt-4 inline-flex items-center gap-2 border border-green-600 text-green-700 hover:bg-green-50 font-semibold px-4 py-2 rounded-xl text-sm transition-colors">
                        Ver mis reservas
                    </a>
                </div>
            </div>
        </div>

    {{-- ===== PANEL ADMIN / RECEPCIONISTA ===== --}}
    @else
        @include('dashboard._admin_inicio')
    @endif

</div>

</body>
</html>
