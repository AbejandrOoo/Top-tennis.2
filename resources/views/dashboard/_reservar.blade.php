@php
    $diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    $meses      = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    $dias = [];
    for ($i = 0; $i < 7; $i++) {
        $ts = strtotime("+{$i} days");
        $dias[] = [
            'label' => $i === 0 ? 'Hoy' : ($i === 1 ? 'Mañana' : $diasSemana[date('w', $ts)]),
            'num'   => date('j', $ts),
            'mes'   => $meses[(int)date('n', $ts) - 1],
            'value' => date('Y-m-d', $ts),
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

    // Todas las tarifas activas para pasarlas a Alpine
    $todasTarifas = \App\Models\Tarifa::where('estado','Activa')->get(['id','cancha_id','precio_hora','hora_inicio','hora_fin','turno']);
@endphp

<div x-data="reservarWizard({{ $todasTarifas->toJson() }})">

    {{-- Errores de validación del servidor --}}
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <h2 class="text-xl font-bold text-gray-900 mb-6">Nueva Reserva</h2>

    {{-- PASO 1: DÍA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-bold shrink-0">1</div>
            <span class="flex items-center gap-2 text-green-900 font-bold">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Selecciona el Día
            </span>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-1">
            @foreach($dias as $dia)
                <button type="button"
                        @click="setDia('{{ $dia['value'] }}')"
                        :class="diaSeleccionado === '{{ $dia['value'] }}'
                            ? 'border-green-500 bg-green-50 text-green-800 shadow-sm'
                            : 'border-gray-200 bg-white text-gray-500 hover:border-green-300'"
                        class="flex flex-col items-center px-5 py-3 rounded-2xl border-2 transition-all shrink-0 min-w-[80px] cursor-pointer">
                    <span class="text-xs font-medium mb-1">{{ $dia['label'] }}</span>
                    <span class="text-2xl font-black leading-none"
                          :class="diaSeleccionado === '{{ $dia['value'] }}' ? 'text-green-700' : 'text-gray-800'">{{ $dia['num'] }}</span>
                    <span class="text-xs mt-1">{{ $dia['mes'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- PASO 2: HORARIO --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition-colors"
                 :class="diaSeleccionado ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">2</div>
            <span class="flex items-center gap-2 font-bold transition-colors"
                  :class="diaSeleccionado ? 'text-green-900' : 'text-gray-300'">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Selecciona el Horario
            </span>
        </div>
        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
            @foreach($slots as $slot)
                <button type="button"
                        :disabled="!diaSeleccionado"
                        @click="setHora('{{ $slot }}')"
                        :class="horaSeleccionada === '{{ $slot }}'
                            ? 'border-green-500 bg-green-50 text-green-800 font-bold shadow-sm'
                            : (!diaSeleccionado ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-500 hover:border-green-300 cursor-pointer')"
                        class="py-2.5 rounded-xl border-2 text-sm transition-all text-center">
                    {{ $slot }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- PASO 3: CANCHA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition-colors"
                 :class="horaSeleccionada ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">3</div>
            <span class="flex items-center gap-2 font-bold transition-colors"
                  :class="horaSeleccionada ? 'text-green-900' : 'text-gray-300'">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><line x1="12" y1="3" x2="12" y2="21" stroke="currentColor" stroke-width="1.5"/><line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="1.5"/><line x1="3" y1="15" x2="21" y2="15" stroke="currentColor" stroke-width="1.5"/></svg>
                Elige tu Cancha
            </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($canchas->where('estado','Disponible') as $cancha)
                @php
                    $precioMin = $cancha->tarifas->min('precio_hora');
                    $bg = $tipoBg($cancha->tipo);
                    $label = $tipoLabel($cancha->tipo);
                @endphp
                <button type="button"
                        :disabled="!horaSeleccionada"
                        @click="setCancha({{ $cancha->id }}, '{{ addslashes($cancha->nombre) }}', '{{ $label }}', {{ $precioMin ?? 0 }})"
                        :class="canchaId === {{ $cancha->id }}
                            ? 'ring-2 ring-green-500 ring-offset-2 opacity-100'
                            : (!horaSeleccionada ? 'opacity-40 cursor-not-allowed' : 'hover:ring-2 hover:ring-green-200 hover:ring-offset-1 cursor-pointer')"
                        class="bg-white rounded-2xl border border-gray-200 overflow-hidden text-left transition-all">
                    <div class="{{ $bg }} h-28 relative flex items-start p-3">
                        <span class="bg-black/30 text-white text-xs font-bold px-2 py-1 rounded-lg">{{ $label }}</span>
                        <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 280 112" preserveAspectRatio="xMidYMid slice" fill="none">
                            <rect x="15" y="10" width="250" height="92" stroke="white" stroke-width="2"/>
                            <line x1="140" y1="10" x2="140" y2="102" stroke="white" stroke-width="1.5"/>
                            <line x1="15" y1="56" x2="265" y2="56" stroke="white" stroke-width="1.5"/>
                            <rect x="55" y="10" width="170" height="92" stroke="white" stroke-width="1"/>
                            <ellipse cx="140" cy="56" rx="20" ry="20" stroke="white" stroke-width="1.5"/>
                        </svg>
                        <div x-show="canchaId === {{ $cancha->id }}"
                             class="absolute top-3 right-3 w-7 h-7 bg-green-400 rounded-full flex items-center justify-center shadow">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $cancha->nombre }}</p>
                            <p class="text-gray-400 text-xs mt-0.5">{{ $label }} · {{ $cancha->tipo }}</p>
                        </div>
                        @if($precioMin)
                            <div class="text-right">
                                <p class="font-black text-gray-900 text-sm">S/ {{ number_format($precioMin, 0) }}</p>
                                <p class="text-gray-400 text-xs">por hora</p>
                            </div>
                        @endif
                    </div>
                </button>
            @endforeach
        </div>

        {{-- Sin tarifa disponible para esa combinación --}}
        <p x-show="canchaId && !tarifaId"
           class="mt-3 text-xs text-orange-600 bg-orange-50 border border-orange-200 rounded-xl px-4 py-2">
            ⚠ No hay una tarifa activa para esta cancha en ese horario. Prueba con otro horario.
        </p>
    </div>

    {{-- PASO 4: RESUMEN + PROCEDER AL PAGO --}}
    <div x-show="listo"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Resumen de reserva</p>
            <p class="font-bold text-green-900 text-base" x-text="canchaNombre + ' — ' + canchaLabel"></p>
            <p class="text-sm text-gray-500 mb-4" x-text="diaSeleccionado + ' · ' + horaSeleccionada"></p>

            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-gray-400">Total (inc. IGV 18%)</span>
                <span class="text-2xl font-black text-green-900" x-text="'S/ ' + totalConIgv.toFixed(2)"></span>
            </div>

            <button type="button"
                    @click="modalPago = true"
                    class="w-full flex items-center justify-center gap-2 font-bold py-3.5 rounded-2xl text-white text-sm transition-all"
                    style="background: linear-gradient(90deg, #4ade80, #22c55e); box-shadow: 0 4px 14px rgba(34,197,94,.4);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M2 10h20" stroke="currentColor" stroke-width="2"/>
                </svg>
                Proceder al Pago
            </button>
            <p class="text-center text-xs text-gray-400 mt-2">Boleta electrónica válida ante SUNAT</p>
        </div>
    </div>

    {{-- ===== MODAL PASARELA DE PAGO ===== --}}
    <div x-show="modalPago"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,.5); backdrop-filter: blur(4px);">

        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden">

            {{-- Header --}}
            <div class="bg-green-900 px-6 py-5 flex items-center justify-between">
                <div>
                    <h2 class="text-white font-black text-xl">Pasarela de Pago</h2>
                    <p class="text-green-300 text-sm font-mono mt-0.5">Elige tu método de pago</p>
                </div>
                <button @click="modalPago = false"
                        class="w-9 h-9 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                </button>
            </div>

            <div class="flex flex-col sm:flex-row">

                {{-- Métodos de pago --}}
                <div class="flex-1 p-6 border-b sm:border-b-0 sm:border-r border-gray-100">
                    <p class="text-sm font-bold text-gray-800 mb-4">Métodos de Pago</p>

                    <form method="POST" action="{{ route('horarios.store') }}" x-ref="formPago">
                        @csrf
                        <input type="hidden" name="cancha_id"   :value="canchaId">
                        <input type="hidden" name="tarifa_id"   :value="tarifaId">
                        <input type="hidden" name="fecha"       :value="diaSeleccionado">
                        <input type="hidden" name="hora_inicio" :value="horaSeleccionada">
                        <input type="hidden" name="hora_fin"    :value="horaFin">
                        <input type="hidden" name="notas"       :value="notasReserva">
                        <input type="hidden" name="metodo_pago" :value="metodoPago">
                    </form>

                    {{-- Notas --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                            Notas <span class="font-normal text-gray-400">(opcional)</span>
                        </label>
                        <textarea x-model="notasReserva" rows="2"
                                  class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-400 resize-none"
                                  placeholder="Ej: clase particular, partido amistoso..."></textarea>
                    </div>

                    {{-- Yape --}}
                    <button type="button"
                            @click="metodoPago = 'Yape'; $refs.formPago.submit()"
                            class="w-full flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-green-300 hover:bg-green-50 transition-all mb-3 text-left cursor-pointer group">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0"
                             style="background: #6d28d9;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <rect x="5" y="2" width="14" height="20" rx="3" stroke="white" stroke-width="2"/>
                                <circle cx="12" cy="17" r="1.5" fill="white"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 text-sm">Yape</p>
                            <p class="text-gray-400 text-xs">Pago digital inmediato con QR o número</p>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-green-500 group-hover:translate-x-0.5 transition-transform">
                            <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    {{-- Efectivo --}}
                    <button type="button"
                            @click="metodoPago = 'Efectivo'; $refs.formPago.submit()"
                            class="w-full flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-green-300 hover:bg-green-50 transition-all text-left cursor-pointer group">
                        <div class="w-12 h-12 rounded-2xl bg-green-800 flex items-center justify-center shrink-0">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <rect x="2" y="7" width="20" height="14" rx="2" stroke="white" stroke-width="2"/>
                                <circle cx="12" cy="14" r="3" stroke="white" stroke-width="2"/>
                                <path d="M6 7V5a2 2 0 012-2h8a2 2 0 012 2v2" stroke="white" stroke-width="2"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 text-sm">Pago en Efectivo</p>
                            <p class="text-gray-400 text-xs">Pago por adelantado en recepción</p>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-green-500 group-hover:translate-x-0.5 transition-transform">
                            <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                {{-- Resumen de reserva --}}
                <div class="w-full sm:w-64 p-6 bg-gray-50">
                    <p class="text-sm font-bold text-gray-800 mb-4">Tu Reserva</p>

                    <div class="space-y-2 text-sm mb-5">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Cancha</span>
                            <span class="font-semibold text-gray-900" x-text="canchaNombre"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Fecha</span>
                            <span class="font-semibold text-gray-900" x-text="diaSeleccionado"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Hora</span>
                            <span class="font-semibold text-gray-900" x-text="horaSeleccionada"></span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-1.5 text-sm mb-4">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span x-text="'S/ ' + canchaPrecio.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>IGV 18%</span>
                            <span x-text="'S/ ' + igv.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between font-black text-green-900 text-base pt-1">
                            <span>Total</span>
                            <span x-text="'S/ ' + totalConIgv.toFixed(2)"></span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 p-3 bg-green-50 border border-green-200 rounded-xl text-xs text-green-700">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Boleta electrónica válida ante SUNAT
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function reservarWizard(todasTarifas) {
    return {
        diaSeleccionado:  '{{ $dias[0]['value'] }}',
        horaSeleccionada: '',
        canchaId:         null,
        canchaNombre:     '',
        canchaLabel:      '',
        canchaPrecio:     0,
        tarifaId:         null,
        horaFin:          '',
        modalPago:        false,
        metodoPago:       '',
        notasReserva:     '',

        get igv()         { return this.canchaPrecio * 0.18; },
        get totalConIgv() { return this.canchaPrecio * 1.18; },
        get listo()       { return this.diaSeleccionado && this.horaSeleccionada && this.canchaId && this.tarifaId; },

        setDia(value) {
            this.diaSeleccionado  = value;
            this.horaSeleccionada = '';
            this.canchaId  = null;
            this.tarifaId  = null;
            this.horaFin   = '';
        },

        setHora(hora) {
            this.horaSeleccionada = hora;
            this.canchaId  = null;
            this.tarifaId  = null;
            this.horaFin   = '';
        },

        setCancha(id, nombre, label, precio) {
            this.canchaId     = id;
            this.canchaNombre = nombre;
            this.canchaLabel  = label;
            this.canchaPrecio = precio;

            const [h, m] = this.horaSeleccionada.split(':').map(Number);
            const fin = new Date(2000, 0, 1, h + 1, m);
            this.horaFin = fin.getHours().toString().padStart(2,'0') + ':' + fin.getMinutes().toString().padStart(2,'0');

            const tarifasCancha = todasTarifas.filter(t => t.cancha_id == id);
            const match = tarifasCancha.find(t =>
                this.horaSeleccionada >= t.hora_inicio && this.horaSeleccionada < t.hora_fin
            ) || tarifasCancha[0] || null;

            this.tarifaId     = match ? match.id : null;
            this.canchaPrecio = match ? parseFloat(match.precio_hora) : precio;
        },
    };
}
</script>
