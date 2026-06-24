<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Horarios Reservados
            </h2>
            <a href="{{ route('horarios.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                + Nueva Reserva
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if(in_array(auth()->user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cancha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Turno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($horarios as $horario)
                            <tr>
                                @if(in_array(auth()->user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]))
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $horario->user->name ?? '—' }}</td>
                                @endif
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $horario->cancha->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $horario->fecha->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $horario->hora_inicio }} – {{ $horario->hora_fin }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $horario->tarifa->turno ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    ${{ number_format($horario->tarifa->precio_hora ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $colores = [
                                            'Reservado'  => 'bg-yellow-100 text-yellow-800',
                                            'Confirmado' => 'bg-green-100 text-green-800',
                                            'Cancelado'  => 'bg-red-100 text-red-800',
                                            'Completado' => 'bg-gray-100 text-gray-600',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colores[$horario->estado] ?? '' }}">
                                        {{ $horario->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    @can('update', $horario)
                                        <a href="{{ route('horarios.edit', $horario) }}"
                                           class="text-indigo-600 hover:underline">Editar</a>
                                    @endcan
                                    @can('delete', $horario)
                                        <form method="POST" action="{{ route('horarios.destroy', $horario) }}" class="inline"
                                              onsubmit="return confirm('¿Eliminar este horario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                    No hay horarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4">
                    {{ $horarios->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
