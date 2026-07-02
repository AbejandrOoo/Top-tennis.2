# Diagramas de Casos de Uso — Top Tennis

> **VS Code:** abrí este archivo y presioná `Ctrl+Shift+V` para ver los diagramas renderizados.
> Complementa a [DIAGRAMA-ER.md](DIAGRAMA-ER.md) (datos) y a [INDEX.md](INDEX.md) (código).

---

## Actores del sistema

| Actor | Descripción | Cómo se identifica en el código |
|---|---|---|
| **Cliente** | Usuario registrado que reserva canchas y paga (Yape o Efectivo) | `users.rol = 'cliente'` ([Rol.php](app/enums/Rol.php)) |
| **Recepcionista** | Personal del club: atiende el mostrador, cobra en efectivo y registra reservas presenciales | `users.rol = 'recepcionista'` |
| **Administrador** | Dueño/gestor del club: configura canchas, tarifas y horarios; ve las finanzas | `users.rol = 'admin'` |
| **Scheduler (tiempo)** | Actor no humano: el Task Scheduler de Laravel dispara procesos automáticos cada minuto | [console.php](routes/console.php) + [LiberarReservasVencidas.php](app/Console/Commands/LiberarReservasVencidas.php) |

**Jerarquía de permisos:** `Admin` hereda todo lo que puede hacer `Recepcionista`, y ambos (staff) además pueden hacer todo lo que hace un `Cliente` (reservar para sí mismos, ver su perfil, etc.). El middleware [RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php) es quien lo hace cumplir ruta por ruta.

---

## 1. Diagrama general del sistema

```mermaid
flowchart LR
    CLI((👤<br>Cliente))
    REC((🎾<br>Recepcionista))
    ADM((👑<br>Administrador))
    SCH((⏰<br>Scheduler))

    subgraph "TOP TENNIS"
        UC_AUTH([Registrarse e iniciar sesión])
        UC_VER([Ver horarios disponibles])
        UC_RESERVAR([Reservar cancha y pagar])
        UC_TICKET([Ver y descargar ticket QR])
        UC_MISRES([Ver historial de reservas])
        UC_CANCELAR([Cancelar reserva])

        UC_CONFPAGO([Confirmar pago en efectivo])
        UC_MANUAL([Crear reserva manual])

        UC_CANCHAS([Gestionar canchas])
        UC_TARIFAS([Gestionar tarifas])
        UC_HORARIOS([Gestionar horarios])
        UC_DASH([Ver dashboard financiero])

        UC_NOSHOW([Liberar reservas vencidas - no-show])
        UC_RESTAURAR([Restaurar canchas post-mantenimiento])
    end

    CLI --> UC_AUTH
    CLI --> UC_VER
    CLI --> UC_RESERVAR
    CLI --> UC_TICKET
    CLI --> UC_MISRES
    CLI --> UC_CANCELAR

    REC --> UC_CONFPAGO
    REC --> UC_MANUAL
    REC --> UC_MISRES
    REC --> UC_CANCELAR
    REC --> UC_TICKET

    ADM --> UC_CANCHAS
    ADM --> UC_TARIFAS
    ADM --> UC_HORARIOS
    ADM --> UC_DASH

    ADM -. hereda permisos de .-> REC
    REC -. hereda permisos de .-> CLI

    SCH --> UC_NOSHOW
    SCH --> UC_RESTAURAR
```

---

## 2. Casos de uso del CLIENTE

```mermaid
flowchart LR
    CLI((👤<br>Cliente))

    subgraph "TOP TENNIS — módulo Cliente"
        UC01([UC-01 Registrarse])
        UC02([UC-02 Iniciar sesión])
        UC03([UC-03 Ver horarios disponibles])
        UC04([UC-04 Reservar cancha])
        UC04a([Elegir método de pago])
        UC04b([Pagar con Yape<br>ingresar N° de operación])
        UC04c([Reservar con pago en Efectivo<br>plazo: 30 min antes del juego])
        UC05([UC-05 Ver ticket digital con QR])
        UC05a([Descargar ticket en PDF])
        UC06([UC-06 Ver mis reservas])
        UC07([UC-07 Cancelar mi reserva])
        UC08([UC-08 Editar perfil<br>datos, contraseña, emoji])
    end

    CLI --> UC01
    CLI --> UC02
    CLI --> UC03
    CLI --> UC04
    CLI --> UC05
    CLI --> UC06
    CLI --> UC07
    CLI --> UC08

    UC04 -.->|«include»| UC04a
    UC04a -.->|«extend»| UC04b
    UC04a -.->|«extend»| UC04c
    UC04 -.->|«include»| UC05
    UC05 -.->|«extend»| UC05a
    UC03 -.->|«include» solo slots reservables| UC04
```

