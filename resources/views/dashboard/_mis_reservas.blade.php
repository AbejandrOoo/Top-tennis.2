@php
    $badgeColor = fn($estado) => match($estado) {
        'Confirmado' => 'bg-green-100 text-green-700',
        'Reservado'  => 'bg-yellow-100 text-yellow-700',
        'Cancelado'  => 'bg-red-100 text-red-600',
        'Completado' => 'bg-gray-100 text-gray-500',
        default      => 'bg-gray-100 text-gray-400',
    };
    $courtBg = fn($tipo) => match(strtolower($tipo ?? '')) {
        'arcilla'            => 'from-orange-800 to-orange-600',
        'sintética','sintetica' => 'from-blue-800 to-blue-600',
        'grass','hierba'     => 'from-green-900 to-green-700',
        default              => 'from-green-900 to-green-700',
    };
@endphp

<div class="flex items-center justify-between mb-5 w-full">
    <h2 class="text-xl font-bold text-gray-900">Mis Reservas</h2>
    <span class="text-sm text-gray-400 font-medium">{{ $misReservas->count() }} total</span>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    @if($misReservas->isEmpty())
        {{-- Estado vacío --}}
        <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
            <div class="mb-5 text-gray-300">
                <svg width="52" height="52" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <p class="text-gray-400 text-base mb-6">No tienes reservas todavía</p>
            <button type="button"
                    @click="tab = 'reservar'"
                    class="bg-green-500 hover:bg-green-400 text-white font-bold px-7 py-3 rounded-2xl text-sm transition-colors">
                Hacer mi primera reserva
            </button>
        </div>

    @else
        {{-- Lista de reservas --}}
        <div class="divide-y divide-gray-100">
            @foreach($misReservas as $reserva)
                @php
                    $bg = $courtBg($reserva->cancha->tipo ?? '');
                @endphp
                <div class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors">

                    {{-- Mini court thumb --}}
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br {{ $bg }} flex items-center justify-center shrink-0 relative overflow-hidden">
                        <svg class="absolute inset-0 w-full h-full opacity-30" viewBox="0 0 56 56" preserveAspectRatio="xMidYMid slice" fill="none">
                            <rect x="4" y="4" width="48" height="48" stroke="white" stroke-width="1.5"/>
                            <line x1="28" y1="4" x2="28" y2="52" stroke="white" stroke-width="1"/>
                            <line x1="4" y1="28" x2="52" y2="28" stroke="white" stroke-width="1"/>
                            <ellipse cx="28" cy="28" rx="10" ry="10" stroke="white" stroke-width="1"/>
                        </svg>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="relative z-10">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke="white" stroke-width="1.8"/>
                            <line x1="12" y1="3" x2="12" y2="21" stroke="white" stroke-width="1.2"/>
                            <line x1="3" y1="9" x2="21" y2="9" stroke="white" stroke-width="1.2"/>
                            <line x1="3" y1="15" x2="21" y2="15" stroke="white" stroke-width="1.2"/>
                        </svg>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-bold text-gray-900 text-sm">{{ $reserva->cancha->nombre ?? 'Cancha' }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $badgeColor($reserva->estado) }} font-semibold">
                                {{ $reserva->estado }}
                            </span>
                        </div>
                        <p class="text-gray-400 text-xs mt-0.5">
                            {{ $reserva->cancha->tipo ?? '' }}
                            · {{ \Carbon\Carbon::parse($reserva->fecha)->translatedFormat('d M Y') }}
                            · {{ substr($reserva->hora_inicio, 0, 5) }} – {{ substr($reserva->hora_fin, 0, 5) }}
                        </p>
                        @if($reserva->tarifa)
                            <p class="text-green-700 text-xs font-semibold mt-0.5">
                                S/. {{ number_format($reserva->tarifa->precio_hora, 2) }}/hr
                            </p>
                        @endif
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @if(in_array($reserva->estado, ['Reservado', 'Confirmado']))
                            <a href="{{ route('horarios.edit', $reserva) }}"
                               class="text-xs border border-gray-200 hover:border-green-400 text-gray-500 hover:text-green-700 px-3 py-1.5 rounded-xl transition-colors font-semibold">
                                Editar
                            </a>
                        @endif
                        <div class="text-right">
                            <p class="text-xs text-gray-300">{{ $reserva->fecha instanceof \Carbon\Carbon ? $reserva->fecha->diffForHumans() : '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="p-4 border-t border-gray-100 text-center">
            <a href="{{ route('horarios.index') }}" class="text-sm text-green-600 font-semibold hover:underline">
                Ver historial completo →
            </a>
        </div>
    @endif
</div>
