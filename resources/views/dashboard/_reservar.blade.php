@php
    $diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    $meses      = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    $dias = [];
    for ($i = 0; $i < 7; $i++) {
        $ts = strtotime("+{$i} days");
        $dias[] = [
            'label'  => $i === 0 ? 'Hoy' : ($i === 1 ? 'Mañana' : $diasSemana[date('w', $ts)]),
            'num'    => date('j', $ts),
            'mes'    => $meses[(int)date('n', $ts) - 1],
            'value'  => date('Y-m-d', $ts),
        ];
    }

    $slots = ['08:00','09:00','10:00','11:00','12:00','13:00',
              '14:00','15:00','16:00','17:00','18:00','19:00','20:00'];

    $tipoBg = fn($tipo) => match(strtolower($tipo)) {
        'arcilla'            => 'court-arcilla',
        'sintética','sintetica' => 'court-sintetica',
        'grass','hierba'     => 'court-grass',
        default              => 'court-default',
    };
    $tipoLabel = fn($tipo) => match(strtolower($tipo)) {
        'arcilla'            => 'Clay',
        'sintética','sintetica' => 'Synthetic',
        'grass','hierba'     => 'Grass',
        default              => $tipo,
    };
@endphp

<div x-data="{
        diaSeleccionado: '{{ $dias[0]['value'] }}',
        horaSeleccionada: '',
        canchaSeleccionada: null,
        get listo() { return this.diaSeleccionado && this.horaSeleccionada && this.canchaSeleccionada; }
     }">

    <h2 class="text-xl font-bold text-gray-900 mb-6">Nueva Reserva</h2>

    {{-- ===== PASO 1: DÍA ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-bold shrink-0">1</div>
            <div class="flex items-center gap-2 text-green-900 font-bold">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Selecciona el Día
            </div>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-1">
            @foreach($dias as $dia)
                <button type="button"
                        @click="diaSeleccionado = '{{ $dia['value'] }}'"
                        :class="diaSeleccionado === '{{ $dia['value'] }}'
                            ? 'border-green-500 bg-green-50 text-green-800 shadow-sm'
                            : 'border-gray-200 bg-white text-gray-500 hover:border-green-300'"
                        class="flex flex-col items-center px-5 py-3 rounded-2xl border-2 transition-all shrink-0 min-w-[80px] cursor-pointer">
                    <span class="text-xs font-medium mb-1">{{ $dia['label'] }}</span>
                    <span class="text-2xl font-black leading-none" :class="diaSeleccionado === '{{ $dia['value'] }}' ? 'text-green-700' : 'text-gray-800'">{{ $dia['num'] }}</span>
                    <span class="text-xs mt-1">{{ $dia['mes'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ===== PASO 2: HORARIO ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition-colors"
                 :class="diaSeleccionado ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">2</div>
            <div class="flex items-center gap-2 font-bold" :class="diaSeleccionado ? 'text-green-900' : 'text-gray-300'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Selecciona el Horario
            </div>
        </div>
        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
            @foreach($slots as $slot)
                <button type="button"
                        :disabled="!diaSeleccionado"
                        @click="horaSeleccionada = '{{ $slot }}'"
                        :class="horaSeleccionada === '{{ $slot }}'
                            ? 'border-green-500 bg-green-50 text-green-800 font-bold shadow-sm'
                            : (!diaSeleccionado ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-500 hover:border-green-300 cursor-pointer')"
                        class="py-2.5 rounded-xl border-2 text-sm transition-all text-center">
                    {{ $slot }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ===== PASO 3: CANCHA ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition-colors"
                 :class="horaSeleccionada ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">3</div>
            <div class="flex items-center gap-2 font-bold" :class="horaSeleccionada ? 'text-green-900' : 'text-gray-300'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><line x1="12" y1="3" x2="12" y2="21" stroke="currentColor" stroke-width="1.5"/><line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="1.5"/><line x1="3" y1="15" x2="21" y2="15" stroke="currentColor" stroke-width="1.5"/></svg>
                Elige tu Cancha
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($canchas as $cancha)
                @php
                    $precioMin = $cancha->tarifas->min('precio_hora');
                    $bg = $tipoBg($cancha->tipo);
                    $label = $tipoLabel($cancha->tipo);
                @endphp
                @if($cancha->estado === 'Disponible')
                <button type="button"
                        :disabled="!horaSeleccionada"
                        @click="canchaSeleccionada = {{ $cancha->id }}"
                        :class="canchaSeleccionada === {{ $cancha->id }}
                            ? 'ring-2 ring-green-500 ring-offset-2 opacity-100'
                            : (!horaSeleccionada ? 'opacity-40 cursor-not-allowed' : 'hover:ring-2 hover:ring-green-300 hover:ring-offset-1 cursor-pointer')"
                        class="bg-white rounded-2xl border border-gray-200 overflow-hidden text-left transition-all">
                    {{-- Court image --}}
                    <div class="{{ $bg }} h-28 relative flex items-start p-3">
                        <span class="bg-black/30 text-white text-xs font-bold px-2 py-1 rounded-lg">{{ $label }}</span>
                        <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 280 112" preserveAspectRatio="xMidYMid slice" fill="none">
                            <rect x="15" y="10" width="250" height="92" stroke="white" stroke-width="2"/>
                            <line x1="140" y1="10" x2="140" y2="102" stroke="white" stroke-width="1.5"/>
                            <line x1="15" y1="56" x2="265" y2="56" stroke="white" stroke-width="1.5"/>
                            <rect x="55" y="10" width="170" height="92" stroke="white" stroke-width="1"/>
                            <ellipse cx="140" cy="56" rx="20" ry="20" stroke="white" stroke-width="1.5"/>
                        </svg>
                        {{-- Check seleccionado --}}
                        <div x-show="canchaSeleccionada === {{ $cancha->id }}"
                             class="absolute top-3 right-3 w-7 h-7 bg-green-400 rounded-full flex items-center justify-center shadow">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 text-sm">{{ $cancha->nombre }}</span>
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ $cancha->tipo }}</span>
                            </div>
                            <p class="text-gray-400 text-xs mt-0.5">{{ $label }}</p>
                        </div>
                        @if($precioMin)
                            <div class="text-right">
                                <p class="font-black text-gray-900 text-sm">S/ {{ number_format($precioMin, 0) }}</p>
                                <p class="text-gray-400 text-xs">por hora</p>
                            </div>
                        @endif
                    </div>
                </button>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ===== RESUMEN + BOTÓN CONFIRMAR ===== --}}
    <div x-show="listo"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-green-50 border border-green-200 rounded-2xl p-5">
        <p class="text-sm font-semibold text-green-800 mb-3">Resumen de tu reserva</p>
        <div class="flex flex-wrap gap-4 text-sm text-green-700 mb-4">
            <span>📅 <span x-text="diaSeleccionado"></span></span>
            <span>🕐 <span x-text="horaSeleccionada"></span></span>
            <span>🎾 Cancha seleccionada</span>
        </div>
        <form method="GET" :action="'{{ route('horarios.create') }}'">
            <input type="hidden" name="fecha"      :value="diaSeleccionado">
            <input type="hidden" name="hora_inicio" :value="horaSeleccionada">
            <input type="hidden" name="cancha_id"  :value="canchaSeleccionada">
            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                Continuar con la reserva
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>
    </div>

</div>
