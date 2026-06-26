<div align="center">

# 🎾 Top Tennis

**Sistema de gestión de reservas de canchas de tenis**

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-38BDF8?style=flat-square&logo=tailwindcss&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-003B57?style=flat-square&logo=sqlite&logoColor=white)

</div>

---

## Descripción

Top Tennis es una aplicación web para la gestión integral de un club de tenis. Permite administrar canchas, tarifas por turno, reservas de horarios y registro de pagos — con control de acceso basado en tres roles: **Administrador**, **Recepcionista** y **Cliente**.

---

## Características

- **Gestión de canchas** — altas, bajas lógicas, superficie, modalidad (Singles/Dobles) y estado
- **Tarifas por turno** — Mañana, Tarde y Noche con precio por hora configurable por cancha
- **Reservas** — flujo completo con estados: `Reservado → Confirmado → Completado / Cancelado`
- **Pagos** — registro de cobros con método de pago y auditoría
- **Control de solapamiento** — constraint único en BD + validación en PHP para evitar doble reserva
- **Soft Deletes** en todas las tablas de negocio para trazabilidad completa
- **Roles** — Admin, Recepcionista y Cliente con permisos diferenciados

---

## Roles y accesos

| Rol | Puede hacer |
|---|---|
| `admin` | Todo — canchas, tarifas, reservas, usuarios, pagos |
| `recepcionista` | Gestionar reservas y consultar canchas/tarifas |
| `cliente` | Ver y crear sus propias reservas |

---

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 + PHP 8.2 |
| Frontend | Blade + Tailwind CSS 3 + Vite |
| Base de datos | MySQL (XAMPP) |
| Auth | Laravel Breeze |

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/top-tennis.git
cd top-tennis

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Migrar y cargar datos de prueba
php artisan migrate --seed

# 5. Compilar assets y levantar servidor
npm run dev
php artisan serve
```

### Usuarios de prueba

| Email | Password | Rol |
|---|---|---|
| admin@toptennis.com | password | Administrador |
| recepcionista@toptennis.com | password | Recepcionista |
| cliente@toptennis.com | password | Cliente |

---

## Diagrama Entidad-Relación

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

    canchas  ||--o{ tarifas   : "tiene"
    canchas  ||--o{ horarios  : "es reservada en"
    tarifas  ||--o{ horarios  : "aplica a"
    users    ||--o{ horarios  : "reserva"
    horarios ||--o{ pagos     : "genera"
    users    ||--o{ pagos     : "cobra"
```

### Valores posibles por campo

| Tabla | Campo | Valores |
|---|---|---|
| `users` | `rol` | `admin` · `recepcionista` · `cliente` |
| `canchas` | `tipo` | `Arcilla` · `Sintética` · `Hierba` · `Dura` |
| `canchas` | `estado` | `Disponible` · `No Disponible` · `Bloqueada` |
| `canchas` | `modalidad` | `Singles` · `Dobles` |
| `tarifas` | `turno` | `Mañana` · `Tarde` · `Noche` |
| `tarifas` | `estado` | `Activa` · `Inactiva` |
| `horarios` | `estado` | `Reservado` · `Confirmado` · `Cancelado` · `Completado` |
| `horarios` | `metodo_pago` | `Efectivo` · `Tarjeta` · `Transferencia` · `Otro` |
| `pagos` | `metodo_pago` | `Efectivo` · `Tarjeta` · `Transferencia` · `Otro` |
| `pagos` | `estado` | `Pendiente` · `Pagado` · `Reembolsado` |

### Constraints clave

- `horarios` → unique en `(cancha_id, fecha, hora_inicio)` — **impide doble reserva a nivel BD**
- Todas las FK usan `onDelete RESTRICT` — protege la integridad al eliminar
- Todas las tablas de negocio usan **Soft Deletes** para auditoría completa

---

## Estructura del proyecto

```
app/
├── Enums/
│   └── Rol.php                  # Enum: admin | recepcionista | cliente
├── Http/
│   ├── Controllers/
│   │   ├── CanchaController.php
│   │   ├── TarifaController.php
│   │   ├── HorarioController.php
│   │   └── ProfileController.php
│   └── Requests/                # Form Requests con validación de negocio
├── Models/
│   ├── User.php
│   ├── Cancha.php
│   ├── Tarifa.php
│   ├── Horario.php
│   └── Pago.php
database/
├── migrations/                  # 13 migraciones ordenadas
└── seeders/
    └── DatabaseSeeder.php       # Datos de prueba listos
resources/views/                 # Vistas Blade
```

---

## Desarrolladores

Proyecto desarrollado por **Renzo León** y **Diego Magallanes**  
**Dienzo INC** — Software Development

---

## Licencia

Proyecto privado — Top Tennis Club.
