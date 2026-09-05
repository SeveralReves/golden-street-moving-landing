# Flujo de cotización de mudanza

## 1. Formulario público (`Booking.vue`)

Embebido en la landing vía `resources/views/components/section-booking.blade.php` (`data-vue="Booking"`). Recolecta:

- Datos de contacto: `name`, `email`.
- Direcciones de origen/destino, capturadas con `AddressAutocomplete.vue` (Google Places) y enviadas como objetos anidados:
  - `org_address.formatted`, `org_address.raw.location.lat`, `org_address.raw.location.lng`
  - `end_address.formatted`, `end_address.raw.location.lat`, `end_address.raw.location.lng`
- `date`, `schedule` (fecha y franja horaria preferida).
- Detalles de la mudanza: `move_type` (`residential` | `office` | `storage`), `origin_floor`, `origin_elevator`, `destination_floor`, `destination_elevator`, `packing_service`, `comments`.

El formulario hace `POST /api/moving-quotes`.

## 2. Endpoint de creación

`MovingQuoteController::store` (`app/Http/Controllers/MovingQuoteController.php`):

1. Valida el payload anidado (ver reglas en el controlador).
2. Aplana los datos a las columnas de la tabla `moving_quotes` (`origin_address`, `origin_lat`, `origin_lng`, `destination_address`, `destination_lat`, `destination_lng`, etc.) y crea un `MovingQuote` con `status = 'pending'`.
3. Llama a `ResendService::sendLead($quote)` para notificar por correo.
4. Responde `201` con `{ message, email_sent, quote }`.

## 3. Notificación por correo (`ResendService`)

`app/Services/ResendService.php`:

- Lee configuración desde `config('services.resend.*')` (`RESEND_API_KEY`, `RESEND_FROM_EMAIL`, `RESEND_FROM_NAME`, `RESEND_TO_EMAIL`).
- Si falta `RESEND_API_KEY`, registra un error en el log y devuelve `false` sin lanzar excepción — la cotización igual queda guardada.
- Renderiza `resources/views/emails/moving_lead.blade.php` con la cotización y lo envía vía el SDK de Resend al `to_email` configurado (destinado al equipo, no al cliente).

## 4. Panel de administración

- `DashboardController::index` (ruta `/dashboard`, middleware `auth`+`verified`) carga todas las `MovingQuote` y las pasa a la vista `resources/views/dashboard.blade.php`.
- `BookingTable.vue` (`data-vue="BookingTable"`) renderiza la tabla, con un modal para ver el detalle de una cotización y cambiar su `status`.
- Al guardar, hace `PUT /api/moving-quotes/{movingQuote}` → `MovingQuoteController::update`, que valida `status` contra `pending|in_review|quoted|closed|cancelled` y lo persiste.

## Nota sobre autorización

`POST /api/moving-quotes` es pública (cualquier visitante puede enviar una cotización) y está en `routes/api.php`. `PUT /api/moving-quotes/{movingQuote}` está registrada en `routes/web.php`, dentro del mismo grupo `Route::middleware('auth')` que las rutas de perfil — misma URL que consume `BookingTable.vue`, pero ahora requiere sesión iniciada para poder cambiar el estado de una cotización.
