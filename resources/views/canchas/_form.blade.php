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

{{-- Selector visual de imagen de cancha --}}
<div class="mb-5" x-data="{ img: '{{ old('imagen', $cancha->imagen ?? '') }}' }">
    <label class="form-label">Imagen de la cancha</label>
    <p class="text-xs text-gray-400 mb-3">Deja en blanco para usar la imagen predeterminada del tipo de superficie.</p>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach(['Arcilla.jpeg' => 'Arcilla', 'CespedArtificial.jpeg' => 'Césped Artificial', 'Cesped.jpeg' => 'Césped Natural', 'Dura.jpeg' => 'Cancha Dura'] as $archivo => $etiqueta)
            <label class="cursor-pointer">
                <input type="radio" name="imagen" value="{{ $archivo }}"
                       x-model="img" class="hidden">
                <div class="rounded-xl overflow-hidden border-2 transition-all"
                     :class="img === '{{ $archivo }}' ? 'border-green-500 ring-2 ring-green-300' : 'border-gray-200'">
                    <img src="{{ asset('images/' . $archivo) }}" alt="{{ $etiqueta }}"
                         class="w-full h-24 object-cover">
                    <div class="px-2 py-1.5 text-center text-xs font-semibold"
                         :class="img === '{{ $archivo }}' ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-600'">
                        {{ $etiqueta }}
                    </div>
                </div>
            </label>
        @endforeach
    </div>
    <button type="button" @click="img = ''"
            class="mt-2 text-xs text-gray-400 hover:text-red-500 underline transition-colors">
        Quitar selección (usar predeterminada)
    </button>
    @error('imagen')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
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
