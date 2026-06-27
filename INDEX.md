# TOP TENNIS - Índice de Código

> Guía rápida para ubicar cualquier archivo del proyecto por módulo.
> Haz click en cualquier ruta para ir directo al archivo.

---

## Estructura General

```
Top-tennis.2/
├── app/                  → Lógica del backend (Controllers, Models, Requests, Policies, Enums)
├── resources/views/      → Vistas Blade (frontend)
├── routes/               → Definición de rutas
├── database/             → Migraciones, factories, seeders
├── config/               → Configuración de Laravel
├── public/               → Assets públicos (imágenes, favicon)
└── tests/                → Tests automatizados
```

---

## 🎾 CANCHAS

> Gestión de canchas de tennis: crear, editar, eliminar, mantenimiento y restauración.

| Archivo | Ubicación | Función |
|---------|-----------|---------|
| [CanchaController.php](app/Http/Controllers/CanchaController.php) | `app/Http/Controllers/` | Controlador principal: CRUD de canchas, poner en mantenimiento, restaurar. Incluye lógica de reembolso automático cuando una cancha entra en mantenimiento |
| [Cancha.php (Model)](app/Models/Cancha.php) | `app/Models/` | Modelo Eloquent. Define campos (nombre, tipo_superficie, modalidad, iluminación, estado_mantenimiento), relaciones con Horario, constantes de superficies/modalidades, método `restaurarVencidas()` |
| [StoreCanchaRequest.php](app/Http/Requests/StoreCanchaRequest.php) | `app/Http/Requests/` | Validación de datos al CREAR una cancha |
| [UpdateCanchaRequest.php](app/Http/Requests/UpdateCanchaRequest.php) | `app/Http/Requests/` | Validación de datos al EDITAR una cancha |
| [CanchaPolicy.php](app/Policies/CanchaPolicy.php) | `app/Policies/` | Políticas de autorización: quién puede gestionar canchas |
| [CanchaFactory.php](database/factories/CanchaFactory.php) | `database/factories/` | Factory para generar canchas de prueba (testing/seeders) |
| [create_canchas_table.php](database/migrations/2026_06_20_154029_create_canchas_table.php) | `database/migrations/` | Migración: crea la tabla `canchas` |
| [add_imagen_to_canchas.php](database/migrations/2026_06_27_025448_add_imagen_to_canchas_table.php) | `database/migrations/` | Migración: agrega campo `imagen` a canchas |
| [add_fields_to_canchas.php](database/migrations/2026_06_27_100000_add_fields_to_canchas_table.php) | `database/migrations/` | Migración: agrega campos extras (modalidad, iluminación, estado_mantenimiento) |
| [add_inicio_mantenimiento.php](database/migrations/2026_06_27_102754_add_inicio_mantenimiento_to_canchas_table.php) | `database/migrations/` | Migración: agrega campo `inicio_mantenimiento` |
| [rename_sintetica.php](database/migrations/2026_06_27_110000_rename_sintetica_to_cesped_artificial.php) | `database/migrations/` | Migración: renombra valor de superficie |
| [index.blade.php](resources/views/canchas/index.blade.php) | `resources/views/canchas/` | Vista: listado de todas las canchas con acciones |
| [create.blade.php](resources/views/canchas/create.blade.php) | `resources/views/canchas/` | Vista: formulario para crear cancha nueva |
| [edit.blade.php](resources/views/canchas/edit.blade.php) | `resources/views/canchas/` | Vista: formulario para editar cancha existente |
| [_form.blade.php](resources/views/canchas/_form.blade.php) | `resources/views/canchas/` | Partial: formulario reutilizable (usado en create y edit) |

### Rutas de Canchas (solo Admin)
| Método | URL | Acción |
|--------|-----|--------|
| GET | `/canchas` | Listar canchas |
| GET | `/canchas/create` | Formulario crear |
| POST | `/canchas` | Guardar nueva |
| GET | `/canchas/{id}/edit` | Formulario editar |
| PATCH | `/canchas/{id}` | Actualizar |
| DELETE | `/canchas/{id}` | Eliminar |
| POST | `/canchas/{id}/mantenimiento` | Poner en mantenimiento |
| POST | `/canchas/{id}/restaurar` | Restaurar a operativa |

---

## 💰 TARIFAS

> Precios que se asignan a los horarios. Cada horario tiene una tarifa asociada.

