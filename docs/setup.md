# Levantar el proyecto en local

## Requisitos

- PHP 8.1+
- Composer
- Node.js (para Vite/npm)
- MySQL (u otro motor compatible con Laravel; `.env.example` trae MySQL por defecto)

## Pasos

```bash
composer install
cp .env.example .env
php artisan key:generate

npm install
```

Configura en `.env`:

- Credenciales de base de datos (`DB_*`).
- `RESEND_API_KEY`, `RESEND_FROM_EMAIL`, `RESEND_FROM_NAME`, `RESEND_TO_EMAIL` — necesarias para que el envío de correos de nuevas cotizaciones (ver [booking-flow.md](booking-flow.md)) funcione. Sin `RESEND_API_KEY` la cotización se guarda igual, pero el correo no se envía (se registra un error en el log).
- `VITE_GOOGLE_MAPS_API_KEY` — clave de Google Maps/Places usada por el frontend para el autocompletado de direcciones (requerida para que `Booking.vue` funcione). Está vacía por defecto en `.env.example`; complétala con tu propia clave.

Base de datos:

```bash
php artisan migrate
php artisan db:seed --class=AdminUserSeeder   # crea admin@admin.com / Golden2025@
```

## Correr en desarrollo

```bash
php artisan serve      # backend en http://localhost:8000
npm run dev            # Vite con HMR
```

## Build de producción

```bash
npm run build
```

## Tests y estilo de código

```bash
php artisan test                       # toda la suite
php artisan test --filter=NombreTest   # un test o clase puntual
vendor/bin/pint                        # aplica el estilo de código (Laravel Pint)
vendor/bin/pint --test                 # solo verifica, sin modificar archivos
```
