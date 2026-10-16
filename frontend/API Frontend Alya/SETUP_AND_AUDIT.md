# Event Ticketing — Integration Setup & Audit

## Install on XAMPP
1. Copy the project folder to `C:\xampp\htdocs\API Frontend Alya`.
2. Start Apache and MySQL. In phpMyAdmin import `database/schema.sql` then `database/seed.sql`. **WARNING: schema.sql drops and recreates the event_ticketing database; back up existing data first.**
3. Check credentials in `db.php` (default local XAMPP root with blank password).
4. Open `http://localhost/API%20Frontend%20Alya/login.php`.
5. Sample admin: `admin@eventhub.my`, password `Password123!`; organiser: `daniel@eventhub.my`; customer: `farid@example.com`. Change all sample passwords before deploying.

## Schema mapping
`api_auth.php` -> users; `api_events.php` -> events, venues, categories; `api_bookings.php` -> bookings, payments, events, users; `api_users.php` -> users.

## Fixes in this package
- Existing signed tokens, role checks, owner checks and transactional booking lock retained.
- Event status values `active/published`, `ended/cancelled`, `draft` now map to SQL ENUM correctly on write.
- Event deletion no longer destroys customer bookings and payment history. Events with bookings must be cancelled instead.
- Database errors are no longer exposed directly to API clients.
- Supplied schema and seed bundled for reproducible local setup.

## Remaining limitations
- Checkout is a **demo payment simulation**: no real payment gateway is called, even though bookings are recorded as paid. Do not use for real money.
- Not tested against a running MySQL server/browser in this environment; PHP syntax and static schema matching only.
- Before production: add CSRF/XSS review, stronger HTTP security headers, rate limiting, HTTPS, production secrets and payment verification.
