@php
    $tipoLabel = fn($tipo) => match(strtolower($tipo ?? '')) {
        'arcilla'            => 'Arcilla',
        'sintética','sintetica' => 'Sintética',
        'grass','hierba'     => 'Hierba',
        'dura'               => 'Dura',
        default              => $tipo,
    };

    $precioBase  = fn($c) => $c->tarifas->min('precio_hora');
    $tarifaPunta = fn($c) => $c->tarifas->sortByDesc('precio_hora')->first()?->precio_hora;
@endphp

<div class="flex items-start justify-between mb-6">
    <div>
        <h2 class="text-2xl font-black text-green-900">Gestión de Canchas</h2>
        <p class="text-sm text-gray-400 mt-0.5">CRUD completo · {{ $canchas->count() }} canchas registradas</p>
    </div>
    <a href="{{ route('canchas.create') }}"
       class="flex items-center gap-2 font-bold px-5 py-2.5 rounded-2xl text-white text-sm transition-all"
       style="background: linear-gradient(90deg,#4ade80,#22c55e); box-shadow:0 4px 14px rgba(34,197,94,.35);">
        + Agregar Cancha
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    @foreach($canchas as $cancha)
        @php
            $operativa  = $cancha->estado === 'Disponible';
            $bloqueada  = $cancha->estado === 'Bloqueada';
            $base       = $precioBase($cancha);
            $punta      = $tarifaPunta($cancha);
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            {{-- Header --}}
            <div class="flex items-start justify-between mb-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="font-black text-green-900 text-lg">{{ $cancha->nombre }}</h3>
                    {{-- Modalidad badge --}}
                    <span class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full border"
                          style="{{ $cancha->modalidad === 'Singles'
                            ? 'background:#f0fdf4;border-color:#bbf7d0;color:#15803d;'
                            : 'background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8;' }}">
                        @if($cancha->modalidad === 'Singles')
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        @else
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="7" r="3" stroke="currentColor" stroke-width="2"/><circle cx="17" cy="7" r="3" stroke="currentColor" stroke-width="2"/><path d="M3 20c0-3 2.7-5 6-5m12 5c0-3-2.7-5-6-5s-6 2-6 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        @endif
                        {{ $cancha->modalidad }}
                    </span>
                </div>
                {{-- Estado badge --}}
                <span class="text-xs font-bold px-3 py-1 rounded-full shrink-0
                    {{ $operativa ? 'bg-green-50 text-green-700 border border-green-200'
                        : ($bloqueada ? 'bg-yellow-50 text-yellow-700 border border-yellow-200'
                        : 'bg-orange-50 text-orange-600 border border-orange-200') }}">
                    {{ $operativa ? 'Activa' : ($bloqueada ? 'Bloqueada' : 'Mantenimiento') }}
                </span>
            </div>

            <p class="text-gray-400 text-sm mb-5">{{ $tipoLabel($cancha->tipo) }}</p>

            {{-- Info precios --}}
            <div class="space-y-1.5 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Precio base:</span>
                    <span class="font-semibold text-gray-800">
                        @if($base) S/ {{ number_format($base,2) }}/hr @else — @endif
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Tarifa punta (17–20h):</span>
                    <span class="font-semibold text-gray-800">
                        @if($punta && $punta != $base) S/ {{ number_format($punta,2) }}/hr
                        @elseif($base) S/ {{ number_format($base * 1.2, 2) }}/hr
                        @else — @endif
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Capacidad:</span>
                    <span class="font-semibold text-gray-800">{{ $cancha->capacidad }} jugadores</span>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-2">
                {{-- Editar --}}
                <a href="{{ route('canchas.edit', $cancha) }}"
                   class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-200 text-gray-700 hover:border-green-400 hover:text-green-700 text-xs font-semibold transition-all">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Editar
                </a>

                {{-- Bloquear --}}
                <form method="POST" action="{{ route('canchas.toggleEstado', $cancha) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="estado" value="Bloqueada">
                    <button type="submit"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-yellow-200 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 text-xs font-semibold transition-all">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Bloquear
                    </button>
                </form>

                {{-- Activar / Desactivar --}}
                <form method="POST" action="{{ route('canchas.toggleEstado', $cancha) }}">
                    @csrf @method('PATCH')
                    @if($operativa)
                        <input type="hidden" name="estado" value="No Disponible">
                        <button type="submit"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-orange-200 text-orange-600 bg-orange-50 hover:bg-orange-100 text-xs font-semibold transition-all">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M18.36 6.64A9 9 0 115.64 17.36M12 2v4M2 12h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Desactivar
                        </button>
                    @else
                        <input type="hidden" name="estado" value="Disponible">
                        <button type="submit"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-green-200 text-green-700 bg-green-50 hover:bg-green-100 text-xs font-semibold transition-all">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Activar
                        </button>
                    @endif
                </form>

                {{-- Eliminar --}}
                <form method="POST" action="{{ route('canchas.destroy', $cancha) }}"
                      onsubmit="return confirm('¿Eliminar {{ $cancha->nombre }}? Esta acción no se puede deshacer.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-9 h-9 rounded-xl border border-red-200 text-red-500 hover:bg-red-50 flex items-center justify-center transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><polyline points="3 6 5 6 21 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>
