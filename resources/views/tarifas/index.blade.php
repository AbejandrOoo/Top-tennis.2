<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Tarifas</h1>
                <p class="text-green-200 text-sm mt-0.5">Precios por cancha y turno</p>
            </div>
            @can('create', App\Models\Tarifa::class)
                <a href="{{ route('tarifas.create') }}" class="btn-primary">+ Nueva Tarifa</a>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-3.5 text-left">Cancha</th>
                        <th class="px-6 py-3.5 text-left">Turno</th>
                        <th class="px-6 py-3.5 text-left">Horario</th>
                        <th class="px-6 py-3.5 text-left">Precio / hora</th>
                        <th class="px-6 py-3.5 text-left">Estado</th>
                        @can('update', App\Models\Tarifa::class)
                            <th class="px-6 py-3.5 text-left">Acciones</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tarifas as $tarifa)
                        <tr class="transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ $tarifa->cancha->nombre ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php $turnoColor = ['Mañana'=>'bg-yellow-100 text-yellow-700','Tarde'=>'bg-orange-100 text-orange-700','Noche'=>'bg-indigo-100 text-indigo-700'][$tarifa->turno] ?? 'bg-gray-100 text-gray-600'; @endphp
                                <span class="badge {{ $turnoColor }}">{{ $tarifa->turno }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $tarifa->hora_inicio }} – {{ $tarifa->hora_fin }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-green-700">
                                S/. {{ number_format($tarifa->precio_hora, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="badge {{ $tarifa->estado === 'Activa' ? 'badge-green' : 'badge-gray' }}">
                                    {{ $tarifa->estado }}
                                </span>
                            </td>
                            @can('update', App\Models\Tarifa::class)
                                <td class="px-6 py-4 text-sm flex items-center gap-3">
                                    <a href="{{ route('tarifas.edit', $tarifa) }}" class="btn-outline-sm py-1 px-3 text-xs">Editar</a>
                                    <form method="POST" action="{{ route('tarifas.destroy', $tarifa) }}" class="inline"
                                          onsubmit="return confirm('¿Eliminar esta tarifa?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger text-xs">Eliminar</button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <p class="text-gray-400 font-medium">No hay tarifas registradas</p>
                                @can('create', App\Models\Tarifa::class)
                                    <a href="{{ route('tarifas.create') }}" class="btn-primary mt-3 inline-flex text-sm">+ Agregar tarifa</a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-50">{{ $tarifas->links() }}</div>
        </div>
    </div>
</x-app-layout>
