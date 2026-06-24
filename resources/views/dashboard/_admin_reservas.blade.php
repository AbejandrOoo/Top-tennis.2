@php
    $total       = $todasReservas->total();
    $confirmadas = \App\Models\Horario::where('estado','Confirmado')->count();
    $canceladas  = \App\Models\Horario::where('estado','Cancelado')->count();
    $reembolso   = \App\Models\Horario::where('estado','Cancelado')->whereNotNull('notas')->count(); // placeholder

    $badgeColor = fn($estado) => match($estado) {
        'Confirmado' => 'bg-green-100 text-green-700',
        'Reservado'  => 'bg-yellow-100 text-yellow-700',
        'Cancelado'  => 'bg-red-100 text-red-600',
        'Completado' => 'bg-gray-100 text-gray-500',
        default      => 'bg-gray-100 text-gray-400',
    };
@endphp

<div x-data="{
    busqueda: '',
    filtroEstado: 'todas',
    filtroCancha: 'todas',
    ordenar: 'reciente',
    get reservas() {
        return this.$refs.filas ? [...this.$refs.filas.querySelectorAll('tr[data-nombre]')] : [];
    }
}">

    {{-- Título --}}
    <div class="mb-5">
        <h2 class="text-2xl font-black text-green-900">Gestión de Reservas</h2>
        <p class="text-sm text-gray-400 mt-0.5">
            Visualiza, <span class="text-green-600 font-semibold">administra</span> y cancela reservas del sistema
        </p>
    </div>

    {{-- Mini-stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-2xl font-black text-green-900">{{ $total }}</p>
            <p class="text-xs text-gray-400 mt-1">Total</p>
        </div>
        <div class="rounded-2xl border border-green-100 shadow-sm p-4" style="background:#f0fdf4;">
            <p class="text-2xl font-black text-green-700">{{ $confirmadas }}</p>
            <p class="text-xs text-gray-400 mt-1">Confirmadas</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <p class="text-2xl font-black text-gray-500">{{ $canceladas }}</p>
            <p class="text-xs text-gray-400 mt-1">Canceladas</p>
        </div>
        <div class="rounded-2xl border border-orange-100 shadow-sm p-4" style="background:#fff7ed;">
            <p class="text-2xl font-black text-orange-500">{{ $reembolso }}</p>
            <p class="text-xs text-gray-400 mt-1">Reembolso</p>
        </div>
    </div>

    {{-- Barra de búsqueda y filtros --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Buscador --}}
            <div class="flex items-center gap-2 flex-1 min-w-[200px] bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="text-gray-400 shrink-0">
                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                    <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <input type="text" x-model="busqueda" placeholder="Buscar por jugador..."
                       class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none w-full">
            </div>

            {{-- Pills estado --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                @foreach(['todas'=>'Todas','Confirmado'=>'Confirmadas','Reservado'=>'Reembolso','Cancelado'=>'Canceladas'] as $val => $lbl)
                    <button type="button"
                            @click="filtroEstado = '{{ $val }}'"
                            :class="filtroEstado === '{{ $val }}'
                                ? 'bg-green-500 text-white shadow-sm'
                                : 'bg-white border border-gray-200 text-gray-600 hover:border-green-300'"
                            class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all">
                        {{ $lbl }}
                    </button>
                @endforeach
            </div>

            {{-- Filtro cancha --}}
            <div class="relative">
                <select x-model="filtroCancha"
                        class="appearance-none pl-7 pr-7 py-1.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 bg-white focus:outline-none focus:ring-2 focus:ring-green-400 cursor-pointer">
                    <option value="todas">Todas las canchas</option>
                    @foreach($canchas as $cancha)
                        <option value="{{ $cancha->id }}">{{ $cancha->nombre }}</option>
                    @endforeach
                </select>
                <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" viewBox="0 0 24 24" fill="none">
                    <path d="M3 6h18M7 12h10M11 18h2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-400 pointer-events-none" viewBox="0 0 24 24" fill="none">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            {{-- Ordenar --}}
            <div class="relative">
                <select x-model="ordenar"
                        class="appearance-none pl-3 pr-7 py-1.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 bg-white focus:outline-none focus:ring-2 focus:ring-green-400 cursor-pointer">
                    <option value="reciente">Más reciente primero</option>
                    <option value="antiguo">Más antiguo primero</option>
                </select>
                <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-400 pointer-events-none" viewBox="0 0 24 24" fill="none">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Tabla / lista --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($todasReservas->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="text-gray-200 mb-4">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="text-gray-300 text-sm">No hay reservas registradas en el sistema.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase tracking-wide">
                            <th class="px-5 py-3.5 text-left font-semibold">Jugador</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Cancha</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Fecha</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Horario</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Precio</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Estado</th>
                            <th class="px-5 py-3.5 text-left font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody x-ref="filas" class="divide-y divide-gray-50">
                        @foreach($todasReservas as $reserva)
                            <tr x-show="
                                    (busqueda === '' || '{{ strtolower($reserva->user->name ?? '') }}'.includes(busqueda.toLowerCase())) &&
                                    (filtroEstado === 'todas' || filtroEstado === '{{ $reserva->estado }}') &&
                                    (filtroCancha === 'todas' || filtroCancha == '{{ $reserva->cancha_id }}')"
                                data-nombre="{{ strtolower($reserva->user->name ?? '') }}"
                                data-fecha="{{ $reserva->fecha }}"
                                class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($reserva->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">{{ $reserva->user->name ?? '—' }}</p>
                                            <p class="text-xs text-gray-400">{{ $reserva->user->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600 font-medium">{{ $reserva->cancha->nombre ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-gray-600">
                                    {{ $reserva->fecha instanceof \Carbon\Carbon ? $reserva->fecha->format('d/m/Y') : $reserva->fecha }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-gray-500">
                                    {{ substr($reserva->hora_inicio,0,5) }} – {{ substr($reserva->hora_fin,0,5) }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-green-700">
                                    @if($reserva->tarifa) S/. {{ number_format($reserva->tarifa->precio_hora,2) }} @else — @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeColor($reserva->estado) }}">
                                        {{ $reserva->estado }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('horarios.edit', $reserva) }}"
                                       class="text-xs border border-gray-200 hover:border-green-400 text-gray-500 hover:text-green-700 px-3 py-1.5 rounded-xl transition-colors font-semibold whitespace-nowrap">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($todasReservas->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $todasReservas->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
