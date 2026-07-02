<div align="center">

# 🎾 Top Tennis

**Sistema de gestión de reservas de canchas de tenis**

![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-38BDF8?style=flat-square&logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-XAMPP-4479A1?style=flat-square&logo=mysql&logoColor=white)

</div>

---

## Descripción

Top Tennis es una aplicación web para la gestión integral de un club de tenis. Permite al administrador configurar canchas, tarifas y horarios disponibles; a los clientes reservar esos horarios pagando con **Yape** (inmediato) o **Efectivo** (presencial); y al administrador confirmar cobros y monitorear ingresos desde un dashboard financiero.

---

## Documentación

| Documento | Contenido |
|---|---|
| [INDEX.md](INDEX.md) | Índice de código: mapa de todos los archivos por módulo |
| [docs/arquitectura.md](docs/arquitectura.md) | Capas del sistema, patrones y conceptos técnicos |
| [docs/modelo-de-datos.md](docs/modelo-de-datos.md) | Diagrama ER, constraints y flujo de estados |
| [docs/casos-de-uso.md](docs/casos-de-uso.md) | Casos de uso del sistema por actor, con diagramas |
| [docs/casos-de-uso-negocio.md](docs/casos-de-uso-negocio.md) | Casos de uso de negocio y trazabilidad |
| [docs/reglas-de-negocio.md](docs/reglas-de-negocio.md) | Catálogo de reglas de negocio |

---

## Características principales

- **Gestión de canchas** — altas, bajas lógicas (SoftDelete), tipo de superficie y estado de mantenimiento
- **Tarifas independientes** — precio configurable por cada tarifa, aplicable a cualquier cancha
- **Horarios (slots)** — el admin crea franjas cancha+tarifa+fecha/hora; el sistema bloquea cruces
- **Reservas con doble método de pago** — Yape (aprobado al instante) y Efectivo (pendiente con plazo)
- **Regla de no-show** — reservas en Efectivo sin pagar a 30 min del partido se anulan automáticamente y liberan el slot
- **Ticket digital** — código `#TT-xxxx` único + QR SVG (sin dependencia de GD/Imagick) descargable en PDF
- **Anti-race condition** — UPDATE atómico condicional en BD impide la doble reserva simultánea del mismo slot
- **Dashboard financiero** — el administrador ve todas las reservas con total de ingresos confirmados
- **Control de acceso por roles** — middleware `RoleMiddleware` protege todas las rutas; intento de acceso no autorizado redirige al dashboard con mensaje de error
- **Soft Deletes** en todas las tablas de negocio para trazabilidad completa

---

## Roles y accesos

| Rol | Puede hacer |
|---|---|
| `admin` | CRUD de canchas, tarifas y horarios · ver todas las reservas · confirmar pagos en efectivo · crear reservas manuales · dashboard financiero |
| `cliente` | Ver horarios disponibles · crear reservas · ver sus tickets · cancelar sus reservas |

> Un cliente que intenta acceder a `/canchas`, `/tarifas` o `/horarios` es redirigido automáticamente al dashboard con un banner de "Acceso denegado".

---

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 · PHP 8.5 (XAMPP) |
| Frontend | Blade · Tailwind CSS 3 (CDN) · Alpine.js 3 |
| Base de datos | MySQL vía XAMPP (`DB=top_tennis`, usuario `root`) |
| Auth | Laravel Breeze |
| PDF | `barryvdh/laravel-dompdf` |
| QR | `bacon/bacon-qr-code` con `SvgImageBackEnd` (sin GD/Imagick) |

---

