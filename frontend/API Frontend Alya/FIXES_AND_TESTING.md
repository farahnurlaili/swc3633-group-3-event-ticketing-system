# EventTix API integration fixes

Changes: signed HMAC bearer tokens (instead of forgeable base64), verified users/roles against DB, removed login password bypass and mock admin session, fixed organiser booking access, added booking transaction row lock and quantity checks, enforced event ownership and preserved sold-ticket counts on event edits, validated user roles, cleared token on logout, and supplied missing CSS file.

## Important
- Set `EVENTTIX_TOKEN_SECRET` to a strong random secret of at least 32 characters in production. Otherwise a `.token_secret` file is generated in the project folder; restrict access and keep it out of version control.
- Tokens from the old version are invalid: **log in again**.
- Database schema was not included in the ZIP, so database integration has **not been end-to-end tested**. Import the matching `event_ticketing` database before testing.
- Payments still use **simulated paid status** and are not real payment-gateway verified. Never use this payment workflow for real money.
- Frontend reads the token for navigation, but sensitive authorization must always be enforced on the backend. Server-rendered pages should additionally be protected before production deployment.
- Test: register, login each role, create/edit event, attempt organiser edit of another organiser's event, book tickets, try quantity 0 and quantity greater than available, inspect booking visibility, and logout.
