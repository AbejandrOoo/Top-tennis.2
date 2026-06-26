<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Tarifas</h1>
                <p class="text-green-200 text-sm mt-0.5">Precios aplicables a los horarios</p>
            </div>
            <a href="{{ route('tarifas.create') }}" class="btn-primary">+ Nueva tarifa</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('partials.errores')

        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th class="text-left px-5 py-3">Tarifa</th>
                        <th class="text-left px-5 py-3">Precio</th>
                        <th class="text-left px-5 py-3">Horarios</th>
                        <th class="text-right px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tarifas as $tarifa)
                        <tr>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ $tarifa->nombre_tarifa }}</td>
                            <td class="px-5 py-3 text-green-700 font-bold">S/ {{ number_format($tarifa->precio, 2) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $tarifa->horarios_count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('tarifas.edit', $tarifa) }}" class="btn-outline-sm">Editar</a>
                                    <form method="POST" action="{{ route('tarifas.destroy', $tarifa) }}"
                                          onsubmit="return confirm('¿Eliminar la tarifa {{ $tarifa->nombre_tarifa }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">No hay tarifas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $tarifas->links() }}</div>
    </div>
</x-app-layout>
