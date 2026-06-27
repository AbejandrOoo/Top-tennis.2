{{-- Formulario compartido de Horario. FK mostradas por nombre, nunca el id. --}}
@php $horario = $horario ?? null; @endphp
<div class="mb-5">
    <label for="cancha_id" class="form-label">Cancha</label>
    <select id="cancha_id" name="cancha_id"
            class="form-input {{ $errors->has('cancha_id') ? 'border-red-400' : '' }}">
        <option value="">Seleccione una cancha...</option>
        @foreach($canchas as $cancha)
            <option value="{{ $cancha->id }}"
                {{ (string) old('cancha_id', optional($horario)->cancha_id) === (string) $cancha->id ? 'selected' : '' }}>
                {{ $cancha->nombre }} ({{ $cancha->tipo_superficie }})
            </option>
        @endforeach
    </select>
    @error('cancha_id')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div class="mb-5">
    <label for="tarifa_id" class="form-label">Tarifa</label>
    <select id="tarifa_id" name="tarifa_id"
            class="form-input {{ $errors->has('tarifa_id') ? 'border-red-400' : '' }}">
        <option value="">Seleccione una tarifa...</option>
        @foreach($tarifas as $tarifa)
            <option value="{{ $tarifa->id }}"
                {{ (string) old('tarifa_id', optional($horario)->tarifa_id) === (string) $tarifa->id ? 'selected' : '' }}>
                {{ $tarifa->nombre_tarifa }} — S/ {{ number_format($tarifa->precio, 2) }}
            </option>
        @endforeach
    </select>
    @error('tarifa_id')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-2 gap-4 mb-5">
    <div>
        <label for="hora_inicio" class="form-label">Inicio</label>
        <input id="hora_inicio" name="hora_inicio" type="datetime-local"
               class="form-input {{ $errors->has('hora_inicio') ? 'border-red-400' : '' }}"
               value="{{ old('hora_inicio', $horario ? $horario->hora_inicio->format('Y-m-d\TH:i') : '') }}" required>
        @error('hora_inicio')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="hora_fin" class="form-label">Fin</label>
        <input id="hora_fin" name="hora_fin" type="datetime-local"
               class="form-input {{ $errors->has('hora_fin') ? 'border-red-400' : '' }}"
               value="{{ old('hora_fin', $horario ? $horario->hora_fin->format('Y-m-d\TH:i') : '') }}" required>
        @error('hora_fin')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
</div>

@if($horario)
    <div class="mb-6 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-500">
        Estado actual:
        @if($horario->estado === 'disponible')
            <span class="badge badge-green">Disponible</span>
        @else
            <span class="badge badge-gray">Reservado</span>
        @endif
        <p class="mt-1 text-xs text-gray-400">El estado lo gestiona automáticamente el flujo de reservas.</p>
    </div>
@endif
