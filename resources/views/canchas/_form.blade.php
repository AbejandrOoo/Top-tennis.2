{{-- Formulario compartido de Cancha. Nunca expone el id. --}}
<div class="mb-5">
    <label for="nombre" class="form-label">Nombre</label>
    <input id="nombre" name="nombre" type="text"
           class="form-input {{ $errors->has('nombre') ? 'border-red-400' : '' }}"
           value="{{ old('nombre', $cancha->nombre ?? '') }}" required>
    @error('nombre')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div class="mb-5">
    <label for="tipo_superficie" class="form-label">Tipo de superficie</label>
    <select id="tipo_superficie" name="tipo_superficie"
            class="form-input {{ $errors->has('tipo_superficie') ? 'border-red-400' : '' }}">
        @foreach(['Arcilla', 'Sintética', 'Hierba', 'Dura'] as $sup)
            <option value="{{ $sup }}" {{ old('tipo_superficie', $cancha->tipo_superficie ?? '') === $sup ? 'selected' : '' }}>
                {{ $sup }}
            </option>
        @endforeach
    </select>
    @error('tipo_superficie')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div class="mb-6">
    <label for="estado_mantenimiento" class="form-label">Estado</label>
    <select id="estado_mantenimiento" name="estado_mantenimiento"
            class="form-input {{ $errors->has('estado_mantenimiento') ? 'border-red-400' : '' }}">
        @foreach(['operativa' => 'Operativa', 'en_mantenimiento' => 'En mantenimiento'] as $val => $lbl)
            <option value="{{ $val }}" {{ old('estado_mantenimiento', $cancha->estado_mantenimiento ?? 'operativa') === $val ? 'selected' : '' }}>
                {{ $lbl }}
            </option>
        @endforeach
    </select>
    @error('estado_mantenimiento')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>