| Archivo | Ubicación | Función |
|---------|-----------|---------|
| [TarifaController.php](app/Http/Controllers/TarifaController.php) | `app/Http/Controllers/` | Controlador CRUD completo. Impide eliminar tarifas con horarios asociados |
| [Tarifa.php (Model)](app/Models/Tarifa.php) | `app/Models/` | Modelo: campos `nombre_tarifa` y `precio` (decimal). Relación hasMany con Horario |
| [StoreTarifaRequest.php](app/Http/Requests/StoreTarifaRequest.php) | `app/Http/Requests/` | Validación al crear tarifa |
| [UpdateTarifaRequest.php](app/Http/Requests/UpdateTarifaRequest.php) | `app/Http/Requests/` | Validación al editar tarifa |
| [TarifaPolicy.php](app/Policies/TarifaPolicy.php) | `app/Policies/` | Políticas de autorización |
| [TarifaFactory.php](database/factories/TarifaFactory.php) | `database/factories/` | Factory para testing |
| [create_tarifas_table.php](database/migrations/2026_06_20_154037_create_tarifas_table.php) | `database/migrations/` | Migración: crea tabla `tarifas` |
| [index.blade.php](resources/views/tarifas/index.blade.php) | `resources/views/tarifas/` | Vista: listado de tarifas |
| [create.blade.php](resources/views/tarifas/create.blade.php) | `resources/views/tarifas/` | Vista: formulario crear tarifa |
| [edit.blade.php](resources/views/tarifas/edit.blade.php) | `resources/views/tarifas/` | Vista: formulario editar tarifa |
| [_form.blade.php](resources/views/tarifas/_form.blade.php) | `resources/views/tarifas/` | Partial: formulario reutilizable |

### Rutas de Tarifas (solo Admin)
| Método | URL | Acción |
|--------|-----|--------|
| GET | `/tarifas` | Listar tarifas |
| GET | `/tarifas/create` | Formulario crear |
| POST | `/tarifas` | Guardar nueva |
| GET | `/tarifas/{id}/edit` | Formulario editar |
| PATCH | `/tarifas/{id}` | Actualizar |
| DELETE | `/tarifas/{id}` | Eliminar |

---

## 🕐 HORARIOS

> Slots de tiempo asignados a una cancha con una tarifa. Se generan en bloque (rango de fechas y horas).

| Archivo | Ubicación | Función |
|---------|-----------|---------|
| [HorarioController.php](app/Http/Controllers/HorarioController.php) | `app/Http/Controllers/` | Controlador: listado por fecha/cancha, generación masiva de slots (rango de fechas × rango de horas × canchas), edición individual, eliminación por día completo |
| [Horario.php (Model)](app/Models/Horario.php) | `app/Models/` | Modelo: campos cancha_id, tarifa_id, hora_inicio, hora_fin, estado. Scope `reservables()` filtra slots disponibles, futuros y de canchas operativas |
| [StoreHorarioRequest.php](app/Http/Requests/StoreHorarioRequest.php) | `app/Http/Requests/` | Validación al crear horario |
| [UpdateHorarioRequest.php](app/Http/Requests/UpdateHorarioRequest.php) | `app/Http/Requests/` | Validación al editar horario |
| [HorarioPolicy.php](app/Policies/HorarioPolicy.php) | `app/Policies/` | Políticas de autorización |
| [HorarioFactory.php](database/factories/HorarioFactory.php) | `database/factories/` | Factory para testing |
| [create_horarios_table.php](database/migrations/2026_06_23_000001_create_horarios_table.php) | `database/migrations/` | Migración: crea tabla `horarios` |
| [index.blade.php](resources/views/horarios/index.blade.php) | `resources/views/horarios/` | Vista: calendario de horarios con filtros por fecha y cancha |
| [create.blade.php](resources/views/horarios/create.blade.php) | `resources/views/horarios/` | Vista: formulario de generación masiva de slots |
| [edit.blade.php](resources/views/horarios/edit.blade.php) | `resources/views/horarios/` | Vista: editar horario individual |
| [_form.blade.php](resources/views/horarios/_form.blade.php) | `resources/views/horarios/` | Partial: formulario reutilizable |

### Rutas de Horarios (solo Admin)
| Método | URL | Acción |
|--------|-----|--------|
| GET | `/horarios` | Listar por fecha (query: `?fecha=YYYY-MM-DD&cancha=ID`) |
| GET | `/horarios/create` | Formulario generación masiva |
| POST | `/horarios` | Generar slots |
| GET | `/horarios/{id}/edit` | Editar slot individual |
| PATCH | `/horarios/{id}` | Actualizar slot |
| DELETE | `/horarios/{id}` | Eliminar slot |
| DELETE | `/horarios-dia` | Eliminar todos los slots de un día |

