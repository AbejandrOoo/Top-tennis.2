<?php

namespace App\Http\Middleware;

use App\Enums\Rol;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si no está autenticado, redirigir al login con mensaje
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('warning', 'Debes iniciar sesión para acceder a esa sección.');
        }

        $user = $request->user();

        // Verificar que el usuario tenga un rol válido (enum casting)
        if (!$user->rol instanceof Rol) {
            abort(403, 'Tu cuenta tiene un rol inválido. Contacta al administrador.');
        }

        // Verificar que el rol del usuario esté en la lista de roles permitidos
        if (in_array($user->rol->value, $roles)) {
            return $next($request);
        }

        // Mensaje específico según el rol del usuario
        $mensajes = [
            Rol::Cliente->value       => 'Acceso denegado. Esa sección es exclusiva para el personal del club.',
            Rol::Recepcionista->value => 'Acceso denegado. Solo el administrador puede acceder a esa sección.',
        ];

        $mensaje = $mensajes[$user->rol->value]
            ?? 'No tienes permisos suficientes para acceder a esa sección.';

        // Redirigir al dashboard con flash de error (nunca mostrar pantalla naranja)
        return redirect()->route('dashboard')->with('error', $mensaje);
    }
}