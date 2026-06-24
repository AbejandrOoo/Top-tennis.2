<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Modelo no encontrado (ej: /canchas/9999) → 404 personalizado
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            $modelo = class_basename($e->getModel());
            $mensajes = [
                'Cancha'  => 'La cancha que buscas no existe o fue eliminada.',
                'Tarifa'  => 'La tarifa que buscas no existe o fue eliminada.',
                'Horario' => 'El horario que buscas no existe o fue eliminado.',
                'User'    => 'El usuario que buscas no existe.',
            ];
            $mensaje = $mensajes[$modelo] ?? 'El recurso que buscas no existe o fue eliminado.';

            return response()->view('errors.404', compact('mensaje'), 404);
        });

        // Ruta no encontrada → 404 personalizado
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->view('errors.404', [
                'mensaje' => 'La página que buscas no existe. Verifica la URL e intenta de nuevo.',
            ], 404);
        });

        // Sin permiso (policy o abort(403)) → 403 personalizado
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            $mensaje = $e->getMessage() ?: 'No tienes permisos para realizar esta acción.';

            return response()->view('errors.403', compact('mensaje'), 403);
        });

        // Token CSRF expirado → 419 personalizado
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return response()->view('errors.419', [], 419);
        });

        // Error de base de datos → log + 500 personalizado
        $exceptions->render(function (QueryException $e, Request $request) {
            Log::error('Error de base de datos', [
                'mensaje' => $e->getMessage(),
                'url'     => $request->fullUrl(),
                'usuario' => $request->user()?->email ?? 'invitado',
            ]);

            // Si la solicitud es de un formulario, redirigir con mensaje amigable
            if ($request->method() !== 'GET') {
                return back()->withInput()
                    ->with('error', 'Ocurrió un error al procesar la operación en la base de datos. Intenta de nuevo.');
            }

            return response()->view('errors.500', [
                'mensaje' => 'Ocurrió un error al consultar la base de datos. El equipo técnico fue notificado.',
            ], 500);
        });

    })->create();