<section x-data="{ modalOpen: false }">
    <header class="mb-6">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><polyline points="3 6 5 6 21 6" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/><path d="M19 6l-1 14H6L5 6" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11v6M14 11v6" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/><path d="M9 6V4h6v2" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2 class="text-lg font-bold text-red-700">Eliminar cuenta</h2>
        </div>
        <p class="text-sm text-gray-400 ml-12">
            Una vez eliminada, todos tus datos y reservas se borrarán permanentemente.
        </p>
    </header>

    <button @click="modalOpen = true"
            class="px-5 py-2 bg-red-600 text-white text-sm font-semibold rounded-full hover:bg-red-700 transition-colors">
        Eliminar mi cuenta
    </button>

    {{-- Modal confirmación --}}
    <div x-show="modalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         style="display:none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40" @click="modalOpen = false"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 9v4M12 17h.01" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="#dc2626" stroke-width="2" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">¿Eliminar tu cuenta?</h3>
            </div>

            <p class="text-sm text-gray-500 mb-6">
                Esta acción es <strong class="text-gray-800">irreversible</strong>. Todos tus datos, reservas e historial serán eliminados permanentemente. Ingresá tu contraseña para confirmar.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="mb-5">
                    <label for="password_delete" class="form-label">Contraseña</label>
                    <input id="password_delete" name="password" type="password"
                           class="form-input {{ $errors->userDeletion->has('password') ? 'border-red-400' : '' }}"
                           placeholder="Ingresá tu contraseña" required>
                    @if($errors->userDeletion->has('password'))
                        <p class="mt-1.5 text-xs text-red-500">{{ $errors->userDeletion->first('password') }}</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="modalOpen = false"
                            class="btn-outline-sm py-2 px-5">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 text-white text-sm font-semibold rounded-full hover:bg-red-700 transition-colors">
                        Sí, eliminar cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