---

## 📋 RESERVAS

> Flujo completo: cliente ve horarios disponibles → confirma → paga (Yape o Efectivo) → recibe ticket con QR.
> Admin/Recepción pueden crear reservas manuales, confirmar pagos en efectivo y ver historial completo.

| Archivo | Ubicación | Función |
|---------|-----------|---------|
| [ReservaController.php](app/Http/Controllers/ReservaController.php) | `app/Http/Controllers/` | Controlador principal. Métodos: `disponibles()` lista slots reservables, `confirmar()` muestra formulario de pago, `store()` procesa reserva con anti-race condition atómico, `ticket()` muestra ticket digital, `descargarTicket()` genera PDF, `cancelar()` libera horario, `confirmarPago()` aprueba pago en efectivo, `crearManual()`/`storeManual()` reserva manual por staff |
| [Reserva.php (Model)](app/Models/Reserva.php) | `app/Models/` | Modelo: campos user_id, horario_id, metodo_pago, numero_operacion, estado_pago, monto_pagado, codigo_validacion, expira_at. Métodos: `liberarVencidas()` anula no-shows, `generarCodigoValidacion()` genera código TT-XXXX, `qrSvg()` genera QR con logo, `contenidoQr()` texto del QR |
| [StoreReservaRequest.php](app/Http/Requests/StoreReservaRequest.php) | `app/Http/Requests/` | Validación al crear reserva (metodo_pago, numero_operacion si es Yape) |
| [ReservaFactory.php](database/factories/ReservaFactory.php) | `database/factories/` | Factory para testing |
| [create_reservas_table.php](database/migrations/2026_06_26_221313_create_reservas_table.php) | `database/migrations/` | Migración: crea tabla `reservas` |
| [add_monto_pagado.php](database/migrations/2026_06_27_022127_add_monto_pagado_to_reservas_table.php) | `database/migrations/` | Migración: agrega `monto_pagado` (snapshot del precio) |
| [drop_unique_horario.php](database/migrations/2026_06_27_030434_drop_unique_horario_id_from_reservas_table.php) | `database/migrations/` | Migración: quita constraint unique de horario_id |
| [update_estado_pago_enum.php](database/migrations/2026_06_27_100001_update_estado_pago_enum_in_reservas_table.php) | `database/migrations/` | Migración: actualiza enum de estado_pago |
| [LiberarReservasVencidas.php](app/Console/Commands/LiberarReservasVencidas.php) | `app/Console/Commands/` | Comando artisan `reservas:liberar-vencidas`: anula reservas en Efectivo cuyo plazo de pago expiró y libera el horario |
| [disponibles.blade.php](resources/views/reservas/disponibles.blade.php) | `resources/views/reservas/` | Vista: catálogo de horarios disponibles para reservar |
| [confirmar.blade.php](resources/views/reservas/confirmar.blade.php) | `resources/views/reservas/` | Vista: formulario de confirmación y pago (Yape/Efectivo) |
| [index.blade.php](resources/views/reservas/index.blade.php) | `resources/views/reservas/` | Vista: historial de reservas (admin ve todas, cliente ve las suyas) |
| [ticket.blade.php](resources/views/reservas/ticket.blade.php) | `resources/views/reservas/` | Vista: ticket digital con código QR |
| [ticket-pdf.blade.php](resources/views/reservas/ticket-pdf.blade.php) | `resources/views/reservas/` | Vista: versión PDF del ticket para descarga |
| [crear-manual.blade.php](resources/views/reservas/crear-manual.blade.php) | `resources/views/reservas/` | Vista: formulario para que admin/recepción cree reserva manual |

### Rutas de Reservas
| Método | URL | Acceso | Acción |
|--------|-----|--------|--------|
| GET | `/reservar` | Auth | Ver horarios disponibles |
| GET | `/reservas` | Auth | Historial de reservas |
| GET | `/reservar/{horario}/confirmar` | Auth | Formulario de pago |
| POST | `/reservas` | Auth | Procesar reserva |
| GET | `/reservas/crear-manual` | Admin/Recepción | Formulario reserva manual |
| POST | `/reservas/manual` | Admin/Recepción | Guardar reserva manual |
| GET | `/reservas/{id}/ticket` | Auth (dueño/staff) | Ver ticket digital |
| GET | `/reservas/{id}/ticket/pdf` | Auth (dueño/staff) | Descargar ticket PDF |
| DELETE | `/reservas/{id}` | Auth (dueño/staff) | Cancelar reserva |
| PATCH | `/reservas/{id}/confirmar-pago` | Admin/Recepción | Confirmar pago en efectivo |

