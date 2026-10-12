# EventHub - PHP Frontend (Individual Frontend Implementation)

Server-rendered PHP frontend for the **Event Ticketing and Venue Booking System**.
It contains no database code: every piece of data comes from the shared RESTful API
through one cURL helper (`includes/api.php`), and the JWT is kept in the PHP session.

## Setup (XAMPP)
1. Copy this folder to `C:\xampp\htdocs\frontend-farah`.
2. Start **Apache** in XAMPP (the `curl` PHP extension is enabled by default).
3. Start the shared backend API.
4. Edit `config.php`:
   - `API_BASE` -> the backend base URL, e.g. `http://localhost:3000/api`
   - `BASE_URL` -> the folder name in htdocs, e.g. `/frontend-farah`
5. Open `http://localhost/frontend-farah/`.

Sample accounts (from `database/seed.sql`, password `Password123!`):
`admin@eventhub.my` (admin), `daniel@eventhub.my` (organiser), `farid@example.com` (customer).

## Folder structure
```
frontend-farah/
  config.php            settings + session start
  includes/
    api.php             api_request(): cURL wrapper, adds Bearer token, normalises responses
    helpers.php         escaping, flash messages, role guards, CSRF, formatting
    header.php / footer.php
  index.php             browse + search/filter events
  event.php             event details + book tickets
  login.php / register.php / logout.php
  my_bookings.php       customer bookings, cancel
  admin/                events.php, event_form.php, venues.php, bookings.php
  assets/style.css
```

## API contract this frontend expects (agree on it with the backend developer)
Responses: `{ "success": true, "data": ..., "message": "..." }`

| Page | Method + endpoint | Role |
|---|---|---|
| login.php | POST /auth/login -> data.token, data.user{user_id, full_name, role} | public |
| register.php | POST /auth/register | public |
| index.php | GET /categories, GET /events?search=&category_id= | public |
| event.php | GET /events/:id, POST /bookings {event_id, quantity} | public / customer |
| my_bookings.php | GET /bookings/my, PUT /bookings/:id/cancel | customer |
| admin/events.php | GET /events?manage=1, DELETE /events/:id | admin, organiser |
| admin/event_form.php | GET /venues, GET /categories, POST /events, PUT /events/:id | admin, organiser |
| admin/venues.php | GET/POST /venues, DELETE /venues/:id | admin, organiser |
| admin/bookings.php | GET /bookings, PUT /bookings/:id/status {status} | admin, organiser |

Event objects should include the joined fields `venue_name`, `city`, `category_name`,
`organiser_name`. Booking objects should include `event_title`, `event_date`, `payment_status`
(customer list) and `customer_name` (admin list). If your teammate uses different endpoint or
field names, change them in one place: the matching `api_request(...)` call in the page.

## Status codes handled
- 400/422: validation message shown on the form
- 401: session cleared, redirect to login
- 403: role not allowed
- 404: not found
- 409: conflict (sold out, duplicate email, event has bookings)
- network failure: "Cannot reach the API server"

## Security features (frontend side)
CSRF token on every POST form, output escaping with `htmlspecialchars`, HttpOnly + SameSite
session cookie, session ID regenerated at login, role-based page guards (the API remains the
real enforcement), and the token never reaches the browser's JavaScript.

## Troubleshooting
- **No styling / broken links**: the files must sit directly in `htdocs/frontend-farah/` (not
  `frontend-farah/frontend-farah/`), and `BASE_URL` in `config.php` must match that folder name.
- **"Cannot reach the API server"**: the backend is not running, or `API_BASE` is wrong.
- **Timed out after 10000 ms while testing with the PHP mock server**: never serve the frontend and the
  mock API from the same `php -S` process. PHP's built-in server handles one request at a time, so the
  frontend ends up waiting for itself. Serve the frontend with XAMPP Apache
  (`http://localhost/frontend-farah/`) and run the mock API separately from its own folder.
