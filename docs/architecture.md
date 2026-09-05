# Arquitectura

## Resumen

Aplicación Laravel 10 tradicional (Blade + rutas server-side), no una SPA. El interactividad puntual (formulario de cotización, tabla del dashboard) se implementa como componentes Vue 3 montados individualmente sobre elementos concretos del DOM, coexistiendo con jQuery/Slick/AOS/Headroom para el resto de la página de marketing (carruseles, animaciones al hacer scroll, header que se oculta al bajar).

## Backend (Laravel)

- **Rutas**: `routes/web.php` (páginas, autenticación vía `routes/auth.php`, dashboard) y `routes/api.php` (endpoints JSON bajo el prefijo `/api`, registrados por `RouteServiceProvider`).
- **Controladores relevantes**:
  - `MovingQuoteController` — crea y actualiza cotizaciones (`app/Http/Controllers/MovingQuoteController.php`).
  - `DashboardController` — lista las cotizaciones para el panel admin (`app/Http/Controllers/DashboardController.php`).
  - Controladores `Auth/*` — autenticación estándar de Laravel Breeze.
- **Modelo**: `MovingQuote` (`app/Models/MovingQuote.php`), tabla `moving_quotes` (ver migración `database/migrations/2025_11_10_060603_create_moving_quotes_table.php`).
- **Servicios**: `ResendService` (`app/Services/ResendService.php`) envía el correo de notificación de nueva cotización usando la API de [Resend](https://resend.com). Reemplaza una integración anterior con SendGrid.
- **Roles**: la tabla `users` tiene una columna `role` (por defecto `user`), agregada en `database/migrations/2025_11_10_061803_add_role_to_users_table.php`. `AdminUserSeeder` crea una cuenta `role => admin`. Actualmente no hay middleware/policy que filtre por rol; solo existe la columna y el seeder.
- **Ruta de mantenimiento**: `GET /run-maintenance` en `routes/web.php` ejecuta `migrate`, `db:seed` y `key:generate` vía `Artisan::call`, protegida por un parámetro `?key=` fijo en el código. Sirve como hook de despliegue en hosting sin acceso SSH — no es una ruta de la aplicación en sí.

## Frontend

### Vistas Blade

- `resources/views/welcome.blade.php` — landing page, compuesta de componentes Blade en `resources/views/components/section-*.blade.php` (hero, servicios, beneficios, reseñas, booking, FAQ).
- `resources/views/dashboard.blade.php` — panel admin, protegido por `auth`+`verified`.
- `resources/views/layouts/` — layouts base (`app`, `guest`, `default`) y navegación.
- `resources/views/emails/moving_lead.blade.php` — plantilla del correo enviado por `ResendService`.

### Componentes Vue

No hay un bundle SPA único: `resources/js/app.js` mantiene un registro manual de componentes cargados de forma perezosa:

```js
const components = {
  ExampleComponent: () => import('./components/ExampleComponent.vue'),
  Booking: () => import('./components/Booking.vue'),
  BookingTable: () => import('./components/BookingTable.vue'),
}
```

Al cargar el DOM, cualquier elemento con `data-vue="NombreComponente"` se monta como una instancia Vue independiente, recibiendo props desde el atributo `data-props` (JSON). Ejemplo real en `resources/views/components/section-booking.blade.php`:

```blade
<div data-vue="Booking" data-props='@json(["wp_action" => $wp_action])'></div>
```

Componentes existentes en `resources/js/components/`:

- `Booking.vue` — formulario público de cotización (ver [booking-flow.md](booking-flow.md)).
- `AddressAutocomplete.vue` — input de dirección con autocompletado de Google Places, usado dentro de `Booking.vue`.
- `BookingTable.vue` — tabla del dashboard admin para ver y cambiar el estado de cada cotización.
- `ExampleComponent.vue` — componente de ejemplo del scaffold de Breeze/Vite, sin uso funcional.

### Google Maps

`resources/js/lib/google.js` envuelve `@googlemaps/js-api-loader` en un singleton para evitar cargar la librería más de una vez:

- `initGoogle(apiKey)` — se llama una sola vez desde `app.js` con `import.meta.env.VITE_GOOGLE_MAPS_API_KEY`.
- `loadPlaces()` / `loadMaps()` — devuelven promesas memoizadas para cargar las librerías `places`/`maps` bajo demanda desde cualquier componente.

### Estilos

`resources/css/app.scss` importa parciales de `resources/css/base/` (reset, variables, mixins, media queries, tipografía) y `resources/css/components/` (un archivo por sección de marketing: hero, header, footer, banner, cards, buttons, booking, sections). Tailwind (`tailwind.config.js`) se usa principalmente en las vistas de autenticación/perfil generadas por Breeze.

## Build

`vite.config.js` usa `laravel-vite-plugin` y `@vitejs/plugin-vue`, con entradas `resources/css/app.scss` y `resources/js/app.js`.
