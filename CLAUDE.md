# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Golden Street Moving is a Laravel 10 marketing/landing site with a moving-quote booking flow and a small admin dashboard. Backend is Laravel + Blade; interactive pieces are Vue 3 single-file components mounted individually into Blade pages (not a full SPA). Styling is SCSS + Tailwind; jQuery/Slick/AOS/Headroom power the marketing page's carousels and scroll effects.

## Commands

```bash
# PHP dependencies / app
composer install
php artisan serve
php artisan migrate
php artisan db:seed --class=AdminUserSeeder   # creates admin@admin.com / Golden2025@

# JS dependencies / assets (Vite)
npm install
npm run dev      # dev server with HMR
npm run build    # production build

# Tests (PHPUnit / Laravel's test runner)
php artisan test
php artisan test --filter=TestClassName
php artisan test tests/Feature/Auth/AuthenticationTest.php
vendor/bin/phpunit --filter=test_method_name

# Code style
vendor/bin/pint          # Laravel Pint, fixes PHP style
vendor/bin/pint --test   # check only, no changes
```

There is no JS test runner or linter configured in `package.json`.

## Architecture

### Vue mounting pattern
Vue is not bootstrapped as an SPA. `resources/js/app.js` keeps a manual registry of lazy-loaded components:
```js
const components = {
  ExampleComponent: () => import('./components/ExampleComponent.vue'),
  Booking: () => import('./components/Booking.vue'),
  BookingTable: () => import('./components/BookingTable.vue'),
}
```
On `DOMContentLoaded`, every DOM element with a `data-vue="ComponentName"` attribute gets its own `createApp(...).mount(el)`, with props parsed from a `data-props` JSON attribute. To add a new interactive widget: create the `.vue` file, register it in this map, and render `<div data-vue="Name" data-props="{{ json_encode($props) }}"></div>` from the Blade view. `resources/views/components/section-booking.blade.php` and `resources/views/dashboard.blade.php` are the existing examples of this pattern.

### Google Maps loading
`resources/js/lib/google.js` wraps `@googlemaps/js-api-loader` in a singleton: `initGoogle(key)` (called once from `app.js` with `import.meta.env.VITE_GOOGLE_MAPS_API_KEY`) configures the loader, and `loadPlaces()` / `loadMaps()` memoize the async library imports so multiple components (e.g. `AddressAutocomplete.vue`, `Booking.vue`) can request the same library without double-loading it. `VITE_GOOGLE_MAPS_API_KEY` must be set in `.env` for the Vite build (it is not currently listed in `.env.example`).

### Moving-quote booking flow
1. `Booking.vue` (public quote form, embedded via `section-booking.blade.php`) collects contact info, origin/destination addresses (via Google Places autocomplete, sent as nested `org_address` / `end_address` objects with `formatted` + `raw.location.{lat,lng}`), move details, and posts to `POST /api/moving-quotes`.
2. `MovingQuoteController::store` (`app/Http/Controllers/MovingQuoteController.php`) validates the nested payload, flattens it into the `moving_quotes` table columns, creates a `MovingQuote`, and calls `ResendService::sendLead($quote)`.
3. `ResendService` (`app/Services/ResendService.php`) renders `resources/views/emails/moving_lead.blade.php` and sends it via the Resend API (`config('services.resend.*')`, backed by `RESEND_API_KEY`/`RESEND_FROM_EMAIL`/`RESEND_FROM_NAME`/`RESEND_TO_EMAIL` in `.env`). A prior SendGrid-based implementation was removed in favor of Resend.
4. `DashboardController::index` (behind `auth`+`verified` middleware, route `/dashboard`) loads all `MovingQuote` records for `BookingTable.vue`, the admin table with a status-editing modal that calls `PUT /api/moving-quotes/{movingQuote}` (`MovingQuoteController::update`) to move a lead through `pending → in_review → quoted → closed/cancelled`.

Note: the `/api/moving-quotes` routes are registered without `auth` middleware in `routes/api.php`; access control for the dashboard UI itself is enforced only at the `/dashboard` route.

### Auth & roles
Standard Laravel Breeze auth (`routes/auth.php`, `app/Http/Controllers/Auth/*`). A `role` column (default `user`) was added to `users` via a later migration (`database/migrations/2025_11_10_061803_add_role_to_users_table.php`); `AdminUserSeeder` seeds a single `role => admin` account. There is currently no middleware/policy gating by role — anything role-specific would need to be added.

### Deployment helper route
`routes/web.php` defines `GET /run-maintenance`, a key-gated endpoint that runs `migrate --force`, `db:seed --force`, and `key:generate --force` via `Artisan::call`, then clears config/cache. This exists to support deploys on hosting without shell/SSH access — treat it as a deploy hook, not a normal application route.

### Frontend styling
`resources/css/app.scss` composes partials under `resources/css/base/` (reset, variables, mixins, media-query helpers, typography) and `resources/css/components/` (one file per marketing section: hero, header, footer, banner, cards, buttons, booking, sections). Tailwind (`tailwind.config.js`) is layered on top for utility classes, primarily used by the Breeze-generated auth/profile views.
