@php
    $emoji = Auth::user()->emoji_perfil ?: null;
    $tipoBg = fn($tipo) => match(strtolower($tipo)) {
        'arcilla'  => 'court-arcilla',
        'sintética','sintetica' => 'court-sintetica',
        'grass','hierba' => 'court-grass',
        default    => 'court-default',
    };
    $tipoLabel = fn($tipo) => match(strtolower($tipo)) {
        'arcilla'  => 'Clay',
        'sintética','sintetica' => 'Synthetic',
        'grass','hierba' => 'Grass',
        default    => $tipo,
    };
@endphp

{{-- ===== HERO ===== --}}
<div class="relative bg-green-900 rounded-3xl overflow-hidden grid-pattern p-8 flex items-center justify-between min-h-[200px]">
    <div class="relative z-10">
        <p class="text-green-400 text-sm font-semibold tracking-wide mb-1">Bienvenido de vuelta</p>
        <h1 class="text-white text-4xl font-black mb-2 leading-none">
            {{ Auth::user()->name }}
        </h1>
        <p class="text-green-200 text-sm mb-6">Reserva tu cancha en segundos · Cancela cuando necesites</p>
        <button type="button" @click="tab = 'reservar'"
           class="inline-flex items-center gap-2 bg-green-400 hover:bg-green-300 text-green-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors cursor-pointer">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2.2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
            Reservar Ahora
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </div>

    {{-- Ícono persona / emoji --}}
    <div class="relative z-10 w-20 h-20 rounded-full bg-green-700 flex items-center justify-center shrink-0 ml-6">
        @if($emoji)
            <span class="text-4xl">{{ $emoji }}</span>
        @else
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="7" r="4" stroke="#4ade80" stroke-width="2"/>
                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="#4ade80" stroke-width="2" stroke-linecap="round"/>
            </svg>
        @endif
    </div>

    {{-- Decoración court lines --}}
    <svg class="absolute inset-0 w-full h-full opacity-10" viewBox="0 0 600 200" preserveAspectRatio="xMidYMid slice" fill="none">
        <rect x="60" y="20" width="480" height="160" stroke="white" stroke-width="2"/>
        <line x1="300" y1="20" x2="300" y2="180" stroke="white" stroke-width="1.5"/>
        <line x1="60" y1="100" x2="540" y2="100" stroke="white" stroke-width="1.5"/>
        <rect x="150" y="20" width="300" height="160" stroke="white" stroke-width="1"/>
        <ellipse cx="300" cy="100" rx="28" ry="28" stroke="white" stroke-width="1.5"/>
    </svg>
</div>

{{-- ===== STATS ===== --}}
<div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="flex justify-center mb-2">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="stat-icon">
                <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="2"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="2" x2="12" y2="4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="12" y1="20" x2="12" y2="22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="2" y1="12" x2="4" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="20" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="stat-num">{{ $canchasLibres }}</p>
        <p class="stat-lbl">Canchas libres</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="flex justify-center mb-2">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="stat-icon">
                <path d="M4 6h16M4 10h16M4 14h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="stat-num">{{ $reservasActivas }}</p>
        <p class="stat-lbl">Mis reservas activas</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="flex justify-center mb-2">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="stat-icon">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="stat-num">{{ $horariosDisp }}</p>
        <p class="stat-lbl">Horarios disponibles</p>
    </div>
</div>

{{-- ===== NUESTRAS CANCHAS ===== --}}
@if($canchas->isNotEmpty())
<div>
    <h2 class="text-lg font-bold text-gray-900 mb-4">Nuestras Canchas</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($canchas as $cancha)
            @php
                $precioMin = $cancha->tarifas->min('precio_hora');
                $bg = $tipoBg($cancha->tipo);
                $label = $tipoLabel($cancha->tipo);
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                {{-- Court visual --}}
                <div class="{{ $bg }} h-32 relative flex items-start p-3">
                    <span class="bg-black/30 text-white text-xs font-bold px-2 py-1 rounded-lg">{{ $label }}</span>
                    {{-- Mini court lines --}}
                    <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 200 128" preserveAspectRatio="xMidYMid slice" fill="none">
                        <rect x="20" y="15" width="160" height="98" stroke="white" stroke-width="2"/>
                        <line x1="100" y1="15" x2="100" y2="113" stroke="white" stroke-width="1.5"/>
                        <line x1="20" y1="64" x2="180" y2="64" stroke="white" stroke-width="1.5"/>
                        <rect x="50" y="15" width="100" height="98" stroke="white" stroke-width="1"/>
                        <ellipse cx="100" cy="64" rx="18" ry="18" stroke="white" stroke-width="1.5"/>
                    </svg>
                    {{-- Disponibilidad --}}
                    <span class="absolute bottom-3 right-3 text-xs font-semibold px-2.5 py-1 rounded-full
                        {{ $cancha->estado === 'Disponible' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $cancha->estado }}
                    </span>
                </div>

                <div class="p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-gray-900">{{ $cancha->nombre }}</h3>
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ $cancha->tipo }}</span>
                    </div>
                    <p class="text-gray-400 text-xs">
                        {{ $label }}
                        @if($precioMin)
                            · <span class="text-green-700 font-semibold">S/. {{ number_format($precioMin, 2) }}/hr</span>
                        @endif
                    </p>
                    @if($cancha->estado === 'Disponible')
                        <button type="button" @click="tab = 'reservar'"
                           class="mt-3 w-full block text-center bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-2 rounded-xl transition-colors cursor-pointer">
                            Reservar
                        </button>
                    @else
                        <div class="mt-3 text-center text-xs text-gray-400 py-2 rounded-xl border border-gray-100">
                            No disponible
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
