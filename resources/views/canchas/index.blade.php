<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Canchas</h1>
                <p class="text-green-200 text-sm mt-0.5">Gestión de canchas del club</p>
            </div>
            <a href="{{ route('canchas.create') }}" class="btn-primary">+ Nueva cancha</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('partials.errores')

        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th class="text-left px-5 py-3">Nombre</th>
                        <th class="text-left px-5 py-3">Superficie</th>
                        <th class="text-left px-5 py-3">Estado</th>
                        <th class="text-left px-5 py-3">Horarios</th>
                        <th class="text-right px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($canchas as $cancha)
                        <tr>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ $cancha->nombre }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $cancha->tipo_superficie }}</td>
                            <td class="px-5 py-3">
                                @if($cancha->estado_mantenimiento === 'operativa')
                                    <span class="badge badge-green">Operativa</span>
                                @else
                                    <span class="badge badge-yellow">En mantenimiento</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $cancha->horarios_count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('canchas.edit', $cancha) }}" class="btn-outline-sm">Editar</a>
                                    <form method="POST" action="{{ route('canchas.destroy', $cancha) }}"
                                          onsubmit="return confirm('¿Eliminar la cancha {{ $cancha->nombre }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No hay canchas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $canchas->links() }}</div>
    </div>
</x-app-layout>
