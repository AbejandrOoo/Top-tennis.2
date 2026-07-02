# Arquitectura — Top Tennis

> Cómo está construido el sistema: capas, patrones y decisiones técnicas.
>
> Documentación relacionada: [Modelo de datos](modelo-de-datos.md) · [Reglas de negocio](reglas-de-negocio.md) · [Índice de código](../INDEX.md)

---

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 · PHP 8.5 (XAMPP) |
| Frontend | Blade · Tailwind CSS 3 · Alpine.js 3 |
| Base de datos | MySQL vía XAMPP (`DB=top_tennis`) |
| Autenticación | Laravel Breeze |
| PDF | `barryvdh/laravel-dompdf` |
| QR | `bacon/bacon-qr-code` con `SvgImageBackEnd` (sin GD/Imagick) |

---

## Estructura de capas

```
Petición HTTP
   │
   ▼
routes/web.php ──────────── define la URL y aplica middleware (auth, role:admin)
   │
   ▼
RoleMiddleware ───────────── verifica el rol; si no corresponde, redirige con mensaje
   │
   ▼
FormRequest ──────────────── valida la entrada (reglas + reglas de negocio contra BD)
   │
   ▼
Controller ───────────────── orquesta el caso de uso (transacciones, try/catch, log)
   │
   ▼
Model (Eloquent) ─────────── reglas de dominio: scopes, estados, liberarVencidas(), qrSvg()
   │
   ▼
Vista Blade / PDF ────────── presenta el resultado (o redirect con flash message)
```

**Principio rector — skinny controllers:** los controladores no validan (lo hacen los FormRequests) ni contienen reglas de dominio reutilizables (viven en los modelos como scopes y métodos estáticos). El controlador coordina: valida → transacciona → responde.

---

## Componentes por responsabilidad

| Responsabilidad | Ubicación | Ejemplos |
|---|---|---|
| Rutas y control de acceso | `routes/web.php` | Grupos `auth`, `role:admin` |
| Autorización por rol | `app/Http/Middleware/RoleMiddleware.php` | `role:admin` |
| Autorización por registro | `ReservaController::autorizar()` | dueño-o-admin |
| Validación de entrada | `app/Http/Requests/` | `StoreReservaRequest`, `StoreCanchaRequest` |
| Casos de uso | `app/Http/Controllers/` | `ReservaController`, `CanchaController` |
| Reglas de dominio | `app/Models/` | `Horario::reservables()`, `Reserva::liberarVencidas()` |
| Tipado de roles | `app/enums/Rol.php` | `Rol::Admin`, `Rol::Cliente` |
| Procesos automáticos | `app/Console/Commands/` + `routes/console.php` | `reservas:liberar-vencidas` |
| Datos de prueba | `database/seeders/`, `database/factories/` | `DatabaseSeeder` |

---

## Conceptos técnicos aplicados

| Concepto | Dónde está | Explicación |
|---|---|---|
| **Concurrencia / doble reserva** | `ReservaController::store()` | UPDATE atómico condicional: `UPDATE horarios SET estado='reservado' WHERE id=? AND estado='disponible'`. Si afecta 0 filas, otro usuario tomó el slot primero y la transacción hace rollback. (El constraint `UNIQUE(horario_id)` original se eliminó porque impedía re-reservar horarios liberados; ver [modelo-de-datos.md](modelo-de-datos.md).) |
| **Transacciones (`DB::transaction`)** | `store()`, `storeManual()`, `cancelar()`, `ponerMantenimiento()`, `liberarVencidas()` | Agrupan varios cambios en una operación todo-o-nada: si algo falla, la BD queda como estaba |
| **`lockForUpdate`** | `Reserva::liberarVencidas()` | Bloquea las filas leídas dentro de la transacción para que el proceso automático y una confirmación de pago no toquen la misma reserva a la vez |
| **Snapshot de precio (`monto_pagado`)** | Tabla `reservas`, se llena en `store()` | El precio se congela al reservar; si cambia la tarifa después, tickets e ingresos históricos no se alteran |
| **SoftDeletes (borrado lógico)** | Modelos `Cancha`, `Tarifa`, `Horario`, `Reserva` | `delete()` solo llena `deleted_at`; el registro se conserva para auditoría e integridad histórica |
| **FormRequest** | `app/Http/Requests/` | La validación vive en clases dedicadas; el controlador recibe datos ya validados con `$request->validated()` |
| **Middleware de roles** | `RoleMiddleware` + `web.php` | `role:admin` en la ruta; si el rol no está en la lista, redirige al dashboard con mensaje amigable |
| **Enum de PHP 8.1** | `App\Enums\Rol` + cast en `User` | Tipado fuerte de roles: la BD guarda el string pero el código trabaja con `Rol::Admin`, sin strings mágicos |
| **Route Model Binding** | Rutas con `{cancha}`, `{horario}`, `{reserva}` | Laravel inyecta el modelo ya buscado por ID (404 automático si no existe) |
| **Scopes de Eloquent** | `Horario::reservables()`, `Reserva::vencidas()` | Consultas con nombre de negocio, reutilizables, en lugar de repetir `where` |
| **Scheduler + modo lazy** | `console.php` + controladores | El comando corre cada minuto; como respaldo, las vistas clave ejecutan la misma lógica al cargarse (funciona incluso sin scheduler activo) |
| **Autorización a nivel de objeto** | `ReservaController::autorizar()` | Además del rol, se verifica que la reserva pertenezca al usuario; evita acceder a tickets ajenos cambiando el ID de la URL |
| **Manejo de errores** | Todos los controladores | `try/catch` + `Log::error()`: el usuario recibe mensajes claros en español, nunca stack traces |
| **QR sin extensión GD** | `Reserva::qrSvg()` | `bacon/bacon-qr-code` renderiza a SVG (vectorial); corrección de errores nivel H (~30%) para tolerar el logo central |
| **PDF del ticket** | `ReservaController::descargarTicket()` | `dompdf` renderiza la vista Blade `ticket-pdf` a un PDF A5 descargable |

---

## Flujo de una reserva (extremo a extremo)

1. **Catálogo** — `GET /reservar` → `disponibles()`: ejecuta el mantenimiento lazy, lista `Horario::reservables()`.
2. **Confirmación** — `GET /reservar/{horario}/confirmar` → `confirmar()`: re-verifica que el slot siga siendo válido.
3. **Pago** — `POST /reservas` → `StoreReservaRequest` valida → `store()` transacciona: toma atómica del horario + creación de la reserva (precio congelado, código TT-XXXX, `expira_at` si es Efectivo).
4. **Ticket** — redirect a `ticket()`: QR SVG con los datos de la reserva; opcionalmente `descargarTicket()` en PDF.
5. **Cobro (solo Efectivo)** — el administrador ejecuta `confirmarPago()`; si el plazo vence antes, el proceso automático anula la reserva y libera el horario.
