<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">{{ $esStaff ? 'Reservas' : 'Mis Reservas' }}</h1>
            <p class="text-green-200 text-sm mt-0.5">
                {{ $esStaff ? 'Control de reservas e ingresos' : 'Tus reservas y tickets' }}
            </p>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('partials.errores')

        @if($esStaff)
            {{-- Dashboard financiero: filtro + total --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
                <div class="card px-6 py-4" style="background:linear-gradient(135deg,#0d3d22,#14532d);">
                    <p class="text-green-200 text-xs uppercase tracking-wide">Ingresos confirmados (pagados)</p>
                    <p class="text-2xl font-extrabold text-white">S/ {{ number_format($totalIngresos, 2) }}</p>
                </div>
                <form method="GET" action="{{ route('reservas.index') }}" class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-semibold">Método:</label>
                    <select name="metodo_pago" onchange="this.form.submit()" class="form-input py-2">
                        <option value="">Todos</option>
                        <option value="Yape"     {{ request('metodo_pago') === 'Yape' ? 'selected' : '' }}>Yape</option>
                        <option value="Efectivo" {{ request('metodo_pago') === 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                    </select>
                </form>
            </div>
        @endif

        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th class="text-left px-5 py-3">Código</th>
                        @if($esStaff)<th class="text-left px-5 py-3">Cliente</th>@endif
                        <th class="text-left px-5 py-3">Cancha</th>
                        <th class="text-left px-5 py-3">Fecha y hora</th>
                        <th class="text-left px-5 py-3">Método</th>
                        <th class="text-left px-5 py-3">Pago</th>
                        <th class="text-right px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reservas as $reserva)
                        <tr>
                            <td class="px-5 py-3 font-bold text-green-700">{{ $reserva->codigo_validacion }}</td>
                            @if($esStaff)<td class="px-5 py-3 text-gray-700">{{ $reserva->user->name ?? '—' }}</td>@endif
                            <td class="px-5 py-3 text-gray-700">{{ $reserva->horario->cancha->nombre ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ optional($reserva->horario->hora_inicio)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $reserva->metodo_pago }}</td>
                            <td class="px-5 py-3">
                                @if($reserva->estado_pago === 'aprobado')
                                    <span class="badge badge-green">Aprobado</span>
                                @elseif($reserva->estado_pago === 'anulada')
                                    <span class="badge badge-red">Anulada</span>
                                @else
                                    <span class="badge badge-yellow">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    @if($esStaff && $reserva->metodo_pago === 'Efectivo' && $reserva->estado_pago === 'pendiente' && ! $reserva->estaVencida())
                                        <form method="POST" action="{{ route('reservas.confirmarPago', $reserva) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-primary py-1 px-3 text-xs">Confirmar pago</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('reservas.ticket', $reserva) }}" class="btn-outline-sm">Ticket</a>
                                    @if($reserva->estado_pago !== 'anulada')
                                        <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}"
                                              onsubmit="return confirm('¿Cancelar esta reserva? El horario quedará disponible.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-danger">Cancelar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $esStaff ? 7 : 6 }}" class="px-5 py-10 text-center text-gray-400">No hay reservas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
