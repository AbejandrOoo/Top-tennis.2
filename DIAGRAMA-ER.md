# Diagrama Entidad-Relación — Top Tennis

> **VS Code:** abrí este archivo y presioná `Ctrl+Shift+V` para ver el diagrama renderizado.

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        string telefono
        string password
        string rol
        string emoji_perfil
        timestamp email_verified_at
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    canchas {
        bigint id PK
        string nombre UK
        string tipo
        enum estado
        enum modalidad
        tinyint capacidad
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }

    tarifas {
        bigint id PK
        bigint cancha_id FK
        decimal precio_hora
        time hora_inicio
        time hora_fin
        string turno
        enum estado
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }

    horarios {
        bigint id PK
        bigint cancha_id FK
        bigint tarifa_id FK
        bigint user_id FK
        date fecha
        time hora_inicio
        time hora_fin
        enum estado
        text notas
        string metodo_pago
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }

    pagos {
        bigint id PK
        bigint horario_id FK
        bigint cobrado_por FK
        decimal monto
        enum metodo_pago
        enum estado
        date fecha_pago
        text notas
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }

    sessions {
        string id PK
        bigint user_id FK
        string ip_address
        text user_agent
        longtext payload
        int last_activity
    }

    password_reset_tokens {
        string email PK
        string token
        timestamp created_at
    }

    canchas  ||--o{ tarifas   : "tiene"
    canchas  ||--o{ horarios  : "es reservada en"
    tarifas  ||--o{ horarios  : "aplica a"
    users    ||--o{ horarios  : "reserva"
    horarios ||--o{ pagos     : "genera"
    users    ||--o{ pagos     : "cobra"
    users    ||--o{ sessions  : "tiene"
```

---

## Valores posibles por campo

| Tabla | Campo | Valores |
|---|---|---|
| `users` | `rol` | `admin` · `recepcionista` · `cliente` |
| `canchas` | `tipo` | `Arcilla` · `Sintética` · `Hierba` · `Dura` |
| `canchas` | `estado` | `Disponible` · `No Disponible` · `Bloqueada` |
| `canchas` | `modalidad` | `Singles` · `Dobles` |
| `tarifas` | `turno` | `Mañana` · `Tarde` · `Noche` |
| `tarifas` | `estado` | `Activa` · `Inactiva` |
| `horarios` | `estado` | `Reservado` · `Confirmado` · `Cancelado` · `Completado` |
| `horarios` | `metodo_pago` | `Efectivo` · `Tarjeta` · `Transferencia` · `Otro` *(nullable)* |
| `pagos` | `metodo_pago` | `Efectivo` · `Tarjeta` · `Transferencia` · `Otro` |
| `pagos` | `estado` | `Pendiente` · `Pagado` · `Reembolsado` |

---

## Constraints clave

| Constraint | Tabla | Detalle |
|---|---|---|
| Unique | `horarios` | `(cancha_id, fecha, hora_inicio)` — impide doble reserva a nivel BD |
| Unique | `users` | `email` |
| Unique | `canchas` | `nombre` |
| FK RESTRICT | `tarifas` | `cancha_id → canchas.id` |
| FK RESTRICT | `horarios` | `cancha_id`, `tarifa_id`, `user_id` |
| FK RESTRICT | `pagos` | `horario_id`, `cobrado_por` |
| Soft Delete | todas las tablas de negocio | `deleted_at` para auditoría |

---

## Relaciones entre modelos Laravel

| Modelo | Relación | Hacia |
|---|---|---|
| `User` | `hasMany` | `Horario` |
| `User` | `hasMany` | `Pago` *(como cobrador)* |
| `Cancha` | `hasMany` | `Tarifa` |
| `Cancha` | `hasMany` | `Horario` |
| `Tarifa` | `belongsTo` | `Cancha` |
| `Tarifa` | `hasMany` | `Horario` |
| `Horario` | `belongsTo` | `Cancha`, `Tarifa`, `User` |
| `Horario` | `hasMany` | `Pago` |
| `Pago` | `belongsTo` | `Horario` |
| `Pago` | `belongsTo` | `User` *(cobrador)* |
