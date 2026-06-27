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

    {{-- Modal de Mantenimiento --}}
    <div x-data="{
            open: false,
            canchaId: null,
            canchaNombre: '',
            motivo: '',
            finMantenimiento: '',
            abrir(id, nombre) {
                this.canchaId = id;
                this.canchaNombre = nombre;
                this.motivo = '';
                this.finMantenimiento = '';
                this.open = true;
            }
         }"
         x-cloak>

        {{-- Overlay --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="open = false"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 flex items-center justify-center p-4">

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">

                {{-- Header modal --}}
                <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background:#fef3c7;">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Poner en mantenimiento</h3>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="canchaNombre"></p>
                            </div>
                        </div>
                        <button @click="open = false"
                                class="text-gray-400 hover:text-gray-600 transition-colors ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Formulario --}}
                <form method="POST" :action="`/canchas/${canchaId}/mantenimiento`">
                    @csrf
                    <div class="px-6 py-5 space-y-4">

                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                            <strong>Atención:</strong> Las reservas aprobadas que caigan dentro de este
                            período serán canceladas automáticamente y se registrará un reembolso.
                        </div>

                        <div>
                            <label class="form-label">Motivo del mantenimiento</label>
                            <input type="text" name="motivo_mantenimiento"
                                   x-model="motivo"
                                   placeholder="Ej. Reparación de red, Pintura de líneas…"
                                   class="form-input mt-1"
                                   maxlength="255" required>
                        </div>

                        <div>
                            <label class="form-label">Fin estimado del mantenimiento</label>
                            <input type="datetime-local" name="fin_mantenimiento"
                                   x-model="finMantenimiento"
                                   class="form-input mt-1"
                                   :min="new Date().toISOString().slice(0, 16)"
                                   required>
                            <p class="text-xs text-gray-400 mt-1">La cancha se restaurará automáticamente al llegar esta fecha.</p>
                        </div>

                    </div>

                    <div class="px-6 pb-6 flex justify-end gap-3">
                        <button type="button" @click="open = false"
                                class="btn-outline-sm py-2 px-5">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="py-2 px-6 rounded-xl text-sm font-bold text-white transition-all duration-200"
                                style="background:#d97706; box-shadow:0 2px 8px rgba(217,119,6,.3);"
                                onmouseover="this.style.opacity='.88'"
                                onmouseout="this.style.opacity='1'">
                            Confirmar mantenimiento
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contenido de la página --}}
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

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
                            <th class="text-left px-6 py-3.5">Modalidad</th>
                            <th class="text-left px-6 py-3.5">Luz</th>
                            <th class="text-left px-6 py-3.5">Estado</th>
                            <th class="text-left px-6 py-3.5">Horarios</th>
                            <th class="text-right px-6 py-3.5">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($canchas as $cancha)
                            <tr class="{{ $cancha->estado_mantenimiento === 'en_mantenimiento' ? 'bg-amber-50/40' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg overflow-hidden shrink-0 border border-gray-100">
                                            <img src="{{ $cancha->imagenUrl() }}" alt="{{ $cancha->nombre }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900">{{ $cancha->nombre }}</span>
                                            @if($cancha->estado_mantenimiento === 'en_mantenimiento' && $cancha->motivo_mantenimiento)
                                                <p class="text-xs text-amber-600 mt-0.5">{{ Str::limit($cancha->motivo_mantenimiento, 35) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $cancha->tipo_superficie }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $cancha->modalidad ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($cancha->iluminacion)
                                        <span class="badge badge-blue text-xs">Con luz</span>
                                    @else
                                        <span class="badge badge-gray text-xs">Sin luz</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($cancha->estado_mantenimiento === 'operativa')
                                        <span class="badge badge-green">Operativa</span>
                                    @else
                                        <div>
                                            <span class="badge badge-yellow">Mantenimiento</span>
                                            @if($cancha->fin_mantenimiento)
                                                <p class="text-xs text-amber-600 mt-0.5">
                                                    Hasta {{ $cancha->fin_mantenimiento->format('d/m/Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-500 font-medium">{{ $cancha->horarios_count }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        <a href="{{ route('canchas.edit', $cancha) }}" class="btn-outline-sm text-xs py-1.5 px-3">
                                            Editar
                                        </a>
                                        @if($cancha->estado_mantenimiento === 'operativa')
                                            {{-- Botón que abre el modal --}}
                                            <button type="button"
                                                    @click="abrir({{ $cancha->id }}, '{{ addslashes($cancha->nombre) }}')"
                                                    class="text-xs py-1.5 px-3 rounded-lg border font-semibold transition-all duration-200
                                                           border-amber-400 text-amber-600 hover:bg-amber-50">
                                                Mantenimiento
                                            </button>
                                        @else
                                            {{-- Botón de restaurar --}}
                                            <form method="POST" action="{{ route('canchas.restaurar', $cancha) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="text-xs py-1.5 px-3 rounded-lg border font-semibold transition-all duration-200
                                                               border-green-500 text-green-600 hover:bg-green-50"
                                                        onclick="return confirm('¿Restaurar la cancha a operativa?')">
                                                    Restaurar
                                                </button>
                                            </form>
                                        @endif
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
                                <td colspan="7" class="px-6 py-14 text-center">
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
    </div>
</x-app-layout>