## Instalación rápida (Windows + XAMPP)

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/top-tennis.git
cd top-tennis

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate
# Editar .env: DB_DATABASE=top_tennis, DB_USERNAME=root, DB_PASSWORD=
```

**Opción A — Script interactivo (recomendado en Windows):**
```
migrate.bat       ← ejecuta migraciones o migrate:fresh --seed
run-app.bat       ← levanta el servidor y abre el navegador
```

**Opción B — Manual:**
```bash
php artisan migrate --seed   # crea tablas y carga datos de prueba
php artisan serve            # levanta en http://127.0.0.1:8000
```

> **Scheduler (auto-liberación de no-shows):** para activar la regla de 30 min en segundo plano, ejecutar `php artisan schedule:work` en otra terminal. Sin él, el modo *lazy* del controller aplica la regla igual al listar horarios.

---

## Usuarios de prueba (seeder)

| Email | Contraseña | Rol |
|---|---|---|
| admin@toptennis.com | password | Administrador |
| cliente@toptennis.com | password | Cliente |

El seeder también crea 4 canchas (una en mantenimiento), 2 tarifas (día y noche), horarios de 7 días (06:00–22:00) en las canchas operativas y 2 reservas de ejemplo (1 Yape aprobada, 1 Efectivo pendiente).

---

## Diagrama Entidad-Relación

```mermaid
erDiagram
    users {
        bigint      id               PK
        string      name
        string      email            UK
        string      password
        string      rol
        string      emoji_perfil
        string      telefono
        timestamp   created_at
        timestamp   updated_at
    }

    canchas {
        bigint      id                    PK
        string      nombre                UK
        string      tipo_superficie
        enum        estado_mantenimiento
        timestamp   deleted_at
        timestamp   created_at
        timestamp   updated_at
    }

    tarifas {
        bigint      id             PK
        string      nombre_tarifa
        decimal     precio
        timestamp   deleted_at
        timestamp   created_at
        timestamp   updated_at
    }

    horarios {
        bigint      id           PK
        bigint      cancha_id    FK
        bigint      tarifa_id    FK
        datetime    hora_inicio
        datetime    hora_fin
        enum        estado
        timestamp   deleted_at
        timestamp   created_at
        timestamp   updated_at
    }

    reservas {
        bigint      id                  PK
        bigint      user_id             FK
        bigint      horario_id          FK
        enum        metodo_pago
        string      numero_operacion    "UK nullable"
        enum        estado_pago
        decimal     monto_pagado
        string      codigo_validacion   UK
        datetime    expira_at           "nullable"
        timestamp   deleted_at
        timestamp   created_at
        timestamp   updated_at
    }

    canchas  ||--o{ horarios  : "tiene slots"
    tarifas  ||--o{ horarios  : "aplica precio a"
    horarios ||--o| reservas  : "es reservado en"
    users    ||--o{ reservas  : "realiza"
```

Ver [docs/modelo-de-datos.md](docs/modelo-de-datos.md) para el diagrama completo con constraints, relaciones Eloquent y flujo de estados.

---

## Estructura del proyecto

```
app/
├── Console/Commands/
│   └── LiberarReservasVencidas.php   # Artisan: reservas:liberar-vencidas
├── Enums/
│   └── Rol.php                       # Enum: admin | cliente
├── Http/
│   ├── Controllers/
│   │   ├── CanchaController.php      # CRUD admin (canchas)
│   │   ├── TarifaController.php      # CRUD admin (tarifas)
│   │   ├── HorarioController.php     # CRUD admin (slots)
│   │   └── ReservaController.php     # Flujo cliente + ticket + PDF + dashboard
│   ├── Middleware/
│   │   └── RoleMiddleware.php        # Guardian de rutas por rol
│   └── Requests/
│       ├── StoreReservaRequest.php   # Validación de reserva + regla 30 min Efectivo
│       ├── StoreHorarioRequest.php   # Anti-cruce de horarios
│       └── ...
├── Models/
│   ├── Cancha.php
│   ├── Tarifa.php
│   ├── Horario.php                   # scopeReservables()
│   └── Reserva.php                   # scopeVencidas() · liberarVencidas() · qrSvg()
database/
├── migrations/                       # 16 migraciones ordenadas
└── seeders/
    └── DatabaseSeeder.php            # 2 usuarios · 4 canchas · 2 tarifas · slots de 7 días · 2 reservas
resources/views/
├── reservas/
│   ├── disponibles.blade.php
│   ├── confirmar.blade.php           # QR Yape + formulario de pago
│   ├── index.blade.php               # Dashboard financiero (staff) / Mis reservas (cliente)
│   ├── ticket.blade.php              # Ticket digital con QR SVG
│   └── ticket-pdf.blade.php          # Versión PDF (dompdf)
├── canchas/ tarifas/ horarios/       # Vistas CRUD admin
└── errors/                           # 403 · 404 · 419 · 429 · 500 personalizados
routes/
├── web.php                           # Rutas protegidas por middleware role:admin
└── console.php                       # Schedule: reservas:liberar-vencidas cada minuto
```

---

## Seguridad de rutas

| Ruta | Middleware | Quién accede |
|---|---|---|
| `/canchas/*`, `/tarifas/*`, `/horarios/*` | `auth + role:admin` | Solo Admin |
| `/reservas/crear-manual`, `/reservas/manual`, `/reservas/{id}/confirmar-pago` | `auth + role:admin` | Solo Admin |
| `/reservar`, `/reservas/*` | `auth` | Cualquier usuario autenticado |
| Ticket/cancelar ajeno | `auth` + `autorizar()` en controller | Dueño o admin |

---

## Desarrolladores

Proyecto desarrollado por **Renzo León** y **Diego Magallanes**  
**Dienzo INC** — Software Development

---

## Licencia

Proyecto privado — Top Tennis Club.