### Lógica de negocio importante
- **Anti-race condition**: `store()` usa UPDATE atómico condicional para evitar doble reserva
- **Monto congelado**: `monto_pagado` guarda el precio al momento de reservar (no cambia si la tarifa se modifica después)
- **No-shows**: reservas en Efectivo tienen `expira_at` = hora_inicio - 30 min. Si no se pagan, se anulan automáticamente
- **Código de validación**: formato `TT-XXXX` único por reserva
- **QR**: genera SVG con logo de pelota de tennis en el centro

---

## 👤 USERS (Usuarios)

> Sistema de usuarios con 3 roles: Admin, Recepcionista, Cliente.

| Archivo | Ubicación | Función |
|---------|-----------|---------|
| [User.php (Model)](app/Models/User.php) | `app/Models/` | Modelo: campos name, email, telefono, password, rol (enum), emoji_perfil. Relación hasMany con Reserva |
| [Rol.php (Enum)](app/enums/Rol.php) | `app/enums/` | Enum PHP: `Admin`, `Recepcionista`, `Cliente`. Define los 3 roles del sistema |
| [RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php) | `app/Http/Middleware/` | Middleware `role:admin,recepcionista`: verifica que el usuario tenga el rol requerido para acceder a la ruta |
| [ProfileController.php](app/Http/Controllers/ProfileController.php) | `app/Http/Controllers/` | Controlador de perfil: editar nombre/email, cambiar contraseña, eliminar cuenta |
| [ProfileUpdateRequest.php](app/Http/Requests/ProfileUpdateRequest.php) | `app/Http/Requests/` | Validación al actualizar perfil |
| [UserFactory.php](database/factories/UserFactory.php) | `database/factories/` | Factory para generar usuarios de prueba |
| [create_users_table.php](database/migrations/0001_01_01_000000_create_users_table.php) | `database/migrations/` | Migración: crea tabla `users` |
| [add_emoji_perfil.php](database/migrations/2026_06_24_054931_add_emoji_perfil_to_users_table.php) | `database/migrations/` | Migración: agrega campo `emoji_perfil` |
| [add_telefono.php](database/migrations/2026_06_26_211459_add_telefono_to_users_table.php) | `database/migrations/` | Migración: agrega campo `telefono` |
| [edit.blade.php](resources/views/profile/edit.blade.php) | `resources/views/profile/` | Vista: página de edición de perfil |
| [update-profile-information-form.blade.php](resources/views/profile/partials/update-profile-information-form.blade.php) | `resources/views/profile/partials/` | Partial: formulario de datos personales |
| [update-password-form.blade.php](resources/views/profile/partials/update-password-form.blade.php) | `resources/views/profile/partials/` | Partial: formulario de cambio de contraseña |
| [delete-user-form.blade.php](resources/views/profile/partials/delete-user-form.blade.php) | `resources/views/profile/partials/` | Partial: formulario para eliminar cuenta |

### Roles y permisos
| Rol | Puede hacer |
|-----|-------------|
| **Admin** | Todo: CRUD canchas, tarifas, horarios, ver todas las reservas, confirmar pagos, crear reservas manuales |
| **Recepcionista** | Ver reservas, confirmar pagos en efectivo, crear reservas manuales |
| **Cliente** | Reservar canchas, ver sus propias reservas, descargar tickets |

---

## 🔐 AUTH (Autenticación)

> Login, registro, recuperación de contraseña, verificación de email (Laravel Breeze).

