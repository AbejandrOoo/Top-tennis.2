<section>
    <header class="mb-6">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" stroke="#15803d" stroke-width="2"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="#15803d" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Cambiar contraseña</h2>
        </div>
        <p class="text-sm text-gray-400 ml-12">Usá una contraseña larga y difícil de adivinar para mayor seguridad.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="form-label">Contraseña actual</label>
            <input id="update_password_current_password" name="current_password" type="password"
                   class="form-input {{ $errors->updatePassword->has('current_password') ? 'border-red-400' : '' }}"
                   placeholder="••••••••" autocomplete="current-password">
            @if($errors->updatePassword->has('current_password'))
                <p class="mt-1.5 text-xs text-red-500">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="form-label">Nueva contraseña</label>
            <input id="update_password_password" name="password" type="password"
                   class="form-input {{ $errors->updatePassword->has('password') ? 'border-red-400' : '' }}"
                   placeholder="Mínimo 8 caracteres" autocomplete="new-password">
            @if($errors->updatePassword->has('password'))
                <p class="mt-1.5 text-xs text-red-500">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="form-label">Confirmar nueva contraseña</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   class="form-input {{ $errors->updatePassword->has('password_confirmation') ? 'border-red-400' : '' }}"
                   placeholder="Repite la nueva contraseña" autocomplete="new-password">
            @if($errors->updatePassword->has('password_confirmation'))
                <p class="mt-1.5 text-xs text-red-500">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-primary py-2 px-6">Actualizar contraseña</button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-sm text-green-600 font-semibold">
                    ✓ Contraseña actualizada
                </p>
            @endif
        </div>
    </form>
</section>
