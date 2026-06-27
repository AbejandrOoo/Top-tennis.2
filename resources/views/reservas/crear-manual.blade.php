<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-green-300 text-xs font-semibold uppercase tracking-widest mb-0.5">Administración</p>
            <h1 class="text-2xl font-black text-white">Reserva Manual</h1>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8" x-data="{
            modoCliente: '{{ old('modo_cliente', 'existente') }}',
            clienteId: '{{ old('user_id', '') }}',
            nuevoNombre: '{{ old('nuevo_nombre', '') }}',
            nuevoTelefono: '{{ old('nuevo_telefono', '') }}',
            busqueda: '',
            metodo: '{{ old('metodo_pago', 'Efectivo') }}',
            fecha: '',
            selectedId: {{ old('horario_id', 'null') }},
            selectedPrecio: null,
            selectedHora: '',
            selectedCancha: '',

            horarios: {{ Js::from($horariosJson) }},
            canchas: {{ Js::from($canchasJson) }},
            clientes: {{ Js::from($clientes->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'email' => $c->email, 'telefono' => $c->telefono])) }},

            get fechasDisponibles() {
                return Object.keys(this.horarios).sort();
            },
            get canchasDelDia() {
                const data = this.horarios[this.fecha] || {};
                return Object.keys(data)
                    .sort((a, b) => parseInt(a) - parseInt(b))
                    .map(canchaId => ({
                        ...this.canchas[canchaId],
                        slots: data[canchaId] || []
                    }))
                    .filter(c => c.slots.length > 0);
            },
            get clientesFiltrados() {
                if (!this.busqueda) return this.clientes;
                const q = this.busqueda.toLowerCase();
                return this.clientes.filter(c =>
                    c.name.toLowerCase().includes(q) || c.email.toLowerCase().includes(q) || (c.telefono && c.telefono.includes(q))
                );
            },
            seleccionar(slot, canchaNombre) {
                this.selectedId = slot.id;
                this.selectedPrecio = slot.precio;
                this.selectedHora = slot.hora;
                this.selectedCancha = canchaNombre;
            }
        }">
            @include('partials.errores')

            <form method="POST" action="{{ route('reservas.storeManual') }}">
                @csrf
                <input type="hidden" name="modo_cliente" :value="modoCliente">
                <input type="hidden" name="horario_id" :value="selectedId">

                {{-- PASO 1: Cliente --}}
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <span class="text-xs font-black text-emerald-700">1</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Cliente</h3>
                    </div>

                    <div class="flex gap-2 mb-4">
                        <button type="button" @click="modoCliente = 'existente'"
                                :class="modoCliente === 'existente' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-emerald-400'"
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                            Cliente existente
                        </button>
                        <button type="button" @click="modoCliente = 'nuevo'"
                                :class="modoCliente === 'nuevo' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-emerald-400'"
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                            + Nuevo cliente
                        </button>
                    </div>

                    {{-- Cliente existente --}}
                    <div x-show="modoCliente === 'existente'" x-cloak>
                        <input type="text" x-model="busqueda" placeholder="Buscar por nombre, email o teléfono..."
                               class="form-input mb-3 text-sm" autocomplete="off">
                        <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl divide-y divide-slate-100">
                            <template x-for="c in clientesFiltrados" :key="c.id">
                                <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-emerald-50/50 transition-colors"
                                       :class="clienteId == c.id ? 'bg-emerald-50' : ''">
                                    <input type="radio" name="user_id" :value="c.id" x-model="clienteId"
                                           class="text-emerald-600 focus:ring-emerald-500">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate" x-text="c.name"></p>
                                        <p class="text-xs text-slate-400 truncate" x-text="c.email"></p>
                                    </div>
                                    <span x-show="c.telefono" class="text-xs text-slate-400" x-text="c.telefono"></span>
                                </label>
                            </template>
                            <div x-show="clientesFiltrados.length === 0" class="px-4 py-6 text-center text-sm text-slate-400">
                                No se encontró ningún cliente
                            </div>
                        </div>
                    </div>

                    {{-- Nuevo cliente --}}
                    <div x-show="modoCliente === 'nuevo'" x-cloak>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nuevo_nombre" x-model="nuevoNombre"
                                       class="form-input mt-1 text-sm" placeholder="Nombre completo">
                            </div>
                            <div>
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="nuevo_telefono" x-model="nuevoTelefono"
                                       class="form-input mt-1 text-sm" placeholder="987 654 321">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PASO 2: Fecha y Horario --}}
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <span class="text-xs font-black text-emerald-700">2</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Fecha y Horario</h3>
                    </div>

                    {{-- Fechas --}}
                    <p class="text-xs font-semibold text-slate-400 mb-2">Selecciona un día</p>
                    <div class="flex flex-wrap gap-2 mb-5">
                        <template x-for="f in fechasDisponibles" :key="f">
                            <button type="button" @click="fecha = f; selectedId = null; selectedPrecio = null;"
                                    :class="fecha === f
                                        ? 'bg-emerald-700 text-white border-emerald-700'
                                        : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-400'"
                                    class="flex flex-col items-center px-4 py-2 rounded-xl border-2 text-xs font-bold transition-all">
                                <span x-text="new Date(f + 'T12:00:00').toLocaleDateString('es-PE', {weekday:'short'}).toUpperCase()" class="text-[10px] tracking-wide" :class="fecha === f ? 'text-emerald-200' : 'text-slate-400'"></span>
                                <span x-text="new Date(f + 'T12:00:00').getDate()" class="text-lg leading-tight"></span>
                                <span x-text="new Date(f + 'T12:00:00').toLocaleDateString('es-PE', {month:'short'})" class="text-[10px]" :class="fecha === f ? 'text-emerald-200' : 'text-slate-400'"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Canchas y Horarios --}}
                    <div x-show="fecha" x-transition>
                        <p class="text-xs font-semibold text-slate-400 mb-3">Elige cancha y hora</p>
                        <div class="space-y-4">
                            <template x-for="cancha in canchasDelDia" :key="cancha.id">
                                <div class="border border-slate-200 rounded-xl overflow-hidden">
                                    <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 border-b border-slate-100">
                                        <img :src="cancha.imagen" class="w-8 h-8 rounded-lg object-cover border border-slate-200" :alt="cancha.nombre">
                                        <div>
                                            <p class="text-sm font-bold text-slate-700" x-text="cancha.nombre"></p>
                                            <p class="text-[11px] text-slate-400" x-text="cancha.tipo"></p>
                                        </div>
                                    </div>
                                    <div class="p-3 flex flex-wrap gap-2">
                                        <template x-for="slot in cancha.slots" :key="slot.id">
                                            <button type="button"
                                                    @click="seleccionar(slot, cancha.nombre)"
                                                    :class="selectedId === slot.id
                                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-md'
                                                        : 'bg-white text-slate-700 border-slate-200 hover:border-emerald-400 hover:bg-emerald-50'"
                                                    class="flex flex-col items-center px-3 py-2 rounded-lg border-2 transition-all min-w-[70px]">
                                                <span class="text-sm font-bold" x-text="slot.hora"></span>
                                                <span class="text-[10px]" :class="selectedId === slot.id ? 'text-emerald-200' : 'text-slate-400'"
                                                      x-text="'S/ ' + slot.precio.toFixed(0)"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Resumen de selección --}}
                        <div x-show="selectedId" x-transition
                             class="mt-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm font-semibold text-emerald-800">
                                    <span x-text="selectedCancha"></span> — <span x-text="selectedHora"></span>
                                </span>
                            </div>
                            <span class="text-sm font-black text-emerald-700" x-text="selectedPrecio ? 'S/ ' + selectedPrecio.toFixed(2) : ''"></span>
                        </div>
                    </div>
                </div>

                {{-- PASO 3: Pago --}}
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <span class="text-xs font-black text-emerald-700">3</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Pago</h3>
                    </div>

                    <div class="flex gap-3 mb-4">
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

                    <div x-show="metodo === 'Yape'" x-cloak>
                        <label class="form-label">Nro. operación Yape</label>
                        <input type="text" name="numero_operacion" class="form-input mt-1 text-sm"
                               placeholder="Ej. 01234567" value="{{ old('numero_operacion') }}">
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('reservas.index') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                    <button type="submit" class="btn-primary py-2.5 px-6"
                            :disabled="!selectedId || (modoCliente === 'existente' && !clienteId) || (modoCliente === 'nuevo' && !nuevoNombre.trim())"
                            :class="(!selectedId || (modoCliente === 'existente' && !clienteId) || (modoCliente === 'nuevo' && !nuevoNombre.trim())) ? 'opacity-50 cursor-not-allowed' : ''">
                        Crear reserva
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
