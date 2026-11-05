# Laravel API Rate Limiter & Usage Monitor

A local Laravel application that authenticates API clients with hashed API keys, enforces **per-client** fixed-window rate limits (minute / hour / day) using Laravel’s **database cache driver** (no Redis), and records safe API usage logs for an admin dashboard.

## Stack

- PHP 8.2+ / Laravel 12
- MySQL
- Laravel session auth for the admin dashboard
- Blade + Tailwind (CDN) UI
- Database cache + cache locks for concurrent-safe rate limiting
- PHPUnit feature tests

## Requirements

- XAMPP (or equivalent) with PHP + MySQL
- Composer
- MySQL database named `rate_limiter` (or update `.env`)

## Installation

1. Open the project folder in a terminal.

2. Install PHP dependencies (if `vendor/` is missing):

```bash
composer install
```

3. Copy environment file if needed:

```bash
copy .env.example .env
php artisan key:generate
```

4. Configure MySQL in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rate_limiter
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

> Do **not** set the cache driver to Redis for this project.

5. Run migrations and seed the admin user:

```bash
php artisan migrate
php artisan db:seed
```

6. Start the app:

```bash
php artisan serve
```

Open `http://127.0.0.1:8000`

### Default admin login

- Email: `admin@example.com`
- Password: `password`

Change this after first login in production-like setups.

## Database configuration

Main tables:

| Table | Purpose |
|-------|---------|
| `users` | Admin dashboard accounts |
| `api_clients` | API clients + independent rate limits |
| `api_keys` | Hashed API keys (`key_hash`), prefix, revoke timestamp |
| `api_request_logs` | Usage logs (no raw secrets) |
| `cache` / `cache_locks` | Database-backed rate-limit counters + locks |
| `sessions` / `jobs` | Session + queue tables |

## Create an API client & key

1. Login to the dashboard.
2. Open **API Clients** → create a client and set minute/hour/day limits.
3. Open **API Keys** → **Generate key**.
4. Copy the raw key immediately. It is shown **once** only.
5. Only `SHA-256(key)` is stored in `api_keys.key_hash`.

You can revoke or regenerate keys later from **API Keys**.

## Rate-limit configuration

- Per-client limits live on `api_clients`:
  - `rate_limit_per_minute`
  - `rate_limit_per_hour`
  - `rate_limit_per_day`
- Edit them under **API Clients** or **Rate Limits**.
- **Rate Limits** also shows live usage for the current fixed windows.
- Counters are isolated by client ID, so one client cannot consume another client’s quota.
- Algorithm: fixed window, protected with database cache locks.

## Example API requests

### Products list

```bash
curl http://127.0.0.1:8000/api/v1/products ^
  -H "Authorization: Bearer YOUR_API_KEY"
```

Browser-friendly test (local only):

```
http://127.0.0.1:8000/api/v1/products?api_key=YOUR_API_KEY
```

### Single product

```bash
curl http://127.0.0.1:8000/api/v1/products/1 ^
  -H "Authorization: Bearer YOUR_API_KEY"
```

### Orders list

```bash
curl http://127.0.0.1:8000/api/v1/orders ^
  -H "Authorization: Bearer YOUR_API_KEY"
```

### Create product (POST)

```bash
curl -X POST http://127.0.0.1:8000/api/v1/products ^
  -H "Authorization: Bearer YOUR_API_KEY" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"name\":\"Gaming Headset\",\"price\":59.99,\"currency\":\"USD\"}"
```

### Update product (PUT)

```bash
curl -X PUT http://127.0.0.1:8000/api/v1/products/1 ^
  -H "Authorization: Bearer YOUR_API_KEY" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"name\":\"Wireless Mouse Pro\",\"price\":34.99}"
```

### Delete product (DELETE)

```bash
curl -X DELETE http://127.0.0.1:8000/api/v1/products/1 ^
  -H "Authorization: Bearer YOUR_API_KEY" ^
  -H "Accept: application/json"
```

### Create / update / delete order

```bash
curl -X POST http://127.0.0.1:8000/api/v1/orders ^
  -H "Authorization: Bearer YOUR_API_KEY" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"total\":120.50,\"status\":\"pending\"}"

curl -X PUT http://127.0.0.1:8000/api/v1/orders/1001 ^
  -H "Authorization: Bearer YOUR_API_KEY" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"status\":\"paid\",\"total\":120.50}"

curl -X DELETE http://127.0.0.1:8000/api/v1/orders/1001 ^
  -H "Authorization: Bearer YOUR_API_KEY" ^
  -H "Accept: application/json"
```

Demo endpoint payloads are sample JSON for local testing (not a real product database).

## HTTP responses & headers

### Success (200)

JSON body plus headers:

- `X-RateLimit-Limit`
- `X-RateLimit-Remaining`
- `X-RateLimit-Reset` (unix timestamp)
- `X-RateLimit-Window` (`minute` / `hour` / `day`)

### Missing / invalid / revoked key

- `401 Unauthorized` (inactive client may return `403`)

### Rate limit exceeded

- `429 Too Many Requests`
- JSON: `error = rate_limit_exceeded`
- Headers include `Retry-After` and rate-limit headers

## Usage logging (privacy)

Each API request writes to `api_request_logs`:

- client id, key id, endpoint, method
- status code, response time
- rate-limit snapshot, blocked flag
- IP + timestamp

Never stored:

- raw API keys
- Authorization headers
- passwords
- sensitive request bodies
- `api_key` query values (endpoint path only is saved)

View logs under **Request Logs**. Blocked calls also appear under **Violations**.

## Admin dashboard pages

| Page | Route |
|------|-------|
| Overview | `/dashboard` |
| API Clients | `/dashboard/clients` |
| API Keys | `/dashboard/keys` |
| Rate Limits | `/dashboard/rate-limits` |
| Request Logs | `/dashboard/logs` |
| Violations | `/dashboard/violations` |

## Testing

```bash
php artisan test
```

Covered areas include:

- Admin authentication / dashboard authorization
- Valid, invalid, and revoked API keys
- Successful API responses
- Per-client rate limits and HTTP 429
- Independent limits across clients
- Usage logging without leaking secrets

## Notes

- Cache driver must remain `database` (project requirement: no Redis).
- If Composer HTTPS/SSL fails on Windows/XAMPP, fix CA certificates or use a working Composer setup before installing packages.
- Keep generated API keys private; treat them like passwords.
