<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Horarios</h1>
                <p class="text-green-200 text-sm mt-0.5">Slots de cancha disponibles para reservar</p>
            </div>
            <a href="{{ route('horarios.create') }}" class="btn-primary">+ Nuevo horario</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('partials.errores')

        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th class="text-left px-5 py-3">Cancha</th>
                        <th class="text-left px-5 py-3">Tarifa</th>
                        <th class="text-left px-5 py-3">Inicio</th>
                        <th class="text-left px-5 py-3">Fin</th>
                        <th class="text-left px-5 py-3">Estado</th>
                        <th class="text-right px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($horarios as $horario)
                        <tr>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ $horario->cancha?->nombre ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $horario->tarifa?->nombre_tarifa ?? '—' }}
                                <span class="text-green-700 font-semibold">(S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }})</span>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ optional($horario->hora_inicio)->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ optional($horario->hora_fin)->format('H:i') ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if($horario->estado === 'disponible')
                                    <span class="badge badge-green">Disponible</span>
                                @else
                                    <span class="badge badge-gray">Reservado</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    @if($horario->estado === 'disponible')
                                        <a href="{{ route('horarios.edit', $horario) }}" class="btn-outline-sm">Editar</a>
                                        <form method="POST" action="{{ route('horarios.destroy', $horario) }}"
                                              onsubmit="return confirm('¿Eliminar este horario?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-danger">Eliminar</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Reservado</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No hay horarios registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $horarios->links() }}</div>
    </div>
</x-app-layout>
