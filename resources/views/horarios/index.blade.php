<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-300 text-xs font-semibold uppercase tracking-widest mb-0.5">Administración</p>
                <h1 class="text-2xl font-black text-white">Horarios</h1>
            </div>
            <a href="{{ route('horarios.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Generar horarios
            </a>
        </div>
    </x-slot>

    @php
        $idsDisponibles = $horarios
            ->filter(fn($h) => $h->estado === 'disponible' && !$h->hora_inicio->lt(now()))
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->values();
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="{
            seleccionados: [],
            disponibles: {{ Js::from($idsDisponibles) }},
            get todosSeleccionados() {
                return this.disponibles.length > 0 && this.disponibles.every(id => this.seleccionados.includes(id));
            },
            toggleTodos() {
                this.seleccionados = this.todosSeleccionados ? [] : [...this.disponibles];
            }
         }">

        @include('partials.errores')

        {{-- Filtro por día --}}
        <div class="card mb-5">
            <div class="px-5 py-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Filtrar por día</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($fechas as $f)
                        @php $esActivo = $f->toDateString() === $fechaStr; @endphp
                        <a href="{{ route('horarios.index', ['fecha' => $f->toDateString(), 'cancha' => $canchaId]) }}"
                           class="flex flex-col items-center px-4 py-2 rounded-xl border-2 text-sm font-bold transition-all
                                  {{ $esActivo
                                      ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm'
                                      : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-400' }}">
                            <span class="text-[10px] uppercase tracking-wide {{ $esActivo ? 'text-emerald-200' : 'text-slate-400' }}">
                                {{ $f->isToday() ? 'Hoy' : $f->locale('es')->isoFormat('ddd') }}
                            </span>
                            <span class="text-lg leading-tight">{{ $f->format('d') }}</span>
                            <span class="text-[10px] {{ $esActivo ? 'text-emerald-200' : 'text-slate-400' }}">{{ $f->locale('es')->isoFormat('MMM') }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Filtro por cancha + Stats + Eliminar día --}}
        <div class="flex flex-wrap items-center gap-3 mb-5">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('horarios.index', ['fecha' => $fechaStr]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all
                          {{ !$canchaId ? 'bg-emerald-700 text-white' : 'bg-white text-slate-500 border border-slate-200 hover:border-emerald-400' }}">
                    Todas
                </a>
                @foreach($canchas as $c)
                    <a href="{{ route('horarios.index', ['fecha' => $fechaStr, 'cancha' => $c->id]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all
                              {{ (string)$canchaId === (string)$c->id ? 'bg-emerald-700 text-white' : 'bg-white text-slate-500 border border-slate-200 hover:border-emerald-400' }}">
                        {{ $c->nombre }}
                    </a>
                @endforeach
            </div>

            <div class="ml-auto flex items-center gap-3">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-green-50 text-green-700">{{ $stats['disponible'] }} disponibles</span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700">{{ $stats['reservado'] }} reservados</span>

                @if($stats['disponible'] > 0)
                    <form method="POST" action="{{ route('horarios.eliminarDia') }}"
                          onsubmit="return confirm('¿Eliminar TODOS los horarios disponibles del {{ $fecha->locale('es')->isoFormat('D [de] MMMM') }}{{ $canchaId ? ' para esta cancha' : '' }}? Los reservados no se tocarán.')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="fecha" value="{{ $fechaStr }}">
                        @if($canchaId)<input type="hidden" name="cancha_id" value="{{ $canchaId }}">@endif
                        <button type="submit" class="btn-danger text-xs py-1.5 px-3">
                            Eliminar día
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Tabla de horarios --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <p class="text-sm font-bold text-slate-700">
                    {{ $fecha->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
                <p class="text-xs text-slate-400">{{ $stats['total'] }} horarios</p>
            </div>

            @if($horarios->isEmpty())
                <div class="px-6 py-14 text-center">
                    <div class="w-12 h-12 rounded-xl mx-auto mb-3 flex items-center justify-center bg-slate-50">
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke-width="2"/>
                            <path d="M12 7v5l3 3" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <p class="text-slate-400 font-medium">No hay horarios para este día.</p>
                    <a href="{{ route('horarios.create') }}" class="mt-2 inline-flex text-sm text-emerald-600 font-semibold hover:underline">
                        Generar horarios →
                    </a>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="table-header">
                        <tr>
                            <th class="text-center px-3 py-3 w-10">
                                <input type="checkbox"
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                       :checked="todosSeleccionados"
                                       @click="toggleTodos()"
                                       x-show="disponibles.length > 0">
                            </th>
                            <th class="text-left px-4 py-3">Hora</th>
                            <th class="text-left px-4 py-3">Cancha</th>
                            <th class="text-left px-4 py-3">Tarifa</th>
                            <th class="text-left px-4 py-3">Precio</th>
                            <th class="text-left px-4 py-3">Estado</th>
                            <th class="text-right px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($horarios as $horario)
                            @php
                                $esReservado = $horario->estado === 'reservado';
                                $esPasado = $horario->hora_inicio->lt(now());
                                $esSeleccionable = !$esReservado && !$esPasado;
                            @endphp
                            <tr class="{{ $esPasado ? 'opacity-40' : '' }} {{ $esReservado ? 'bg-blue-50/50' : '' }}"
                                :class="seleccionados.includes('{{ $horario->id }}') ? 'bg-emerald-50/60' : ''">
                                <td class="text-center px-3 py-3">
                                    @if($esSeleccionable)
                                        <input type="checkbox"
                                               value="{{ $horario->id }}"
                                               x-model="seleccionados"
                                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-slate-800">
                                        {{ $horario->hora_inicio->format('H:i') }}
                                    </span>
                                    <span class="text-slate-300 mx-1">–</span>
                                    <span class="text-slate-500">{{ $horario->hora_fin->format('H:i') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded overflow-hidden shrink-0 border border-slate-100">
                                            <img src="{{ $horario->cancha?->imagenUrl() }}" class="w-full h-full object-cover" alt="">
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $horario->cancha?->nombre ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $horario->tarifa?->nombre_tarifa ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-emerald-700">S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($esPasado)
                                        <span class="badge badge-gray text-xs">Pasado</span>
                                    @elseif($esReservado)
                                        <span class="badge badge-blue text-xs">Reservado</span>
                                    @else
                                        <span class="badge badge-green text-xs">Disponible</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($esSeleccionable)
                                            <a href="{{ route('horarios.edit', $horario) }}"
                                               class="btn-outline-sm text-xs py-1 px-2.5">Editar</a>
                                            <form method="POST" action="{{ route('horarios.destroy', $horario) }}"
                                                  onsubmit="return confirm('¿Eliminar este horario?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-danger text-xs py-1 px-2.5">Eliminar</button>
                                            </form>
                                        @elseif($esReservado)
                                            <span class="text-xs text-blue-400 font-medium">Reservado</span>
                                        @else
                                            <span class="text-xs text-slate-300">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Barra flotante de acciones masivas --}}
        <div x-show="seleccionados.length > 0" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50" x-cloak>
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 px-6 py-4 flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <span class="text-sm font-black text-emerald-700" x-text="seleccionados.length"></span>
                    </div>
                    <span class="text-sm font-semibold text-slate-600">seleccionados</span>
                </div>

                <div class="w-px h-8 bg-slate-200"></div>

                <form method="POST" action="{{ route('horarios.cambiarTarifa') }}" class="flex items-center gap-2">
                    @csrf
                    <template x-for="id in seleccionados" :key="id">
                        <input type="hidden" name="horario_ids[]" :value="id">
                    </template>
                    <select name="tarifa_id" class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="">Cambiar tarifa a...</option>
                        @foreach($tarifas as $tarifa)
                            <option value="{{ $tarifa->id }}">{{ $tarifa->nombre_tarifa }} — S/ {{ number_format($tarifa->precio, 2) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary text-sm py-2 px-4">Aplicar</button>
                </form>

                <div class="w-px h-8 bg-slate-200"></div>

                <button @click="seleccionados = []" class="text-xs text-slate-400 hover:text-slate-600 font-medium">
                    Deseleccionar
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
