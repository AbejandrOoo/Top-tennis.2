<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Nueva Tarifa</h1>
            <p class="text-green-200 text-sm mt-0.5">
                <a href="{{ route('tarifas.index') }}" class="hover:underline">Tarifas</a> / Nueva
            </p>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8">
            @include('partials.errores')

            <form method="POST" action="{{ route('tarifas.store') }}">
                @csrf
                @include('tarifas._form', ['tarifa' => null])

                <div class="flex justify-end gap-3 mt-2">
                    <a href="{{ route('tarifas.index') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                    <button type="submit" class="btn-primary py-2 px-6">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
