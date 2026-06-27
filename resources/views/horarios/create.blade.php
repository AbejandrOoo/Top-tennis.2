<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Generar Horarios</h1>
            <p class="text-green-200 text-sm mt-0.5">
                <a href="{{ route('horarios.index') }}" class="hover:underline">Horarios</a> / Generar
            </p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8">
            @include('partials.errores')

            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800">
                <strong>Generación masiva:</strong> Define el rango de fechas, las canchas y las horas de operación.
                El sistema creará automáticamente todos los slots horarios, omitiendo los que ya existan.
            </div>

            <form method="POST" action="{{ route('horarios.store') }}">
                @csrf

                {{-- Rango de fechas --}}
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Período</p>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio"
                               class="form-input mt-1"
                               value="{{ old('fecha_inicio', today()->toDateString()) }}" required>
                    </div>
                    <div>
                        <label for="fecha_fin" class="form-label">Fecha fin</label>
                        <input type="date" id="fecha_fin" name="fecha_fin"
                               class="form-input mt-1"
                               value="{{ old('fecha_fin', today()->addMonths(1)->toDateString()) }}" required>
                    </div>
                </div>

                {{-- Horas de operación --}}
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Horas de operación</p>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="hora_desde" class="form-label">Hora apertura</label>
                        <select id="hora_desde" name="hora_desde" class="form-input mt-1">
                            @for($h = 0; $h < 24; $h++)
                                <option value="{{ $h }}" {{ old('hora_desde', 6) == $h ? 'selected' : '' }}>
                                    {{ sprintf('%02d:00', $h) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="hora_hasta" class="form-label">Hora cierre</label>
                        <select id="hora_hasta" name="hora_hasta" class="form-input mt-1">
                            @for($h = 1; $h <= 24; $h++)
                                <option value="{{ $h }}" {{ old('hora_hasta', 22) == $h ? 'selected' : '' }}>
                                    {{ sprintf('%02d:00', $h) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Canchas --}}
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Canchas</p>
                <div class="mb-6 space-y-2">
                    @foreach($canchas as $cancha)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-emerald-400 cursor-pointer transition-colors">
                            <input type="checkbox" name="canchas[]" value="{{ $cancha->id }}"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                   {{ in_array($cancha->id, old('canchas', $canchas->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg overflow-hidden shrink-0 border border-slate-100">
                                    <img src="{{ $cancha->imagenUrl() }}" class="w-full h-full object-cover" alt="">
                                </div>
                                <span class="font-semibold text-sm text-slate-800">{{ $cancha->nombre }}</span>
                                <span class="text-xs text-slate-400">({{ $cancha->tipo_superficie }})</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                {{-- Tarifas por franja --}}
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Tarifas por franja horaria</p>
                <div class="space-y-3 mb-8">
                    @php
                        $franjas = [
                            ['tarifa_dia',   'Día (06:00 – 17:59)',   '#f0fdf4', '#15803d'],
                            ['tarifa_noche', 'Noche (18:00 – 21:59)', '#eff6ff', '#1d4ed8'],
                        ];
                    @endphp
                    @foreach($franjas as [$name, $label, $bg, $color])
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:{{ $bg }}">
                                <svg class="w-4 h-4" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                    <path d="M12 7v5l3 3" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label for="{{ $name }}" class="text-sm font-semibold text-slate-700">{{ $label }}</label>
                                <select id="{{ $name }}" name="{{ $name }}" class="form-input mt-1 text-sm">
                                    <option value="">— Sin tarifa —</option>
                                    @foreach($tarifas as $tarifa)
                                        <option value="{{ $tarifa->id }}">
                                            {{ $tarifa->nombre_tarifa }} — S/ {{ number_format($tarifa->precio, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('horarios.index') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                    <button type="submit" class="btn-primary py-2 px-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Generar horarios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
