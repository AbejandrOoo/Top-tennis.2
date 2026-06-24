<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Canchas de Tenis</h1>
                <p class="text-green-200 text-sm mt-0.5">Gestión de canchas del club</p>
            </div>
            @can('create', App\Models\Cancha::class)
                <a href="{{ route('canchas.create') }}" class="btn-primary">+ Nueva Cancha</a>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-3.5 text-left">Nombre</th>
                        <th class="px-6 py-3.5 text-left">Superficie</th>
                        <th class="px-6 py-3.5 text-left">Estado</th>
                        <th class="px-6 py-3.5 text-left">Tarifas</th>
                        @can('update', App\Models\Cancha::class)
                            <th class="px-6 py-3.5 text-left">Acciones</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($canchas as $cancha)
                        <tr class="transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900 text-sm">{{ $cancha->nombre }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <span class="flex items-center gap-1.5">
                                    @php
                                        $tipoColor = ['Arcilla' => 'bg-orange-100 text-orange-700', 'Sintética' => 'bg-blue-100 text-blue-700'][$cancha->tipo] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="badge {{ $tipoColor }}">{{ $cancha->tipo }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="badge {{ $cancha->estado === 'Disponible' ? 'badge-green' : 'badge-red' }}">
                                    {{ $cancha->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('tarifas.index') }}"
                                   class="text-green-600 hover:text-green-800 font-medium hover:underline">
                                    Ver tarifas →
                                </a>
                            </td>
                            @can('update', App\Models\Cancha::class)
                                <td class="px-6 py-4 text-sm flex items-center gap-3">
                                    <a href="{{ route('canchas.edit', $cancha) }}" class="btn-outline-sm py-1 px-3 text-xs">Editar</a>
                                    <form method="POST" action="{{ route('canchas.destroy', $cancha) }}" class="inline"
                                          onsubmit="return confirm('¿Eliminar la cancha «{{ $cancha->nombre }}»?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger text-xs">Eliminar</button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <svg class="mx-auto mb-3 text-gray-300" width="40" height="40" viewBox="0 0 56 56" fill="none">
                                    <circle cx="28" cy="28" r="26" stroke="currentColor" stroke-width="3"/>
                                    <path d="M6 22 Q28 13 50 22" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                    <path d="M6 34 Q28 43 50 34" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                </svg>
                                <p class="text-gray-400 font-medium">No hay canchas registradas</p>
                                @can('create', App\Models\Cancha::class)
                                    <a href="{{ route('canchas.create') }}" class="btn-primary mt-3 inline-flex text-sm">+ Agregar primera cancha</a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-50">{{ $canchas->links() }}</div>
        </div>
    </div>
</x-app-layout>
