<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Canchas de Tenis
            </h2>
            @can('create', App\Models\Cancha::class)
            <a href="{{ route('canchas.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                + Nueva Cancha
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarifas</th>
                            @can('update', App\Models\Cancha::class)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($canchas as $cancha)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $cancha->nombre }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $cancha->tipo }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $cancha->estado === 'Disponible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $cancha->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <a href="{{ route('tarifas.index', ['cancha_id' => $cancha->id]) }}"
                                       class="text-indigo-600 hover:underline">
                                        Ver tarifas
                                    </a>
                                </td>
                                @can('update', App\Models\Cancha::class)
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('canchas.edit', $cancha) }}"
                                       class="text-indigo-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('canchas.destroy', $cancha) }}" class="inline"
                                          onsubmit="return confirm('¿Eliminar esta cancha?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                    No hay canchas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4">
                    {{ $canchas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
