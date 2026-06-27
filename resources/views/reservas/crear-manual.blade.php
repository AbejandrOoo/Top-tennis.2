<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-green-300 text-xs font-semibold uppercase tracking-widest mb-0.5">Administración</p>
            <h1 class="text-2xl font-black text-white">Reserva Manual</h1>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8">
            @include('partials.errores')

            <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-sm text-blue-800">
                <strong>Reserva manual:</strong> Crea una reserva directamente para un cliente.
                El pago se marca como aprobado automáticamente.
            </div>

            <form method="POST" action="{{ route('reservas.storeManual') }}" x-data="{
                metodo: '{{ old('metodo_pago', 'Efectivo') }}',
                fecha: '',
                horarios: {{ Js::from($horarios) }},
                get fechasDisponibles() {
                    return Object.keys(this.horarios).sort();
                },
                get horariosDelDia() {
                    return this.horarios[this.fecha] || [];
                }
            }">
                @csrf

                {{-- Cliente --}}
                <div class="mb-5">
                    <label for="user_id" class="form-label">Cliente</label>
                    <select id="user_id" name="user_id" class="form-input mt-1" required>
                        <option value="">Seleccionar cliente...</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('user_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->name }} ({{ $cliente->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fecha --}}
                <div class="mb-5">
                    <label class="form-label">Fecha</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        <template x-for="f in fechasDisponibles" :key="f">
                            <button type="button" @click="fecha = f"
                                    :class="fecha === f
                                        ? 'bg-emerald-700 text-white border-emerald-700'
                                        : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-400'"
                                    class="px-3 py-2 rounded-xl border-2 text-xs font-bold transition-all">
                                <span x-text="new Date(f + 'T00:00:00').toLocaleDateString('es-PE', {weekday:'short', day:'numeric', month:'short'})"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Horario --}}
                <div class="mb-5" x-show="fecha">
                    <label class="form-label">Horario</label>
                    <div class="grid grid-cols-1 gap-2 mt-1 max-h-64 overflow-y-auto">
                        <template x-for="h in horariosDelDia" :key="h.id">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-emerald-400 cursor-pointer transition-colors">
                                <input type="radio" name="horario_id" :value="h.id"
                                       class="text-emerald-600 focus:ring-emerald-500" required>
                                <div class="flex-1 flex items-center justify-between">
                                    <div>
                                        <span class="font-bold text-sm text-slate-800" x-text="h.cancha?.nombre"></span>
                                        <span class="text-xs text-slate-400 ml-2"
                                              x-text="new Date(h.hora_inicio).toLocaleTimeString('es-PE', {hour:'2-digit', minute:'2-digit'}) + ' – ' + new Date(h.hora_fin).toLocaleTimeString('es-PE', {hour:'2-digit', minute:'2-digit'})">
                                        </span>
                                    </div>
                                    <span class="text-sm font-bold text-emerald-700"
                                          x-text="'S/ ' + Number(h.tarifa?.precio || 0).toFixed(2)"></span>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Método de pago --}}
                <div class="mb-5">
                    <label class="form-label">Método de pago</label>
                    <div class="flex gap-3 mt-1">
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer transition-all"
                               :class="metodo === 'Efectivo' ? 'border-emerald-600 bg-emerald-50' : 'border-slate-200'">
                            <input type="radio" name="metodo_pago" value="Efectivo" x-model="metodo"
                                   class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm font-semibold">Efectivo</span>
                        </label>
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer transition-all"
                               :class="metodo === 'Yape' ? 'border-purple-600 bg-purple-50' : 'border-slate-200'">
                            <input type="radio" name="metodo_pago" value="Yape" x-model="metodo"
                                   class="text-purple-600 focus:ring-purple-500">
                            <span class="text-sm font-semibold">Yape</span>
                        </label>
                    </div>
                </div>

                {{-- Número de operación (Yape) --}}
                <div class="mb-6" x-show="metodo === 'Yape'" x-cloak>
                    <label for="numero_operacion" class="form-label">Nro. operación Yape</label>
                    <input type="text" id="numero_operacion" name="numero_operacion"
                           class="form-input mt-1" placeholder="Ej. 01234567"
                           value="{{ old('numero_operacion') }}">
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('reservas.index') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                    <button type="submit" class="btn-primary py-2 px-6">Crear reserva</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
