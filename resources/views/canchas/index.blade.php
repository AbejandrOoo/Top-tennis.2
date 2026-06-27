<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-300 text-xs font-semibold uppercase tracking-widest mb-0.5">Administración</p>
                <h1 class="text-2xl font-black text-white">Canchas</h1>
            </div>
            <a href="{{ route('canchas.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva cancha
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('partials.errores')

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-500">{{ $canchas->total() }} canchas registradas</p>
            </div>
            <table class="w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th class="text-left px-6 py-3.5">Cancha</th>
                        <th class="text-left px-6 py-3.5">Superficie</th>
                        <th class="text-left px-6 py-3.5">Estado</th>
                        <th class="text-left px-6 py-3.5">Horarios</th>
                        <th class="text-right px-6 py-3.5">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($canchas as $cancha)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg overflow-hidden shrink-0 border border-gray-100">
                                        <img src="{{ $cancha->imagenUrl() }}" alt="{{ $cancha->nombre }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $cancha->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-500">{{ $cancha->tipo_superficie }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($cancha->estado_mantenimiento === 'operativa')
                                    <span class="badge badge-green">Operativa</span>
                                @else
                                    <span class="badge badge-yellow">Mantenimiento</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-500 font-medium">{{ $cancha->horarios_count }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('canchas.edit', $cancha) }}" class="btn-outline-sm text-xs py-1.5 px-3">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('canchas.destroy', $cancha) }}"
                                          onsubmit="return confirm('¿Eliminar la cancha {{ addslashes($cancha->nombre) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger text-xs">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <div class="w-12 h-12 rounded-xl mx-auto mb-3 flex items-center justify-center" style="background:#f0fdf4;">
                                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </div>
                                <p class="text-gray-400 font-medium">No hay canchas registradas.</p>
                                <a href="{{ route('canchas.create') }}" class="mt-3 inline-flex text-sm text-green-600 font-semibold hover:underline">
                                    Crear la primera cancha →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $canchas->links() }}</div>
    </div>
</x-app-layout>
