<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">
                    {{ in_array(auth()->user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]) ? 'Horarios Reservados' : 'Mis Reservas' }}
                </h1>
                <p class="text-green-200 text-sm mt-0.5">Gestión de horarios y reservas</p>
            </div>
            <a href="{{ route('horarios.create') }}" class="btn-primary">+ Nueva Reserva</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="table-header">
                        <tr>
                            @if(in_array(auth()->user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]))
                                <th class="px-6 py-3.5 text-left">Cliente</th>
                            @endif
                            <th class="px-6 py-3.5 text-left">Cancha</th>
                            <th class="px-6 py-3.5 text-left">Fecha</th>
                            <th class="px-6 py-3.5 text-left">Horario</th>
                            <th class="px-6 py-3.5 text-left">Turno</th>
                            <th class="px-6 py-3.5 text-left">Precio</th>
                            <th class="px-6 py-3.5 text-left">Estado</th>
                            <th class="px-6 py-3.5 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($horarios as $horario)
                            <tr class="transition-colors">
                                @if(in_array(auth()->user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]))
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center text-green-700 text-xs font-bold shrink-0">
                                                {{ strtoupper(substr($horario->user->name ?? 'X', 0, 1)) }}
                                            </div>
                                            {{ $horario->user->name ?? '—' }}
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    {{ $horario->cancha->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $horario->fecha->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                                    {{ $horario->hora_inicio }} – {{ $horario->hora_fin }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @php $turnoColor = ['Mañana'=>'bg-yellow-100 text-yellow-700','Tarde'=>'bg-orange-100 text-orange-700','Noche'=>'bg-indigo-100 text-indigo-700'][$horario->tarifa->turno ?? ''] ?? 'bg-gray-100 text-gray-600'; @endphp
                                    <span class="badge {{ $turnoColor }}">{{ $horario->tarifa->turno ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-green-700">
                                    S/. {{ number_format($horario->tarifa->precio_hora ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $badge = ['Reservado'=>'badge-yellow','Confirmado'=>'badge-green','Cancelado'=>'badge-red','Completado'=>'badge-gray'][$horario->estado] ?? 'badge-gray';
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $horario->estado }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm flex items-center gap-2">
                                    @can('update', $horario)
                                        <a href="{{ route('horarios.edit', $horario) }}" class="btn-outline-sm py-1 px-3 text-xs">Editar</a>
                                    @endcan
                                    @can('delete', $horario)
                                        <form method="POST" action="{{ route('horarios.destroy', $horario) }}" class="inline"
                                              onsubmit="return confirm('¿Eliminar este horario?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-danger text-xs">Eliminar</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-14 text-center">
                                    <svg class="mx-auto mb-3 text-gray-300" width="40" height="40" viewBox="0 0 24 24" fill="none">
                                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <p class="text-gray-400 font-medium">No hay horarios registrados</p>
                                    <a href="{{ route('horarios.create') }}" class="btn-primary mt-3 inline-flex text-sm">+ Nueva reserva</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-50">{{ $horarios->links() }}</div>
        </div>
    </div>
</x-app-layout>
