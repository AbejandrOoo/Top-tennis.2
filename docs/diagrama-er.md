# Diagrama Entidad-Relación — Top Tennis

> Abrí este archivo en VS Code y presioná `Ctrl+Shift+V` para ver el diagrama renderizado.

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

    canchas    ||--o{ tarifas   : "tiene"
    canchas    ||--o{ horarios  : "es reservada en"
    tarifas    ||--o{ horarios  : "aplica a"
    users      ||--o{ horarios  : "reserva"
    horarios   ||--o{ pagos     : "genera"
    users      ||--o{ pagos     : "cobra"
    users      ||--o{ sessions  : "tiene"
```

## Leyenda de roles (`users.rol`)
| Valor | Descripción |
|---|---|
| `admin` | Acceso total |
| `recepcionista` | Gestión de reservas y canchas |
| `cliente` | Solo sus propias reservas |

## Constraints importantes
- `horarios`: unique en `(cancha_id, fecha, hora_inicio)` — evita doble reserva
- `tarifas.cancha_id`: `onDelete RESTRICT` — no se puede borrar cancha con tarifas
- `horarios.cancha_id / tarifa_id / user_id`: `onDelete RESTRICT`
- `pagos.horario_id / cobrado_por`: `onDelete RESTRICT`
- Todas las tablas de negocio usan **Soft Deletes** para auditoría
