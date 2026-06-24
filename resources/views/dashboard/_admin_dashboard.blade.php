@php
    $diasEs = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    $mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $fechaHoy = $diasEs[now()->dayOfWeek] . ', ' . now()->day . ' de ' . $mesesEs[now()->month - 1] . ' de ' . now()->year;

    $courtBg = fn($tipo) => match(strtolower($tipo ?? '')) {
        'arcilla'            => ['bg'=>'#fff7ed','border'=>'#fed7aa','text'=>'#9a3412','icon'=>'#f97316','estado_color'=>'text-orange-400'],
        'sintética','sintetica' => ['bg'=>'#eff6ff','border'=>'#bfdbfe','text'=>'#1e40af','icon'=>'#3b82f6','estado_color'=>'text-blue-400'],
        default              => ['bg'=>'#f0fdf4','border'=>'#bbf7d0','text'=>'#166534','icon'=>'#22c55e','estado_color'=>'text-green-400'],
    };
@endphp

{{-- Título --}}
<div class="mb-6">
    <h1 class="text-2xl font-black text-gray-900">Dashboard General</h1>
    <p class="text-gray-400 text-sm mt-0.5">{{ $fechaHoy }}</p>
</div>

{{-- ===== 4 STAT CARDS ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Ingresos Hoy (green) --}}
    <div class="rounded-2xl p-5" style="background: linear-gradient(135deg,#4ade80,#22c55e);">
        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mb-3">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M12 2v2M12 20v2M6 12H4M20 12h-2" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="6" stroke="white" stroke-width="2"/>
                <path d="M12 8v4l2 2" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="text-white/80 text-xs font-semibold mb-1">Ingresos Hoy</p>
        <p class="text-white text-2xl font-black">S/ {{ number_format($ingresosHoy, 0) }}</p>
    </div>

    {{-- Ingresos Total --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mb-3">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M3 17l4-4 4 4 4-6 4-4" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <p class="text-gray-400 text-xs font-semibold mb-1">Ingresos Total</p>
        <p class="text-green-900 text-2xl font-black">S/ {{ number_format($ingresosTotal, 0) }}</p>
    </div>

    {{-- Reservas Totales --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mb-3">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke="#22c55e" stroke-width="2"/>
                <path d="M16 2v4M8 2v4M3 10h18" stroke="#22c55e" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="text-gray-400 text-xs font-semibold mb-1">Reservas Totales</p>
        <p class="text-green-900 text-2xl font-black">{{ $reservasTotales }}</p>
    </div>

    {{-- Reservas Hoy --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mb-3">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <circle cx="9" cy="7" r="3" stroke="#22c55e" stroke-width="2"/>
                <circle cx="17" cy="7" r="3" stroke="#22c55e" stroke-width="2"/>
                <path d="M3 20c0-3 2.7-5 6-5" stroke="#22c55e" stroke-width="2" stroke-linecap="round"/>
                <path d="M21 20c0-3-2.7-5-6-5s-6 2-6 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="text-gray-400 text-xs font-semibold mb-1">Reservas Hoy</p>
        <p class="text-green-900 text-2xl font-black">{{ $reservasHoy }}</p>
    </div>
</div>

{{-- ===== ESTADO CANCHAS + HORARIOS ACTIVOS ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Estado de Canchas --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
            <h2 class="font-bold text-green-900 text-base">Estado de Canchas</h2>
        </div>
        <div class="grid grid-cols-2 gap-3">
            @foreach($canchas as $cancha)
                @php
                    $operativa = $cancha->estado === 'Disponible';
                    $colors = $courtBg($cancha->tipo);
                    $precioMin = $cancha->tarifas->min('precio_hora');
                @endphp
                <div class="rounded-2xl p-4 border"
                     style="background:{{ $colors['bg'] }}; border-color:{{ $colors['border'] }};">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="font-bold text-sm" style="color:{{ $colors['text'] }}">{{ $cancha->nombre }}</p>
                            <p class="text-xs mt-0.5" style="color:{{ $colors['text'] }}; opacity:.7">
                                {{ $operativa ? 'Operativa' : 'Mantenimiento' }}
                            </p>
                        </div>
                        @if($operativa)
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="9" stroke="{{ $colors['icon'] }}" stroke-width="2"/>
                                <path d="M9 12l2 2 4-4" stroke="{{ $colors['icon'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="#f97316" stroke-width="2" stroke-linejoin="round"/>
                                <line x1="12" y1="9" x2="12" y2="13" stroke="#f97316" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="12" cy="17" r="1" fill="#f97316"/>
                            </svg>
                        @endif
                    </div>
                    <p class="text-xs" style="color:{{ $colors['text'] }}; opacity:.7">
                        {{ $cancha->tipo }}
                        @if($precioMin) · S/ {{ number_format($precioMin,0) }}/hr @endif
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Horarios más activos --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-2 mb-5">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="#22c55e" stroke-width="2"/>
                <path d="M12 8v4l3 3" stroke="#22c55e" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <h2 class="font-bold text-green-900 text-base">Horarios más activos</h2>
        </div>

        @if($horariosActivos->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" class="text-gray-200 mb-3">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <p class="text-gray-300 text-sm">Sin reservas.</p>
                <p class="text-gray-300 text-xs">El gráfico aparecerá aquí.</p>
            </div>
        @else
            @php $maxTotal = $horariosActivos->max('total'); @endphp
            <div class="space-y-3">
                @foreach($horariosActivos as $slot)
                    @php $pct = $maxTotal > 0 ? ($slot->total / $maxTotal * 100) : 0; @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-mono text-gray-500 w-12 shrink-0">{{ substr($slot->hora_inicio,0,5) }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-green-400 to-green-600 transition-all"
                                 style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-green-700 w-6 text-right">{{ $slot->total }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
