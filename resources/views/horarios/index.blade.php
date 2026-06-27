<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-300 text-xs font-semibold uppercase tracking-widest mb-0.5">Administración</p>
                <h1 class="text-2xl font-black text-white">Horarios</h1>
            </div>
            <a href="{{ route('horarios.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo horario
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('partials.errores')

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <p class="text-sm font-semibold text-gray-500">{{ $horarios->total() }} horarios registrados</p>
            </div>
            <table class="w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th class="text-left px-6 py-3.5">Cancha</th>
                        <th class="text-left px-6 py-3.5">Tarifa</th>
                        <th class="text-left px-6 py-3.5">Fecha y hora</th>
                        <th class="text-left px-6 py-3.5">Estado</th>
                        <th class="text-right px-6 py-3.5">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($horarios as $horario)
                        <tr>
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $horario->cancha?->nombre ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600">{{ $horario->tarifa?->nombre_tarifa ?? '—' }}</span>
                                <span class="font-bold text-green-700 ml-1">
                                    (S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }})
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-700 font-medium">
                                    {{ optional($horario->hora_inicio)->format('d/m/Y') ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ optional($horario->hora_inicio)->format('H:i') ?? '—' }}
                                    –
                                    {{ optional($horario->hora_fin)->format('H:i') ?? '—' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @if($horario->estado === 'disponible')
                                    <span class="badge badge-green">Disponible</span>
                                @else
                                    <span class="badge badge-gray">Reservado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    @if($horario->estado === 'disponible')
                                        <a href="{{ route('horarios.edit', $horario) }}"
                                           class="btn-outline-sm text-xs py-1.5 px-3">Editar</a>
                                        <form method="POST" action="{{ route('horarios.destroy', $horario) }}"
                                              onsubmit="return confirm('¿Eliminar este horario?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-danger text-xs">Eliminar</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-300 font-medium">Bloqueado</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <p class="text-gray-400 font-medium">No hay horarios registrados.</p>
                                <a href="{{ route('horarios.create') }}" class="mt-2 inline-flex text-sm text-green-600 font-semibold hover:underline">
                                    Crear el primer horario →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $horarios->links() }}</div>
    </div>
</x-app-layout>
