<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-300 text-xs font-semibold uppercase tracking-widest mb-0.5">
                    {{ now()->format('l, d \d\e F') }}
                </p>
                <h1 class="text-2xl font-black text-white leading-tight">
                    Hola, {{ explode(' ', Auth::user()->name)[0] }} 👋
                </h1>
            </div>
            {{-- Badge de rol --}}
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold"
                  style="background:rgba(255,255,255,.12); color:#a7f3d0; border:1px solid rgba(255,255,255,.15);">
                @if($esStaff)
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                @else
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M4.5 9Q12 5.5 19.5 9M4.5 15Q12 18.5 19.5 15" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                @endif
                {{ ucfirst(Auth::user()->rol->value ?? 'usuario') }}
            </span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if($esStaff)
        {{-- ══════════ PANEL ADMIN / RECEPCIÓN ══════════ --}}

            {{-- Stat cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @php
                $statItems = [
                    [
                        'label' => 'Canchas',
                        'value' => $stats['canchas'],
                        'sub'   => $stats['canchas_operativas'].' operativas',
                        'color' => '#15803d',
                        'bg'    => '#f0fdf4',
                        'icon'  => '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M3.5 9Q12 5.5 20.5 9M3.5 15Q12 18.5 20.5 15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round"/>',
                    ],
                    [
                        'label' => 'Tarifas',
                        'value' => $stats['tarifas'],
                        'sub'   => 'configuradas',
                        'color' => '#1d4ed8',
                        'bg'    => '#eff6ff',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                    [
                        'label' => 'Horarios libres',
                        'value' => $stats['horarios_disp'],
                        'sub'   => 'disponibles ahora',
                        'color' => '#7c3aed',
                        'bg'    => '#f5f3ff',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                    ],
                    [
                        'label' => 'Reservas',
                        'value' => $stats['reservas'],
                        'sub'   => 'registradas',
                        'color' => '#b45309',
                        'bg'    => '#fffbeb',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
                    ],
                ];
                @endphp

                @foreach($statItems as $s)
                    <div class="stat-card">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background:{{ $s['bg'] }};">
                                <svg class="w-5 h-5" fill="none" stroke="{{ $s['color'] }}" viewBox="0 0 24 24">
                                    {!! $s['icon'] !!}
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-black text-gray-900 leading-none">{{ $s['value'] }}</p>
                        <p class="text-sm font-semibold text-gray-700 mt-1.5">{{ $s['label'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $s['sub'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Ingresos banner --}}
            <div class="rounded-2xl p-6 mb-8 flex items-center justify-between"
                 style="background: linear-gradient(135deg, #0d3d22, #155e36); border: 1px solid rgba(255,255,255,.06);">
                <div>
                    <p class="text-green-300 text-xs font-bold uppercase tracking-widest mb-1">Ingresos confirmados</p>
                    <p class="text-4xl font-black text-white">S/ {{ number_format($stats['ingresos'], 2) }}</p>
                    <p class="text-green-400 text-xs mt-1">Basado en montos pagados registrados</p>
                </div>
                <div class="hidden sm:flex flex-col items-end gap-2">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                         style="background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.1);">
                        <svg class="w-7 h-7 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <a href="{{ route('reservas.index') }}"
                       class="text-xs font-semibold text-green-300 hover:text-white transition-colors">
                        Ver desglose →
                    </a>
                </div>
            </div>

            {{-- Accesos rápidos --}}
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Gestión rápida</p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @php
                $accesos = [
                    ['Canchas',  'canchas.index',  '#f0fdf4', '#15803d', '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M3.5 9Q12 5.5 20.5 9M3.5 15Q12 18.5 20.5 15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round"/>', 'Gestiona canchas'],
                    ['Tarifas',  'tarifas.index',  '#eff6ff', '#1d4ed8', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'Precios y planes'],
                    ['Horarios', 'horarios.index', '#f5f3ff', '#7c3aed', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>', 'Slots de canchas'],
                    ['Reservas', 'reservas.index', '#fffbeb', '#b45309', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>', 'Historial y pagos'],
                ];
                @endphp

                @foreach($accesos as [$lbl, $ruta, $bg, $color, $icon, $desc])
                    <a href="{{ route($ruta) }}"
                       class="card p-5 hover:shadow-md transition-all duration-200 group"
                       style="hover:border-color:#e0e0e0;">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3 transition-transform duration-200 group-hover:scale-110"
                             style="background:{{ $bg }};">
                            <svg class="w-5 h-5" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                                {!! $icon !!}
                            </svg>
                        </div>
                        <p class="font-bold text-gray-900 text-sm">{{ $lbl }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $desc }}</p>
                    </a>
                @endforeach
            </div>

        @else
        {{-- ══════════ PANEL CLIENTE ══════════ --}}

            {{-- Stats cliente --}}
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="stat-card">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3"
                         style="background:#f0fdf4;">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                            <path d="M3.5 9Q12 5.5 20.5 9M3.5 15Q12 18.5 20.5 15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-black text-green-700 leading-none">{{ $stats['disponibles'] }}</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1.5">Canchas disponibles</p>
                    <p class="text-xs text-gray-400 mt-0.5">horarios para reservar hoy</p>
                </div>
                <div class="stat-card">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3"
                         style="background:#fffbeb;">
                        <svg class="w-5 h-5" fill="none" stroke="#b45309" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-black text-gray-900 leading-none">{{ $stats['mis_reservas'] }}</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1.5">Mis reservas</p>
                    <p class="text-xs text-gray-400 mt-0.5">historial completo</p>
                </div>
            </div>

            {{-- Acciones cliente --}}
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">¿Qué deseas hacer?</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('reservas.disponibles') }}"
                   class="card p-7 flex items-start gap-5 hover:shadow-lg transition-all duration-200 group border-l-4"
                   style="border-left-color:#15803d;">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:scale-110"
                         style="background:#f0fdf4;">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                            <path d="M3.5 9Q12 5.5 20.5 9M3.5 15Q12 18.5 20.5 15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-extrabold text-gray-900 text-lg leading-tight">Reservar una cancha</p>
                        <p class="text-sm text-gray-400 mt-1 leading-relaxed">Mira los horarios disponibles y paga con Yape o en efectivo en recepción.</p>
                        <p class="mt-3 text-sm font-bold text-green-600 group-hover:text-green-700 flex items-center gap-1">
                            Ver canchas disponibles
                            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </p>
                    </div>
                </a>

                <a href="{{ route('reservas.index') }}"
                   class="card p-7 flex items-start gap-5 hover:shadow-lg transition-all duration-200 group border-l-4"
                   style="border-left-color:#b45309;">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:scale-110"
                         style="background:#fffbeb;">
                        <svg class="w-6 h-6" fill="none" stroke="#b45309" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-extrabold text-gray-900 text-lg leading-tight">Mis reservas</p>
                        <p class="text-sm text-gray-400 mt-1 leading-relaxed">Consulta tus tickets digitales, el estado de tus pagos y descarga comprobantes en PDF.</p>
                        <p class="mt-3 text-sm font-bold text-amber-600 group-hover:text-amber-700 flex items-center gap-1">
                            Ver historial
                            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </p>
                    </div>
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
