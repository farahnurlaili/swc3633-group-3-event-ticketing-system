# Fixes for screenshots
- Admin venue access now checks a server-side login session and the current role in MySQL; browser localStorage alone is not trusted.
- Login initializes session and rotates session ID; logout clears session and browser token.
- Dashboard user and venue totals are fetched from authenticated API endpoints, rather than hard-coded 3 and 6.
- Removed JavaScript write to nonexistent metric-total-bookings element, allowing revenue to update.
- NOTE: End-to-end tests require a running XAMPP/MySQL installation. Importing schema.sql drops the database: back up first.
