<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-300 text-xs font-semibold uppercase tracking-widest mb-0.5">
                    {{ $esStaff ? 'Administración' : 'Mi cuenta' }}
                </p>
                <h1 class="text-2xl font-black text-white">
                    {{ $esStaff ? 'Reservas' : 'Mis Reservas' }}
                </h1>
            </div>
            @if(!$esStaff)
                <a href="{{ route('reservas.disponibles') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva reserva
                </a>
            @endif
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('partials.errores')

        @if($esStaff)
            {{-- Dashboard financiero --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div class="rounded-2xl p-5 flex items-center gap-4"
                     style="background:linear-gradient(135deg,#0d3d22,#166534); border:1px solid rgba(255,255,255,.06);">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                         style="background:rgba(255,255,255,.1);">
                        <svg class="w-6 h-6 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-green-300 text-xs font-semibold uppercase tracking-wide">Ingresos confirmados</p>
                        <p class="text-2xl font-black text-white">S/ {{ number_format($totalIngresos, 2) }}</p>
                    </div>
                </div>

                <div class="card p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </div>
                    <form method="GET" action="{{ route('reservas.index') }}" class="flex items-center gap-2 w-full">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide whitespace-nowrap">Filtrar:</label>
                        <select name="metodo_pago" onchange="this.form.submit()"
                                class="form-input py-1.5 text-sm" style="width:auto; flex:1;">
                            <option value="">Todos los métodos</option>
                            <option value="Yape"     {{ request('metodo_pago') === 'Yape' ? 'selected' : '' }}>Yape</option>
                            <option value="Efectivo" {{ request('metodo_pago') === 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                        </select>
                    </form>
                </div>
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <p class="text-sm font-semibold text-gray-500">{{ $reservas->count() }} reservas</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="table-header">
                        <tr>
                            <th class="text-left px-6 py-3.5">Código</th>
                            @if($esStaff)<th class="text-left px-6 py-3.5">Cliente</th>@endif
                            <th class="text-left px-6 py-3.5">Cancha</th>
                            <th class="text-left px-6 py-3.5">Fecha</th>
                            <th class="text-left px-6 py-3.5">Método</th>
                            <th class="text-left px-6 py-3.5">Monto</th>
                            <th class="text-left px-6 py-3.5">Estado</th>
                            <th class="text-right px-6 py-3.5">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservas as $reserva)
                            <tr>
                                <td class="px-6 py-4">
                                    <span class="font-black tracking-wider" style="color:#15803d;">
                                        {{ $reserva->codigo_validacion }}
                                    </span>
                                </td>
                                @if($esStaff)
                                    <td class="px-6 py-4 text-gray-700 font-medium">
                                        {{ $reserva->user?->name ?? '—' }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $reserva->horario?->cancha?->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-700 font-medium">
                                        {{ optional($reserva->horario?->hora_inicio)->format('d/m/Y') ?? '—' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ optional($reserva->horario?->hora_inicio)->format('H:i') ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge {{ $reserva->metodo_pago === 'Yape' ? 'badge-blue' : 'badge-gray' }}">
                                        {{ $reserva->metodo_pago }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    S/ {{ number_format($reserva->monto_pagado, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($reserva->estado_pago === 'aprobado')
                                        <span class="badge badge-green">Aprobado</span>
                                    @elseif($reserva->estado_pago === 'anulada')
                                        <span class="badge badge-red">Anulada</span>
                                    @else
                                        <span class="badge badge-yellow">Pendiente</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($esStaff && $reserva->metodo_pago === 'Efectivo' && $reserva->estado_pago === 'pendiente' && !$reserva->estaVencida())
                                            <form method="POST" action="{{ route('reservas.confirmarPago', $reserva) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn-primary text-xs py-1.5 px-3">
                                                    Confirmar pago
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('reservas.ticket', $reserva) }}"
                                           class="btn-outline-sm text-xs py-1.5 px-3">Ticket</a>
                                        @if($reserva->estado_pago !== 'anulada')
                                            <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}"
                                                  onsubmit="return confirm('¿Cancelar esta reserva?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-danger text-xs">Cancelar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $esStaff ? 8 : 7 }}" class="px-6 py-14 text-center">
                                    <p class="text-gray-400 font-medium">No hay reservas registradas.</p>
                                    @if(!$esStaff)
                                        <a href="{{ route('reservas.disponibles') }}"
                                           class="mt-2 inline-flex text-sm text-green-600 font-semibold hover:underline">
                                            Reservar una cancha →
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
