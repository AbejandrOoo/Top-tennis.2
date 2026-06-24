<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tarifas
            </h2>
            @can('create', App\Models\Tarifa::class)
            <a href="{{ route('tarifas.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                + Nueva Tarifa
            </a>
            @endcan
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cancha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Turno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            @can('update', App\Models\Tarifa::class)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tarifas as $tarifa)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $tarifa->cancha->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $tarifa->turno }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $tarifa->hora_inicio }} – {{ $tarifa->hora_fin }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                                    ${{ number_format($tarifa->precio_hora, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $tarifa->estado === 'Activa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $tarifa->estado }}
                                    </span>
                                </td>
                                @can('update', App\Models\Tarifa::class)
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('tarifas.edit', $tarifa) }}"
                                       class="text-indigo-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('tarifas.destroy', $tarifa) }}" class="inline"
                                          onsubmit="return confirm('¿Eliminar esta tarifa?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    No hay tarifas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4">
                    {{ $tarifas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