**Reglas visibles para el Cliente:**
- Solo ve horarios `disponibles`, a futuro y de canchas `operativas` (scope `reservables()` en [Horario.php](app/Models/Horario.php)).
- Si paga con **Yape**, la reserva queda `aprobado` al instante y el N° de operación no puede repetirse (constraint `UNIQUE`).
- Si elige **Efectivo**, la reserva queda `pendiente` y caduca 30 min antes de la hora de juego si no paga en recepción.
- Solo puede ver/cancelar/descargar **sus propias** reservas (método `autorizar()` en [ReservaController.php](app/Http/Controllers/ReservaController.php)).

---

## 3. Casos de uso del RECEPCIONISTA

```mermaid
flowchart LR
    REC((🎾<br>Recepcionista))

    subgraph "TOP TENNIS — módulo Recepción"
        UC10([UC-10 Ver todas las reservas])
        UC10a([Filtrar por método de pago<br>Yape / Efectivo])
        UC11([UC-11 Confirmar pago en efectivo<br>pendiente → aprobado])
        UC12([UC-12 Crear reserva manual<br>cliente presencial o por teléfono])
        UC12a([Seleccionar cliente existente])
        UC12b([Registrar cliente nuevo al vuelo<br>solo nombre y teléfono])
        UC13([UC-13 Ver ticket de cualquier reserva])
        UC14([UC-14 Cancelar cualquier reserva<br>libera el horario])
        UC15([UC-15 Validar ticket del cliente<br>escanear QR / código TT-XXXX])
    end

    REC --> UC10
    REC --> UC11
    REC --> UC12
    REC --> UC13
    REC --> UC14
    REC --> UC15

    UC10 -.->|«extend»| UC10a
    UC12 -.->|«extend»| UC12a
    UC12 -.->|«extend»| UC12b
    UC11 -.->|«include» verifica que no esté vencida| UC10
```

**Reglas del Recepcionista:**
- Solo puede confirmar pagos de reservas **en Efectivo** y **pendientes**; si la reserva ya caducó (`estaVencida()`), el sistema lo rechaza.
- En la reserva manual el pago se registra como `aprobado` directo (el cobro ocurre en el mostrador).
- Si registra un cliente nuevo, el sistema le genera email y contraseña aleatorios internos ([ReservaController.php](app/Http/Controllers/ReservaController.php), `storeManual()`).
- **No puede** entrar a los CRUD de canchas, tarifas ni horarios: el middleware `role:admin` lo redirige al dashboard con mensaje de error.

---

## 4. Casos de uso del ADMINISTRADOR

```mermaid
flowchart LR
    ADM((👑<br>Administrador))

    subgraph "TOP TENNIS — módulo Administración"
        direction LR
        UC20([UC-20 Gestionar canchas<br>crear, editar, eliminar])
        UC20a([Poner cancha en mantenimiento])
        UC20b([Reembolso automático<br>de reservas afectadas])
        UC20c([Restaurar cancha a operativa])

        UC21([UC-21 Gestionar tarifas<br>crear, editar, eliminar])

        UC22([UC-22 Generar horarios en masa<br>rango fechas × horas × canchas])
        UC22a([Asignar tarifa día / noche<br>corte a las 18:00])
        UC23([UC-23 Editar horario individual])
        UC24([UC-24 Eliminar horarios de un día])
        UC25([UC-25 Cambiar tarifa en lote<br>solo slots disponibles])

        UC26([UC-26 Ver dashboard<br>stats + ingresos])
        UC27([UC-27 Ver historial completo<br>con total de ingresos])
    end

    ADM --> UC20
    ADM --> UC21
    ADM --> UC22
    ADM --> UC23
    ADM --> UC24
    ADM --> UC25
    ADM --> UC26
    ADM --> UC27

    UC20 -.->|«extend»| UC20a
    UC20a -.->|«include» automático| UC20b
    UC20 -.->|«extend»| UC20c
    UC22 -.->|«include»| UC22a
```

