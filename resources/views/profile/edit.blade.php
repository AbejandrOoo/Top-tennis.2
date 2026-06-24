<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Mi Perfil</h1>
            <p class="text-green-200 text-sm mt-0.5">Administrá tu información personal y contraseña</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Info personal --}}
        <div class="card p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Contraseña --}}
        <div class="card p-8">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Eliminar cuenta --}}
        <div class="card p-8 border border-red-100">
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</x-app-layout>