| Archivo | Ubicación | Función |
|---------|-----------|---------|
| [AuthenticatedSessionController.php](app/Http/Controllers/Auth/AuthenticatedSessionController.php) | `app/Http/Controllers/Auth/` | Login y logout |
| [RegisteredUserController.php](app/Http/Controllers/Auth/RegisteredUserController.php) | `app/Http/Controllers/Auth/` | Registro de nuevos usuarios |
| [PasswordResetLinkController.php](app/Http/Controllers/Auth/PasswordResetLinkController.php) | `app/Http/Controllers/Auth/` | Enviar enlace de recuperación de contraseña |
| [NewPasswordController.php](app/Http/Controllers/Auth/NewPasswordController.php) | `app/Http/Controllers/Auth/` | Establecer nueva contraseña |
| [PasswordController.php](app/Http/Controllers/Auth/PasswordController.php) | `app/Http/Controllers/Auth/` | Cambiar contraseña actual |
| [ConfirmablePasswordController.php](app/Http/Controllers/Auth/ConfirmablePasswordController.php) | `app/Http/Controllers/Auth/` | Confirmar contraseña para acciones sensibles |
| [EmailVerificationPromptController.php](app/Http/Controllers/Auth/EmailVerificationPromptController.php) | `app/Http/Controllers/Auth/` | Pantalla de verificación de email |
| [EmailVerificationNotificationController.php](app/Http/Controllers/Auth/EmailVerificationNotificationController.php) | `app/Http/Controllers/Auth/` | Reenviar email de verificación |
| [VerifyEmailController.php](app/Http/Controllers/Auth/VerifyEmailController.php) | `app/Http/Controllers/Auth/` | Procesar link de verificación |
| [LoginRequest.php](app/Http/Requests/Auth/LoginRequest.php) | `app/Http/Requests/Auth/` | Validación y rate-limiting del login |
| [auth.php (rutas)](routes/auth.php) | `routes/` | Define todas las rutas de autenticación |
| [login.blade.php](resources/views/auth/login.blade.php) | `resources/views/auth/` | Vista: formulario de login |
| [register.blade.php](resources/views/auth/register.blade.php) | `resources/views/auth/` | Vista: formulario de registro |
| [forgot-password.blade.php](resources/views/auth/forgot-password.blade.php) | `resources/views/auth/` | Vista: pedir recuperación de contraseña |
| [reset-password.blade.php](resources/views/auth/reset-password.blade.php) | `resources/views/auth/` | Vista: establecer nueva contraseña |
| [verify-email.blade.php](resources/views/auth/verify-email.blade.php) | `resources/views/auth/` | Vista: verificar email |
| [confirm-password.blade.php](resources/views/auth/confirm-password.blade.php) | `resources/views/auth/` | Vista: confirmar contraseña |

---

## 🗂️ RUTAS Y CONFIGURACIÓN

| Archivo | Ubicación | Función |
|---------|-----------|---------|
| [web.php](routes/web.php) | `routes/` | **Archivo principal de rutas**. Define TODAS las rutas web: dashboard, canchas, tarifas, horarios, reservas, perfil |
| [auth.php](routes/auth.php) | `routes/` | Rutas de autenticación (login, registro, etc.) |
| [console.php](routes/console.php) | `routes/` | Comandos de consola programados |
| [app.php (bootstrap)](bootstrap/app.php) | `bootstrap/` | Configuración de la aplicación Laravel |
| [providers.php](bootstrap/providers.php) | `bootstrap/` | Proveedores de servicios |
| [AppServiceProvider.php](app/Providers/AppServiceProvider.php) | `app/Providers/` | Proveedor de servicios principal |
| [DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) | `database/seeders/` | Seeder: datos iniciales de la BD |

---

## 🎨 LAYOUTS Y COMPONENTES (Frontend)

| Archivo | Ubicación | Función |
|---------|-----------|---------|
| [app.blade.php](resources/views/layouts/app.blade.php) | `resources/views/layouts/` | Layout principal: CSS global, estilos de cards/botones, estructura HTML base |
| [guest.blade.php](resources/views/layouts/guest.blade.php) | `resources/views/layouts/` | Layout para páginas sin autenticación (login, registro) |
| [navigation.blade.php](resources/views/layouts/navigation.blade.php) | `resources/views/layouts/` | Barra de navegación: links según rol, menú usuario, dropdown |
| [dashboard.blade.php](resources/views/dashboard.blade.php) | `resources/views/` | Dashboard: panel Admin (stats + accesos rápidos) y panel Cliente (disponibles + mis reservas) |
| [welcome.blade.php](resources/views/welcome.blade.php) | `resources/views/` | Página de bienvenida (landing) |
| [errores.blade.php](resources/views/partials/errores.blade.php) | `resources/views/partials/` | Partial: muestra errores de validación |
| [app.css](resources/css/app.css) | `resources/css/` | Estilos CSS personalizados |
| [app.js](resources/js/app.js) | `resources/js/` | JavaScript principal |