**Reglas del Administrador:**
- No puede eliminar una **cancha** o **tarifa** con horarios asociados (integridad referencial + chequeo en el controlador).
- No puede editar ni eliminar un **horario reservado** (primero hay que cancelar la reserva).
- Al poner una cancha **en mantenimiento** con fecha de fin, las reservas `aprobadas` dentro del rango pasan a `cancelado_por_mantenimiento` y se registra el reembolso de cada una en el log — todo en una transacción ([CanchaController.php](app/Http/Controllers/CanchaController.php), `ponerMantenimiento()`).
- El **ingreso total** del dashboard suma `monto_pagado` (precio congelado al reservar), nunca el precio actual de la tarifa.

---

## 5. Casos de uso del SCHEDULER (procesos automáticos)

```mermaid
flowchart LR
    SCH((⏰<br>Scheduler<br>cada minuto))
    LAZY((🌐<br>Carga de vistas<br>modo lazy))

    subgraph "TOP TENNIS — procesos automáticos"
        UC30([UC-30 Liberar no-shows<br>reservas en Efectivo vencidas])
        UC30a([Marcar reserva como anulada])
        UC30b([Devolver horario a disponible])
        UC31([UC-31 Restaurar canchas<br>cuyo mantenimiento terminó])
    end

    SCH --> UC30
    LAZY --> UC30
    LAZY --> UC31

    UC30 -.->|«include»| UC30a
    UC30 -.->|«include»| UC30b
```

**Doble disparo (defensa en profundidad):** la misma lógica corre por dos vías para que la regla se cumpla aunque el scheduler no esté activo en Windows/XAMPP:
1. **Scheduler:** `Schedule::command('reservas:liberar-vencidas')->everyMinute()->withoutOverlapping()` en [console.php](routes/console.php).
2. **Modo lazy:** `Reserva::liberarVencidas()` y `Cancha::restaurarVencidas()` se llaman al inicio de las vistas clave (`disponibles()`, `confirmar()`, `canchas.index`).

---

## 6. Especificaciones detalladas (formato académico)

### UC-04 — Reservar cancha (el caso de uso central)

| Campo | Detalle |
|---|---|
| **Actor principal** | Cliente |
| **Precondiciones** | Sesión iniciada; existe al menos un horario `disponible`, a futuro, de cancha `operativa` y con tarifa |
| **Disparador** | El cliente pulsa "Reservar" sobre un horario del catálogo |
| **Flujo principal** | 1. El sistema muestra el resumen del horario (cancha, fecha, hora, precio).<br>2. El cliente elige método de pago.<br>3. Si es Yape, ingresa el N° de operación.<br>4. El sistema valida ([StoreReservaRequest.php](app/Http/Requests/StoreReservaRequest.php)): horario aún disponible, a futuro, cancha operativa, N° de operación no reutilizado.<br>5. En una transacción: marca el horario como `reservado` (UPDATE atómico) y crea la reserva con `monto_pagado` congelado y `codigo_validacion` TT-XXXX.<br>6. Redirige al ticket digital con QR. |
| **Flujos alternos** | **4a.** Otro usuario tomó el horario un instante antes → el UPDATE afecta 0 filas, rollback y mensaje "Ese horario acaba de ser reservado por otra persona".<br>**4b.** La tarifa fue eliminada entre la carga y el envío → rollback y aviso.<br>**3a.** Eligió Efectivo pero faltan menos de 30 min → el sistema exige pagar con Yape. |
| **Postcondiciones** | Horario en `reservado`; reserva `aprobado` (Yape) o `pendiente` con `expira_at` (Efectivo); ticket emitido |
| **Código** | `ReservaController::confirmar()` y `store()` |

### UC-11 — Confirmar pago en efectivo

| Campo | Detalle |
|---|---|
| **Actor principal** | Recepcionista (o Admin) |
| **Precondiciones** | Reserva en `Efectivo` con estado `pendiente` y no vencida |
| **Flujo principal** | 1. El cliente paga en el mostrador.<br>2. El staff ubica la reserva en el historial.<br>3. Pulsa "Confirmar pago".<br>4. El sistema valida rol, método y estado, y pasa `estado_pago` a `aprobado` limpiando `expira_at`. |
| **Flujos alternos** | **4a.** La reserva ya caducó (`estaVencida()`) → se rechaza; el no-show ya la anuló o la anulará.<br>**4b.** Un usuario sin rol de staff intenta la acción → redirección con "Solo el personal puede confirmar pagos". |
| **Postcondiciones** | Reserva `aprobado`; su monto ya cuenta en los ingresos del dashboard |
| **Código** | `ReservaController::confirmarPago()` |

