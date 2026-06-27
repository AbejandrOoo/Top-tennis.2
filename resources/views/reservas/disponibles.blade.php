<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Reservar una cancha</h1>
            <p class="text-emerald-300 text-sm mt-0.5 font-medium">Elige el día, la hora y la cancha</p>
        </div>
    </x-slot>

    @php
        $diasAbrev = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        $meses     = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $hoyStr    = today()->format('Y-m-d');
        $horaActual = (int) now()->format('H');

        // Fechas únicas que tienen al menos 1 horario disponible
        $fechasUnicas = $horarios
            ->map(fn($h) => optional($h->hora_inicio)->format('Y-m-d'))
            ->filter()->unique()->sort()->values();

        // Horas que realmente tienen horarios disponibles por fecha
        $horasDisponiblesPorFecha = $horarios
            ->groupBy(fn($h) => optional($h->hora_inicio)->format('Y-m-d'))
            ->map(fn($grupo) => $grupo
                ->map(fn($h) => optional($h->hora_inicio)->format('H:i'))
                ->unique()->sort()->values()->toArray()
            )->toArray();

        // TODAS las horas de operación (6:00–21:00 = 16 slots)
        $todasLasHoras = collect(range(6, 21))->map(fn($h) => sprintf('%02d:00', $h))->values()->toArray();

        // Mapa: "fecha|hora" → array de canchas con su horario_id
        $canchasPorSlot = [];
        foreach ($horarios as $h) {
            $key = optional($h->hora_inicio)->format('Y-m-d') . '|' . optional($h->hora_inicio)->format('H:i');
            $cancha = $h->cancha;
            if (! $cancha) continue;
            $canchasPorSlot[$key][] = [
                'horario_id'  => $h->id,
                'cancha_id'   => $cancha->id,
                'nombre'      => $cancha->nombre,
                'superficie'  => $cancha->tipo_superficie,
                'modalidad'   => $cancha->modalidad ?? 'Ambos',
                'iluminacion' => (bool) $cancha->iluminacion,
                'imagen'      => $cancha->imagenUrl(),
                'precio'      => $h->tarifa?->precio ?? 0,
                'hora_fin'    => optional($h->hora_fin)->format('H:i'),
                'confirmar_url' => route('reservas.confirmar', $h->id),
            ];
        }
    @endphp

    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
             x-data="{
                fechaFiltro: '{{ $fechasUnicas->contains($hoyStr) ? $hoyStr : ($fechasUnicas->first() ?? '') }}',
                horaFiltro: '',
                hoyStr: '{{ $hoyStr }}',
                horaActual: {{ $horaActual }},

                todasLasHoras: {{ json_encode($todasLasHoras) }},
                horasDisponiblesPorFecha: {{ json_encode($horasDisponiblesPorFecha) }},
                canchasPorSlot: {{ json_encode($canchasPorSlot) }},

                esHoraDisponible(hora) {
                    const disponibles = this.horasDisponiblesPorFecha[this.fechaFiltro] || [];
                    return disponibles.includes(hora);
                },
                esHoraPasada(hora) {
                    if (this.fechaFiltro !== this.hoyStr) return false;
                    const h = parseInt(hora.split(':')[0]);
                    return h <= this.horaActual;
                },
                get canchasVisibles() {
                    if (!this.fechaFiltro || !this.horaFiltro) return [];
                    const key = this.fechaFiltro + '|' + this.horaFiltro;
                    return this.canchasPorSlot[key] || [];
                },
                get horaFinLabel() {
                    const c = this.canchasVisibles;
                    return c.length ? c[0].hora_fin : '';
                },
                setFecha(f) {
                    this.fechaFiltro = f;
                    this.horaFiltro  = '';
                },
                fechaLegible(f) {
                    const d = new Date(f + 'T00:00:00');
                    return d.toLocaleDateString('es-PE', {weekday:'long', day:'numeric', month:'long'});
                },
                supColor(s) {
                    const m = {'Arcilla':'bg-orange-500','Sintética':'bg-emerald-600','Hierba':'bg-green-700','Dura':'bg-blue-600'};
                    return m[s] || 'bg-slate-500';
                }
             }">

            @include('partials.errores')

            @if($horarios->isEmpty() && $canchasEnMantenimiento->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-white shadow-sm flex items-center justify-center mb-5 border border-slate-100">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
                            <path d="M3 9Q12 5 21 9M3 15Q12 19 21 15" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-slate-800 mb-2">Sin horarios disponibles</h2>
                    <p class="text-slate-400 text-sm max-w-xs">No hay canchas disponibles en este momento. Vuelve más tarde o consulta con recepción.</p>
                </div>
            @else

                {{-- ═══ PASO 1 — ELIGE EL DÍA ═══ --}}
                @if($fechasUnicas->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-4">
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-[.12em] mb-3 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black shrink-0">1</span>
                            Elige el día
                        </p>

                        <div class="flex gap-2.5 overflow-x-auto pb-1 -mx-1 px-1"
                             style="scrollbar-width:none; -ms-overflow-style:none;">
                            @foreach($fechasUnicas as $fecha)
                                @php
                                    $c     = \Carbon\Carbon::parse($fecha);
                                    $dia   = $diasAbrev[$c->dayOfWeek];
                                    $mes   = $meses[$c->month - 1];
                                    $esHoy = $c->isToday();
                                @endphp
                                <button type="button" @click="setFecha('{{ $fecha }}')"
                                        :class="fechaFiltro === '{{ $fecha }}'
                                            ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm'
                                            : 'bg-slate-50 text-slate-700 border-slate-200 hover:border-slate-300 hover:text-slate-900'"
                                        class="shrink-0 flex flex-col items-center px-5 py-2.5 rounded-xl border-2 transition-all duration-150 cursor-pointer min-w-[72px] relative">
                                    @if($esHoy)
                                        <span class="absolute -top-2 left-1/2 -translate-x-1/2 bg-emerald-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full leading-none">HOY</span>
                                    @endif
                                    <span class="text-[11px] font-bold opacity-60 leading-none mb-0.5 uppercase tracking-wide">{{ $dia }}</span>
                                    <span class="text-base font-black leading-none">{{ $c->format('d') }}</span>
                                    <span class="text-[11px] font-semibold opacity-60 leading-none mt-0.5">{{ $mes }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- ═══ PASO 2 — ELIGE LA HORA ═══ --}}
                    <div x-show="fechaFiltro !== ''"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-4">

                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-[.12em] mb-3 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black shrink-0">2</span>
                            Elige la hora
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <template x-for="hora in todasLasHoras" :key="hora">
                                <button type="button"
                                        @click="if (esHoraDisponible(hora) && !esHoraPasada(hora)) horaFiltro = hora"
                                        :disabled="!esHoraDisponible(hora) || esHoraPasada(hora)"
                                        :class="{
                                            'bg-emerald-700 text-white border-emerald-700 shadow-sm': horaFiltro === hora,
                                            'bg-slate-50 text-slate-700 border-slate-200 hover:border-emerald-400 hover:text-emerald-700 cursor-pointer': horaFiltro !== hora && esHoraDisponible(hora) && !esHoraPasada(hora),
                                            'bg-slate-100 text-slate-300 border-slate-100 cursor-not-allowed line-through': esHoraPasada(hora),
                                            'bg-red-50 text-red-300 border-red-100 cursor-not-allowed': !esHoraDisponible(hora) && !esHoraPasada(hora),
                                        }"
                                        class="flex items-center gap-1.5 px-4 py-2 rounded-xl border-2 text-sm font-bold transition-all duration-150">
                                    <svg class="w-3.5 h-3.5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                        <path d="M12 7v5l3 3" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <span x-text="hora"></span>
                                </button>
                            </template>
                        </div>
                        <div class="flex items-center gap-4 mt-3 text-[10px] font-semibold text-slate-400">
                            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-slate-100 border border-slate-200 inline-block"></span> Pasada</span>
                            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-red-50 border border-red-100 inline-block"></span> Reservada</span>
                            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-slate-50 border border-slate-200 inline-block"></span> Disponible</span>
                        </div>
                    </div>

                    {{-- ═══ PASO 3 — CANCHAS DISPONIBLES ═══ --}}
                    <div x-show="horaFiltro !== '' && canchasVisibles.length > 0"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak>

                        {{-- Resumen --}}
                        <div class="flex items-center gap-2 flex-wrap mb-4">
                            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-[.12em] flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black shrink-0">3</span>
                                Elige tu cancha
                            </p>
                            <span class="text-slate-300">—</span>
                            <span class="bg-emerald-50 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full border border-emerald-100"
                                  x-text="fechaLegible(fechaFiltro)"></span>
                            <span class="bg-slate-100 text-slate-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-slate-200"
                                  x-text="horaFiltro + ' – ' + horaFinLabel"></span>
                        </div>

                        {{-- Grid de canchas --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            <template x-for="cancha in canchasVisibles" :key="cancha.cancha_id">
                                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col border border-slate-100">

                                    {{-- Imagen --}}
                                    <div class="relative aspect-[16/10] overflow-hidden">
                                        <img :src="cancha.imagen"
                                             :alt="cancha.nombre"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

                                        {{-- Precio --}}
                                        <div class="absolute top-3 right-3">
                                            <span class="inline-flex items-center gap-1 bg-black/60 backdrop-blur-md text-white text-sm font-black px-3 py-1.5 rounded-full border border-white/20 shadow-lg">
                                                S/ <span x-text="parseFloat(cancha.precio).toFixed(2)"></span>
                                                <span class="text-white/60 font-medium text-xs">/hr</span>
                                            </span>
                                        </div>

                                        {{-- Superficie --}}
                                        <div class="absolute bottom-3 left-3">
                                            <span class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full text-white backdrop-blur-sm border border-white/20"
                                                  :class="supColor(cancha.superficie)"
                                                  x-text="cancha.superficie"></span>
                                        </div>
                                    </div>

                                    {{-- Contenido --}}
                                    <div class="flex flex-col flex-1 p-5 gap-3">
                                        <h3 class="text-lg font-black text-slate-900 leading-tight" x-text="cancha.nombre"></h3>

                                        <div class="flex flex-wrap gap-2">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full bg-violet-50 text-violet-700 border border-violet-100">
                                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                                </svg>
                                                <span x-text="cancha.modalidad"></span>
                                            </span>

                                            <template x-if="cancha.iluminacion">
                                                <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3M6.343 6.343l-.707-.707M12 21v-1m0-4a4 4 0 100-8 4 4 0 000 8z"/>
                                                    </svg>
                                                    Con iluminación
                                                </span>
                                            </template>
                                            <template x-if="!cancha.iluminacion">
                                                <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                                    </svg>
                                                    Sin iluminación
                                                </span>
                                            </template>
                                        </div>

                                        <div class="flex-1"></div>
                                        <div class="border-t border-slate-100"></div>

                                        <a :href="cancha.confirmar_url"
                                           class="group/btn flex items-center justify-center gap-2 w-full bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-sm py-3 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md hover:shadow-emerald-200 active:scale-[.97]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Reservar
                                            <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Mensaje cuando selecciona hora pero no hay canchas --}}
                    <div x-show="horaFiltro !== '' && canchasVisibles.length === 0" x-cloak
                         class="text-center py-12">
                        <p class="text-slate-400 font-semibold text-sm">No hay canchas disponibles en ese horario.</p>
                    </div>

                    {{-- Indicador cuando aún falta elegir hora --}}
                    <div x-show="fechaFiltro !== '' && horaFiltro === ''" x-cloak
                         class="text-center py-12">
                        <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center mx-auto mb-3 border border-slate-100">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
                                <path d="M12 7v5l3 3" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p class="text-slate-500 font-bold text-sm">Selecciona una hora para ver las canchas disponibles</p>
                    </div>
                @endif

                {{-- ═══ CANCHAS EN MANTENIMIENTO ═══ --}}
                @if($canchasEnMantenimiento->isNotEmpty())
                    <div class="mt-8">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="h-px flex-1 bg-slate-200"></div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest shrink-0">
                                Temporalmente no disponibles
                            </span>
                            <div class="h-px flex-1 bg-slate-200"></div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            @foreach($canchasEnMantenimiento as $cancha)
                                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 opacity-75 flex flex-col">
                                    <div class="relative aspect-[16/10] overflow-hidden">
                                        <img src="{{ $cancha->imagenUrl() }}"
                                             alt="{{ $cancha->nombre }}"
                                             class="w-full h-full object-cover grayscale">
                                        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px] flex flex-col items-center justify-center gap-2">
                                            <div class="w-12 h-12 rounded-full bg-white/10 border border-white/20 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                                </svg>
                                            </div>
                                            <span class="text-white font-black text-sm tracking-wide drop-shadow">En Mantenimiento</span>
                                        </div>
                                    </div>

                                    <div class="p-5 flex flex-col gap-3">
                                        <h3 class="text-base font-black text-slate-400">{{ $cancha->nombre }}</h3>
                                        <div class="flex flex-wrap gap-1.5">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 border border-slate-200">{{ $cancha->tipo_superficie }}</span>
                                            @if($cancha->modalidad)
                                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 border border-slate-200">{{ $cancha->modalidad }}</span>
                                            @endif
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 border border-slate-200">{{ $cancha->iluminacion ? 'Con luz' : 'Sin luz' }}</span>
                                        </div>

                                        @if($cancha->motivo_mantenimiento || $cancha->fin_mantenimiento)
                                            <div class="bg-rose-50 border border-rose-100 rounded-xl p-3.5 flex flex-col gap-2">
                                                @if($cancha->motivo_mantenimiento)
                                                    <div class="flex items-start gap-2">
                                                        <svg class="w-3.5 h-3.5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <p class="text-xs text-rose-700 leading-relaxed">
                                                            <span class="font-bold">Motivo:</span> {{ $cancha->motivo_mantenimiento }}
                                                        </p>
                                                    </div>
                                                @endif
                                                @if($cancha->inicio_mantenimiento)
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <p class="text-xs text-rose-600">
                                                            Desde el
                                                            <span class="font-extrabold text-rose-800">{{ $cancha->inicio_mantenimiento->format('d/m/Y H:i') }}</span>
                                                        </p>
                                                    </div>
                                                @endif
                                                @if($cancha->fin_mantenimiento)
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                                            <path d="M12 7v5l3 3" stroke-width="2" stroke-linecap="round"/>
                                                        </svg>
                                                        <p class="text-xs text-rose-600">
                                                            Reabre el
                                                            <span class="font-extrabold text-rose-800">{{ $cancha->fin_mantenimiento->format('d/m/Y') }}</span>
                                                            a las
                                                            <span class="font-extrabold text-rose-800">{{ $cancha->fin_mantenimiento->format('H:i') }}</span>
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            @endif
        </div>
    </div>
</x-app-layout>
