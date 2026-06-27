<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        try {
            return view('profile.edit', ['user' => $request->user()]);
        } catch (\Throwable $e) {
            Log::error('Error al mostrar perfil', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')
                ->withErrors(['general' => 'No se pudo cargar tu perfil. Intenta de nuevo.']);
        }
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            return Redirect::route('profile.edit')->with('status', 'profile-updated');

        } catch (\Throwable $e) {
            Log::error('Error al actualizar perfil', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return back()->withInput()
                ->withErrors(['general' => 'No se pudo actualizar tu perfil. Intenta de nuevo.']);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        // La validación queda fuera del try/catch para que su
        // ValidationException la maneje Breeze en el bag 'userDeletion'.
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        try {
            $user = $request->user();

            // Borrar ANTES de cerrar sesión: si la BD rechaza (FK, etc.)
            // el usuario sigue autenticado y ve el error en lugar de quedar
            // sin acceso con la cuenta aún existente.
            DB::transaction(function () use ($user) {
                $user->delete();
            });

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/');

        } catch (\Throwable $e) {
            Log::error('Error al eliminar cuenta', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return back()
                ->withErrors(['userDeletion' => [
                    'No se pudo eliminar la cuenta. Es posible que tenga reservas activas.',
                ]]);
        }
    }
}