### UC-12 — Crear reserva manual

| Campo | Detalle |
|---|---|
| **Actor principal** | Recepcionista o Admin |
| **Precondiciones** | Sesión de staff; hay horarios reservables |
| **Flujo principal** | 1. El staff abre "Reserva manual" (calendario agrupado por día y cancha).<br>2. Selecciona día → cancha → hora.<br>3. Elige cliente existente **o** registra uno nuevo (nombre + teléfono).<br>4. Registra el método de pago; el sistema crea la reserva ya `aprobado`. |
| **Flujos alternos** | **3a.** Cliente nuevo → se crea un User rol `cliente` con email/contraseña internos aleatorios.<br>**4a.** El slot fue tomado en paralelo → mismo mecanismo anti-race que UC-04. |
| **Postcondiciones** | Reserva aprobada a nombre del cliente; horario `reservado` |
| **Código** | `ReservaController::crearManual()` y `storeManual()` |

### UC-20a — Poner cancha en mantenimiento (con reembolsos)

| Campo | Detalle |
|---|---|
| **Actor principal** | Administrador |
| **Precondiciones** | Cancha `operativa` |
| **Flujo principal** | 1. El admin abre el modal e ingresa **motivo** y **fecha/hora de fin** (obligatoria y futura).<br>2. En una transacción: las reservas `aprobadas` con juego dentro del rango pasan a `cancelado_por_mantenimiento` y se registra el reembolso de cada una (código, cliente, monto) en el log.<br>3. La cancha pasa a `en_mantenimiento` y desaparece del catálogo reservable. |
| **Flujos alternos** | **1a.** Fecha de fin en el pasado → error de validación. |
| **Postcondiciones** | Cancha fuera de servicio hasta `fin_mantenimiento`; al vencer, `restaurarVencidas()` la reactiva sola (UC-31) |
| **Código** | `CanchaController::ponerMantenimiento()` / `restaurar()` |

### UC-30 — Liberar no-shows (automático)

| Campo | Detalle |
|---|---|
| **Actor principal** | Scheduler (tiempo) — sin intervención humana |
| **Precondiciones** | Existen reservas `Efectivo` + `pendiente` con `expira_at <= now()` |
| **Flujo principal** | 1. Cada minuto corre `reservas:liberar-vencidas`.<br>2. En una transacción con `lockForUpdate`: cada vencida pasa a `anulada` y su horario vuelve a `disponible`.<br>3. Otro cliente puede tomar ese horario de inmediato. |
| **Flujos alternos** | **1a.** El scheduler no está corriendo → el modo lazy ejecuta la misma lógica al cargar las vistas de reservas. |
| **Postcondiciones** | Sin reservas fantasma; el club no pierde el slot |
| **Código** | `Reserva::liberarVencidas()` (modelo), comando y modo lazy |

---

## 7. Matriz Rol × Caso de uso (resumen para defensa)

| Caso de uso | Cliente | Recepcionista | Admin | Scheduler |
|---|:---:|:---:|:---:|:---:|
| Registrarse / iniciar sesión | ✅ | ✅ | ✅ | — |
| Ver horarios disponibles | ✅ | ✅ | ✅ | — |
| Reservar y pagar (Yape/Efectivo) | ✅ | ✅ | ✅ | — |
| Ver/descargar ticket QR | solo suyos | todos | todos | — |
| Ver historial de reservas | solo suyas | todas + total | todas + total | — |
| Cancelar reserva | solo suyas | todas | todas | — |
| Confirmar pago en efectivo | ❌ | ✅ | ✅ | — |
| Crear reserva manual | ❌ | ✅ | ✅ | — |
| CRUD canchas / mantenimiento | ❌ | ❌ | ✅ | — |
| CRUD tarifas | ❌ | ❌ | ✅ | — |
| CRUD horarios / generación masiva | ❌ | ❌ | ✅ | — |
| Dashboard financiero | ❌ | ✅ | ✅ | — |
| Liberar no-shows | — | — | — | ✅ |
| Restaurar canchas vencidas | — | — | — | ✅ |

> La columna del rol se hace cumplir en dos niveles: **rutas** ([web.php](routes/web.php), middleware `role:`) y **registros** (`autorizar()` para que un cliente no acceda a reservas ajenas por URL).
