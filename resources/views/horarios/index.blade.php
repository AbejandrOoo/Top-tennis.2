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

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

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
                            <th class="text-left px-6 py-3">Hora</th>
                            <th class="text-left px-6 py-3">Cancha</th>
                            <th class="text-left px-6 py-3">Tarifa</th>
                            <th class="text-left px-6 py-3">Precio</th>
                            <th class="text-left px-6 py-3">Estado</th>
                            <th class="text-right px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($horarios as $horario)
                            @php
                                $esReservado = $horario->estado === 'reservado';
                                $esPasado = $horario->hora_inicio->lt(now());
                            @endphp
                            <tr class="{{ $esPasado ? 'opacity-40' : '' }} {{ $esReservado ? 'bg-blue-50/50' : '' }}">
                                <td class="px-6 py-3">
                                    <span class="font-bold text-slate-800">
                                        {{ $horario->hora_inicio->format('H:i') }}
                                    </span>
                                    <span class="text-slate-300 mx-1">–</span>
                                    <span class="text-slate-500">{{ $horario->hora_fin->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded overflow-hidden shrink-0 border border-slate-100">
                                            <img src="{{ $horario->cancha?->imagenUrl() }}" class="w-full h-full object-cover" alt="">
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $horario->cancha?->nombre ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-slate-500">
                                    {{ $horario->tarifa?->nombre_tarifa ?? '—' }}
                                </td>
                                <td class="px-6 py-3">
                                    <span class="font-bold text-emerald-700">S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    @if($esPasado)
                                        <span class="badge badge-gray text-xs">Pasado</span>
                                    @elseif($esReservado)
                                        <span class="badge badge-blue text-xs">Reservado</span>
                                    @else
                                        <span class="badge badge-green text-xs">Disponible</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(!$esReservado && !$esPasado)
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
    </div>
</x-app-layout>