### Componentes Blade
| Archivo | Función |
|---------|---------|
| [application-logo.blade.php](resources/views/components/application-logo.blade.php) | Logo de la aplicación |
| [primary-button.blade.php](resources/views/components/primary-button.blade.php) | Botón primario verde |
| [secondary-button.blade.php](resources/views/components/secondary-button.blade.php) | Botón secundario |
| [danger-button.blade.php](resources/views/components/danger-button.blade.php) | Botón rojo (acciones peligrosas) |
| [text-input.blade.php](resources/views/components/text-input.blade.php) | Input de texto |
| [input-label.blade.php](resources/views/components/input-label.blade.php) | Label para inputs |
| [input-error.blade.php](resources/views/components/input-error.blade.php) | Mensaje de error de validación |
| [modal.blade.php](resources/views/components/modal.blade.php) | Modal reutilizable |
| [dropdown.blade.php](resources/views/components/dropdown.blade.php) | Menú dropdown |
| [dropdown-link.blade.php](resources/views/components/dropdown-link.blade.php) | Link dentro de dropdown |
| [nav-link.blade.php](resources/views/components/nav-link.blade.php) | Link de navegación |
| [responsive-nav-link.blade.php](resources/views/components/responsive-nav-link.blade.php) | Link de nav responsive |
| [auth-session-status.blade.php](resources/views/components/auth-session-status.blade.php) | Status de sesión |

---

## ⚠️ PÁGINAS DE ERROR

| Archivo | Función |
|---------|---------|
| [403.blade.php](resources/views/errors/403.blade.php) | Error 403: Sin permiso |
| [404.blade.php](resources/views/errors/404.blade.php) | Error 404: No encontrado |
| [419.blade.php](resources/views/errors/419.blade.php) | Error 419: Sesión expirada |
| [429.blade.php](resources/views/errors/429.blade.php) | Error 429: Demasiadas solicitudes |
| [500.blade.php](resources/views/errors/500.blade.php) | Error 500: Error del servidor |
| [layout.blade.php](resources/views/errors/layout.blade.php) | Layout base de páginas de error |

---

## 🧪 TESTS

| Archivo | Función |
|---------|---------|
| [AuthenticationTest.php](tests/Feature/Auth/AuthenticationTest.php) | Test de login/logout |
| [RegistrationTest.php](tests/Feature/Auth/RegistrationTest.php) | Test de registro |
| [PasswordResetTest.php](tests/Feature/Auth/PasswordResetTest.php) | Test de recuperar contraseña |
| [PasswordUpdateTest.php](tests/Feature/Auth/PasswordUpdateTest.php) | Test de cambiar contraseña |
| [PasswordConfirmationTest.php](tests/Feature/Auth/PasswordConfirmationTest.php) | Test de confirmar contraseña |
| [EmailVerificationTest.php](tests/Feature/Auth/EmailVerificationTest.php) | Test de verificación de email |
| [ProfileTest.php](tests/Feature/ProfileTest.php) | Test de edición de perfil |
| [ExampleTest.php](tests/Feature/ExampleTest.php) | Test ejemplo (feature) |
| [ExampleTest.php](tests/Unit/ExampleTest.php) | Test ejemplo (unit) |

---

## 📦 ARCHIVOS DE CONFIGURACIÓN

| Archivo | Función |
|---------|---------|
| [composer.json](composer.json) | Dependencias PHP (Laravel, DomPDF, BaconQrCode, etc.) |
| [package.json](package.json) | Dependencias JS (Vite, Tailwind, Alpine) |
| [vite.config.js](vite.config.js) | Configuración de Vite (bundler) |
| [tailwind.config.js](tailwind.config.js) | Configuración de Tailwind CSS |
| [postcss.config.js](postcss.config.js) | Configuración de PostCSS |
| [phpunit.xml](phpunit.xml) | Configuración de PHPUnit (tests) |
| [.env.example](.env.example) | Variables de entorno de ejemplo |
| [.gitignore](.gitignore) | Archivos excluidos de git |

---

## 🔄 RELACIONES ENTRE MODELOS

```
User (1) ──────────── (N) Reserva
                              │
Cancha (1) ── (N) Horario (1)─┘
                      │
Tarifa (1) ── (N) ────┘
```

- Un **User** tiene muchas **Reservas**
- Una **Cancha** tiene muchos **Horarios**
- Una **Tarifa** tiene muchos **Horarios**
- Un **Horario** tiene una **Reserva** (o ninguna)
- Una **Reserva** pertenece a un **User** y a un **Horario**

---

## 🚀 CÓMO EJECUTAR

```bash
# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate --seed

# Iniciar servidor
php artisan serve
npm run dev
```
