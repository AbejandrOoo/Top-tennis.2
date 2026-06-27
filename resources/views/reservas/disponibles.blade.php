<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Reservar una cancha</h1>
            <p class="text-green-200 text-sm mt-0.5">Elige fecha y horario disponible</p>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
         x-data="{
            fechaFiltro: '',
            horariosFiltrados(fecha) {
                return fecha === '';
            }
         }">

        @include('partials.errores')

        {{-- Selector de fecha --}}
        <div class="card p-5 mb-8 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Filtrar por fecha</p>
                    <p class="text-xs text-gray-400">Deja vacío para ver todos los horarios disponibles</p>
                </div>
            </div>
            <div class="flex items-center gap-3 sm:ml-auto">
                <input type="date" x-model="fechaFiltro"
                       min="{{ today()->format('Y-m-d') }}"
                       class="form-input py-2 text-sm" style="width:auto;">
                <button type="button" @click="fechaFiltro = ''"
                        x-show="fechaFiltro !== ''"
                        class="text-xs text-gray-400 hover:text-red-500 font-medium underline whitespace-nowrap">
                    Limpiar
                </button>
            </div>
        </div>

        @if($horarios->isEmpty() && $canchasEnMantenimiento->isEmpty())
            <div class="card p-16 text-center">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                        <path d="M9 12l2 2 4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <p class="text-gray-600 font-bold text-lg">No hay horarios disponibles por ahora.</p>
                <p class="text-gray-400 text-sm mt-1">Vuelve más tarde o consulta con recepción.</p>
            </div>
        @else

            {{-- Grid de horarios disponibles --}}
            @if($horarios->isNotEmpty())
                <h2 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    Horarios disponibles ({{ $horarios->count() }})
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                    @foreach($horarios as $horario)
                        @php
                            $cancha   = $horario->cancha;
                            $tarifa   = $horario->tarifa;
                            $precio   = $tarifa?->precio ?? 0;
                            $sup      = $cancha?->tipo_superficie ?? '—';
                            $modalidad = $cancha?->modalidad ?? 'Ambos';
                            $ilum     = $cancha?->iluminacion;
                            $fechaSlot = optional($horario->hora_inicio)->format('Y-m-d');

                            $iconoSup = match($sup) {
                                'Arcilla'   => '#c2410c',
                                'Sintética' => '#16a34a',
                                'Hierba'    => '#15803d',
                                'Dura'      => '#2563eb',
                                default     => '#6b7280',
                            };
                        @endphp

                        <div x-show="fechaFiltro === '' || fechaFiltro === '{{ $fechaSlot }}'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="rounded-2xl overflow-hidden shadow-md bg-white flex flex-col hover:shadow-xl transition-shadow duration-300">

                            {{-- Imagen --}}
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $cancha?->imagenUrl() ?? asset('images/Arcilla.jpeg') }}"
                                     alt="{{ $cancha?->nombre ?? 'Cancha' }}"
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

                                {{-- Badge precio --}}
                                <div class="absolute top-3 right-3">
                                    <span class="bg-green-500 text-white text-sm font-extrabold px-3 py-1.5 rounded-full shadow-md">
                                        S/ {{ number_format($precio, 2) }}
                                    </span>
                                </div>

                                {{-- Superficie badge --}}
                                <div class="absolute bottom-3 left-3">
                                    <span class="text-xs font-bold px-2 py-1 rounded-lg text-white backdrop-blur-sm"
                                          style="background:{{ $iconoSup }}cc;">
                                        {{ $sup }}
                                    </span>
                                </div>
                            </div>

                            {{-- Contenido --}}
                            <div class="flex flex-col flex-1 px-5 pt-4 pb-5 gap-3">
                                <h3 class="text-lg font-extrabold text-gray-900 leading-tight">
                                    {{ $cancha?->nombre ?? '—' }}
                                </h3>

                                {{-- Características físicas de la cancha --}}
                                <div class="flex flex-wrap gap-2">
                                    {{-- Modalidad --}}
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg bg-purple-50 text-purple-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                        </svg>
                                        {{ $modalidad }}
                                    </span>

                                    {{-- Iluminación --}}
                                    @if($ilum)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg bg-yellow-50 text-yellow-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3M6.343 6.343l-.707-.707M12 17v1m0 0a4 4 0 01-4-4H8m8 0a4 4 0 01-4 4"/>
                                            </svg>
                                            Con iluminación
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg bg-gray-100 text-gray-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                            </svg>
                                            Sin iluminación
                                        </span>
                                    @endif
                                </div>

                                {{-- Fecha y hora del slot --}}
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                                            <path d="M16 2v4M8 2v4M3 10h18" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span class="font-medium text-gray-700">
                                            {{ optional($horario->hora_inicio)->format('d/m/Y') ?? '—' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                            <path d="M12 7v5l3 3" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span class="font-medium text-gray-700">
                                            {{ optional($horario->hora_inicio)->format('H:i') ?? '—' }}
                                            – {{ optional($horario->hora_fin)->format('H:i') ?? '—' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-1"></div>

                                <a href="{{ route('reservas.confirmar', $horario) }}"
                                   class="block w-full text-center border-2 border-green-600 text-green-700 font-extrabold text-sm py-3 rounded-xl hover:bg-green-600 hover:text-white transition-all duration-200 tracking-wide">
                                    RESERVAR AHORA &nbsp;→
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Mensaje cuando el filtro no tiene resultados --}}
                <div x-show="fechaFiltro !== '' && {{ $horarios->count() }} > 0"
                     class="hidden"
                     x-cloak>
                    @foreach($horarios as $horario)
                        @php $fechaSlot = optional($horario->hora_inicio)->format('Y-m-d'); @endphp
                    @endforeach
                </div>
                <p x-show="fechaFiltro !== ''"
                   x-cloak
                   class="text-center text-sm text-gray-400 mt-2 mb-6">
                    Mostrando horarios para <span class="font-semibold text-gray-700" x-text="fechaFiltro"></span>.
                    <button @click="fechaFiltro = ''" class="text-green-600 underline font-medium">Ver todos</button>
                </p>
            @endif

            {{-- Canchas en mantenimiento --}}
            @if($canchasEnMantenimiento->isNotEmpty())
                <h2 class="text-base font-bold text-gray-500 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                    Temporalmente no disponibles ({{ $canchasEnMantenimiento->count() }})
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($canchasEnMantenimiento as $cancha)
                        <div class="rounded-2xl overflow-hidden border border-gray-200 bg-white opacity-70">
                            {{-- Imagen con overlay de mantenimiento --}}
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $cancha->imagenUrl() }}"
                                     alt="{{ $cancha->nombre }}"
                                     class="w-full h-full object-cover grayscale">
                                <div class="absolute inset-0 bg-amber-900/40 flex items-center justify-center">
                                    <div class="text-center">
                                        <svg class="w-10 h-10 text-amber-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                        </svg>
                                        <span class="text-white font-bold text-sm drop-shadow">En mantenimiento</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-5 pt-4 pb-5">
                                <h3 class="text-base font-bold text-gray-600 mb-2">{{ $cancha->nombre }}</h3>

                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg bg-gray-100 text-gray-500">
                                        {{ $cancha->tipo_superficie }}
                                    </span>
                                    @if($cancha->modalidad)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg bg-gray-100 text-gray-500">
                                            {{ $cancha->modalidad }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg bg-gray-100 text-gray-500">
                                        {{ $cancha->iluminacion ? 'Con luz' : 'Sin luz' }}
                                    </span>
                                </div>

                                @if($cancha->motivo_mantenimiento)
                                    <p class="text-xs text-amber-700 font-medium mb-1">
                                        <span class="font-bold">Motivo:</span> {{ $cancha->motivo_mantenimiento }}
                                    </p>
                                @endif

                                @if($cancha->fin_mantenimiento)
                                    <p class="text-xs text-gray-500">
                                        En mantenimiento hasta el
                                        <span class="font-semibold text-amber-600">
                                            {{ $cancha->fin_mantenimiento->format('d/m/Y') }} a las {{ $cancha->fin_mantenimiento->format('H:i') }}
                                        </span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        @endif
    </div>
</x-app-layout>
