<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-300 text-xs font-semibold uppercase tracking-widest mb-0.5">Administración</p>
                <h1 class="text-2xl font-black text-white">Tarifas</h1>
            </div>
            <a href="{{ route('tarifas.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva tarifa
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('partials.errores')

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <p class="text-sm font-semibold text-gray-500">{{ $tarifas->total() }} tarifas configuradas</p>
            </div>
            <table class="w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th class="text-left px-6 py-3.5">Nombre</th>
                        <th class="text-left px-6 py-3.5">Precio</th>
                        <th class="text-left px-6 py-3.5">Horarios</th>
                        <th class="text-right px-6 py-3.5">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifas as $tarifa)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                         style="background:#eff6ff;">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $tarifa->nombre_tarifa }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-black text-green-700 text-base">S/ {{ number_format($tarifa->precio, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge badge-gray">{{ $tarifa->horarios_count }} horarios</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('tarifas.edit', $tarifa) }}" class="btn-outline-sm text-xs py-1.5 px-3">Editar</a>
                                    <form method="POST" action="{{ route('tarifas.destroy', $tarifa) }}"
                                          onsubmit="return confirm('¿Eliminar la tarifa {{ addslashes($tarifa->nombre_tarifa) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger text-xs">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center">
                                <p class="text-gray-400 font-medium">No hay tarifas registradas.</p>
                                <a href="{{ route('tarifas.create') }}" class="mt-2 inline-flex text-sm text-green-600 font-semibold hover:underline">
                                    Crear la primera tarifa →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $tarifas->links() }}</div>
    </div>
</x-app-layout>
