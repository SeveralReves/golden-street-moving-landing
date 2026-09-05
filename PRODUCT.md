# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Primary: local Atlanta-area (Georgia) households and small businesses arranging a residential or corporate move. Their job is to get a fast, trustworthy moving quote and lock in a move date without back-and-forth. Out-of-state/long-distance moves are a real, secondary offering (see the "Out-of-state transfers" banner), not the primary focus.

## Product Purpose

A marketing site with an embedded booking flow for Golden Street Moving, a moving company. It exists to convert visitors into qualified leads through a quote-request form, and to give the internal team a dashboard to track each lead from first contact through completion. Success is a lead that moves cleanly through the pipeline (pending → in_review → quoted → closed) without losing any detail captured at submission.

## Positioning

Care & reliability — fully insured moves, on-time service, and a trained, professional crew that handles belongings carefully. This is the claim the business leads with today (see the benefits section) and the one it wants to own, rather than competing on price or speed.

## Operating Context

- Public marketing page collects leads via `Booking.vue` (embedded through `section-booking.blade.php`), posting to `POST /api/moving-quotes`.
- `MovingQuoteController::store` validates and saves the lead, then `ResendService` emails a notification to the operations inbox.
- Internal admin dashboard (`/dashboard`, behind `auth`+`verified`) shows all leads in `BookingTable.vue`; staff move each lead through `pending → in_review → quoted → closed/cancelled` via a status modal.
- Contact channels advertised on-site: phone/WhatsApp +1 (770) 589-9512, email infogoldenstreets@gmail.com, Instagram @goldenstreets_moving.
- `GET /run-maintenance` is a key-gated deploy hook (runs migrations/seeders on hosting without shell access) — not a user-facing route.

## Capabilities and Constraints

- Services offered: residential moving, corporate/office moving, packing/unpacking, and out-of-state/long-distance transfers.
- Booking flow depends on Google Places autocomplete for origin/destination addresses (requires `VITE_GOOGLE_MAPS_API_KEY`).
- No role-based access control beyond a `role` column on `users` (default `user`, one seeded `admin`); nothing is currently gated by role beyond the blanket `auth`+`verified` middleware on the dashboard.
- No JS test runner or linter is configured; PHP tests run via `php artisan test` / `vendor/bin/phpunit`.

## Brand Commitments

- Name: Golden Street Moving (rendered as "Golden Street Moving Company" in legal copy and review schema).
- Contact: phone/WhatsApp +1 (770) 589-9512, email infogoldenstreets@gmail.com, Instagram @goldenstreets_moving.
- Existing voice: warm and reassuring, emphasizing a stress-free, carefully handled move ("Your move is easy, safe, and on time").

## Evidence on Hand

- Real customer reviews/testimonials exist and should replace the current placeholders once supplied — `section-reviews.blade.php` and `welcome.blade.php` currently ship fake names ("John Doe", "Jane Smith", etc.) and fake Google review links purely as layout filler. Future work must not present these as real and must not invent new testimonials, ratings, or review counts.
- `resources/views/legal.blade.php` states real operating policies (24-hour cancellation window, prohibited items list, accepted payment methods) usable as factual constraints elsewhere on the site.
- No DOT/MC license number, founding year, or named insurance provider is on hand yet — do not state one.

## Product Principles

1. The booking → email → dashboard pipeline is the product's core value; every surface change must preserve lead data integrity end to end.
2. Lead with care and reliability (insured, on-time, careful, professional), not price or speed.
3. Never fabricate proof — testimonials, ratings, and credentials must be real or explicitly marked as pending/placeholder.
4. Local Atlanta-area service is the primary audience; long-distance/out-of-state is real but secondary — don't let it crowd out the local pitch.

## Accessibility & Inclusion

No product-specific accessibility requirement has been established yet.
