# Reglas de Negocio — Top Tennis

> Reglas que gobiernan el comportamiento del sistema, con su ubicación exacta en el código.
>
> Documentación relacionada: [Casos de uso](casos-de-uso.md) · [Modelo de datos](modelo-de-datos.md) · [Arquitectura](arquitectura.md)

---

## RN-01 — Pagos

| Regla | Detalle | Código |
|---|---|---|
| RN-01.1 | **Yape** se aprueba al instante: el cliente ya pagó y adjunta su N° de operación | `ReservaController::store()` |
| RN-01.2 | Un **N° de operación Yape no puede reutilizarse** en otra reserva (constraint `UNIQUE`) | `StoreReservaRequest` + migración de `reservas` |
| RN-01.3 | **Efectivo** queda `pendiente` hasta que el administrador confirme el cobro en el mostrador | `ReservaController::confirmarPago()` |
| RN-01.4 | Una reserva en Efectivo **caduca 30 minutos antes** de la hora de juego (`expira_at = hora_inicio − 30 min`) | `Reserva::MINUTOS_GRACIA` |
| RN-01.5 | No se acepta Efectivo si faltan **menos de 30 minutos** para el juego (caducaría de inmediato): se exige Yape | `StoreReservaRequest::withValidator()` |
| RN-01.6 | Solo pueden confirmarse pagos de reservas en Efectivo, pendientes y **no vencidas** | `ReservaController::confirmarPago()` |

## RN-02 — Precios

| Regla | Detalle | Código |
|---|---|---|
| RN-02.1 | El precio se **congela al reservar** (`monto_pagado`): cambios posteriores de tarifa no afectan reservas ni ingresos históricos | `ReservaController::store()` |
| RN-02.2 | En la generación masiva de horarios, la **tarifa día** aplica antes de las 18:00 y la **tarifa noche** desde las 18:00 | `HorarioController::store()` |
| RN-02.3 | El cambio masivo de tarifa solo afecta horarios **disponibles** (los reservados conservan su precio) | `HorarioController::cambiarTarifaMasiva()` |
| RN-02.4 | Los ingresos del dashboard suman `monto_pagado` de reservas **aprobadas** únicamente | `web.php` (dashboard) |

## RN-03 — Disponibilidad y reservas

| Regla | Detalle | Código |
|---|---|---|
| RN-03.1 | Un horario solo es **reservable** si está `disponible`, es a futuro, su cancha está `operativa` y tiene tarifa | `Horario::scopeReservables()` |
| RN-03.2 | Un horario admite **una sola reserva activa**: la toma se hace con un UPDATE atómico condicional (`WHERE estado='disponible'`); si afecta 0 filas, otro usuario ganó y se hace rollback | `ReservaController::store()` |
| RN-03.3 | Cancelar una reserva **libera el horario** (vuelve a `disponible`) para que otro cliente lo tome | `ReservaController::cancelar()` |
| RN-03.4 | Una reserva **anulada** (no-show) no puede cancelarse: su horario ya fue liberado | `ReservaController::cancelar()` |
| RN-03.5 | Cada reserva emite un **código de validación único** con formato `TT-XXXX` y un ticket con QR | `Reserva::generarCodigoValidacion()` / `qrSvg()` |

## RN-04 — No-shows (ausentismo)

| Regla | Detalle | Código |
|---|---|---|
| RN-04.1 | Al vencer `expira_at`, la reserva pasa a `anulada` y su horario vuelve a `disponible` | `Reserva::liberarVencidas()` |
| RN-04.2 | El proceso corre **cada minuto** vía scheduler (`withoutOverlapping`) y, como respaldo, en **modo lazy** al cargar las vistas de reservas | `console.php` + controladores |
| RN-04.3 | La liberación usa transacción con `lockForUpdate` para no chocar con una confirmación de pago simultánea | `Reserva::liberarVencidas()` |

## RN-05 — Mantenimiento de canchas

| Regla | Detalle | Código |
|---|---|---|
| RN-05.1 | Poner una cancha en mantenimiento exige **motivo** y **fecha/hora de fin futura** | `CanchaController::ponerMantenimiento()` |
| RN-05.2 | Las reservas `aprobadas` dentro del periodo pasan a `cancelado_por_mantenimiento` y se **registra el reembolso** de cada una (código, cliente, monto) | `CanchaController::ponerMantenimiento()` |
| RN-05.3 | Una cancha en mantenimiento **no aparece** en el catálogo reservable | `Horario::scopeReservables()` |
| RN-05.4 | Al llegar `fin_mantenimiento`, la cancha se **restaura sola** a `operativa` (scheduler lazy); el administrador también puede restaurarla manualmente | `Cancha::restaurarVencidas()` / `CanchaController::restaurar()` |

## RN-06 — Integridad del catálogo

| Regla | Detalle | Código |
|---|---|---|
| RN-06.1 | No se puede eliminar una **cancha** con horarios asociados | `CanchaController::destroy()` + FK `RESTRICT` |
| RN-06.2 | No se puede eliminar una **tarifa** con horarios asociados | `TarifaController::destroy()` + FK `RESTRICT` |
| RN-06.3 | No se puede **editar ni eliminar** un horario `reservado`; primero debe cancelarse la reserva | `HorarioController::edit()/update()/destroy()` |
| RN-06.4 | La generación masiva **omite duplicados** (misma cancha + misma hora de inicio) y los slots sin tarifa asignada | `HorarioController::store()` |
| RN-06.5 | Todos los borrados de negocio son **lógicos** (SoftDeletes): los registros se conservan para auditoría | Modelos con trait `SoftDeletes` |

## RN-07 — Acceso y seguridad

| Regla | Detalle | Código |
|---|---|---|
| RN-07.1 | Hay **dos roles**: `admin` (gestión total) y `cliente` (reservar y ver lo suyo) | `App\Enums\Rol` |
| RN-07.2 | Las rutas de gestión (canchas, tarifas, horarios, reserva manual, confirmación de pagos) exigen `role:admin` | `web.php` + `RoleMiddleware` |
| RN-07.3 | Un intento de acceso sin permiso **no muestra error crudo**: redirige al dashboard con un mensaje según el rol | `RoleMiddleware` |
| RN-07.4 | Un cliente solo accede a **sus propias reservas**, aunque manipule el ID en la URL (`abort(403)` si no es dueño ni admin) | `ReservaController::autorizar()` |
| RN-07.5 | Toda operación va envuelta en `try/catch` con registro en log: el usuario recibe mensajes claros en español, nunca stack traces | Todos los controladores |
