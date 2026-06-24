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
                ->with('warning', 'Debés iniciar sesión para acceder a esa sección.');
        }

        $user = $request->user();

        // Verificar que el usuario tenga un rol válido (enum casting)
        if (!$user->rol instanceof Rol) {
            abort(403, 'Tu cuenta tiene un rol inválido. Contactá al administrador.');
        }

        // Verificar que el rol del usuario esté en la lista de roles permitidos
        if (in_array($user->rol->value, $roles)) {
            return $next($request);
        }

        // Mensaje específico según el rol del usuario
        $mensajes = [
            Rol::Cliente->value => 'Esta sección es exclusiva para el personal del club. Como cliente, podés acceder a reservas y consultar tarifas.',
        ];

        $mensaje = $mensajes[$user->rol->value]
            ?? 'No tenés permisos suficientes para acceder a esta sección.';

        abort(403, $mensaje);
    }
}