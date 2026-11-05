<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
</p>

<h1 align="center">🛡️ Laravel API Rate Limiter & Usage Monitor</h1>

<p align="center">
  <strong>Secure API keys · Per-client rate limits · Live usage monitoring</strong><br/>
  Protect your APIs with hashed keys, fixed-window limits, and a clean admin dashboard — using <b>MySQL + database cache only</b> (no Redis).
</p>

<p align="center">
  <a href="https://github.com/omaisjaveed/Laravel-API-Rate-Limiter-Usage-Monitor"><img src="https://img.shields.io/badge/GitHub-Repository-181717?style=flat-square&logo=github" alt="GitHub" /></a>
  <a href="mailto:omaisjaveed7@gmail.com"><img src="https://img.shields.io/badge/Email-omaisjaveed7%40gmail.com-EA4335?style=flat-square&logo=gmail&logoColor=white" alt="Email" /></a>
  <img src="https://img.shields.io/badge/Tests-PHPUnit-0D9488?style=flat-square&logo=php" alt="PHPUnit" />
  <img src="https://img.shields.io/badge/License-MIT-blue?style=flat-square" alt="License" />
</p>

---

## 👤 Author

| | |
|---|---|
| 👨‍💻 **Developer** | **Omais Javeed** |
| 📧 **Email** | [omaisjaveed7@gmail.com](mailto:omaisjaveed7@gmail.com) |
| 🌐 **GitHub** | [omaisjaveed/Laravel-API-Rate-Limiter-Usage-Monitor](https://github.com/omaisjaveed/Laravel-API-Rate-Limiter-Usage-Monitor/) |

---

## ✨ Features

| Icon | Feature | Description |
|------|---------|-------------|
| 🔐 | **API Key Auth** | Bearer tokens hashed with SHA-256; raw key shown only once |
| ⏱️ | **Per-client limits** | Independent minute / hour / day fixed-window limits |
| 🚫 | **HTTP 429** | Blocks abuse with `Retry-After` + rate-limit headers |
| 📊 | **Usage dashboard** | Overview stats, charts, top clients, recent activity |
| 🧾 | **Safe logging** | Logs requests without storing keys, passwords, or auth headers |
| 🧩 | **Demo CRUD APIs** | GET / POST / PUT / DELETE for products & orders |
| 🧪 | **Automated tests** | PHPUnit coverage for auth, keys, limits, and logging |
| 💾 | **No Redis** | Laravel database cache + locks only |

---

## 🧠 How it works

```text
   Client App
       │
       │  Authorization: Bearer API_KEY
       ▼
 ┌─────────────────────────────┐
 │  Authenticate API Key       │
 │  Check active + not revoked │
 └──────────────┬──────────────┘
                │
                ▼
 ┌─────────────────────────────┐
 │  Fixed-window Rate Limiter  │
 │  (minute / hour / day)      │
 │  DB cache + locks           │
 └──────────────┬──────────────┘
                │
        ┌───────┴───────┐
        │               │
     ✅ Allow        🚫 429 Block
        │               │
        └───────┬───────┘
                ▼
        Usage Log (safe fields)
                │
                ▼
           API Response
```

---

## 🧰 Tech stack

- 🐘 **PHP 8.2+** / **Laravel 12**
- 🗄️ **MySQL**
- 🎨 **Blade + Tailwind (CDN)**
- ⚡ **Database cache** + cache locks
- ✅ **PHPUnit** feature tests

---

## 📋 Requirements

- ✅ XAMPP (or PHP + MySQL)
- ✅ Composer
- ✅ MySQL database: `rate_limiter`

---

## 🚀 Installation

```bash
# 1) Install dependencies
composer install

# 2) Environment
copy .env.example .env
php artisan key:generate

# 3) Migrate + seed admin
php artisan migrate
php artisan db:seed

# 4) Run
php artisan serve
```

Open 👉 **http://127.0.0.1:8000**

### 🔑 Default admin login

| Field | Value |
|-------|-------|
| 📧 Email | `admin@example.com` |
| 🔒 Password | `password` |

> Change these credentials for any shared/production-like environment.

### ⚙️ `.env` (MySQL + database cache)

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

> ⚠️ Do **not** use Redis for this project.

---

## 🗄️ Database tables

| Table | Purpose |
|-------|---------|
| `users` | Admin dashboard accounts |
| `api_clients` | Clients + independent rate limits |
| `api_keys` | Hashed keys, prefix, revoke status |
| `api_request_logs` | Safe usage logs |
| `cache` / `cache_locks` | Rate-limit counters + concurrency locks |
| `sessions` / `jobs` | Session + queue support |

---

## 🧭 Admin dashboard

| Page | Route | What you get |
|------|-------|--------------|
| 📈 Overview | `/dashboard` | Stats, chart, top clients, recent activity |
| 👥 API Clients | `/dashboard/clients` | Create / edit / activate / deactivate |
| 🗝️ API Keys | `/dashboard/keys` | Generate / copy / revoke / regenerate |
| ⏳ Rate Limits | `/dashboard/rate-limits` | Edit limits + live usage |
| 📜 Request Logs | `/dashboard/logs` | Searchable usage history |
| 🚨 Violations | `/dashboard/violations` | Blocked `429` requests + stats |

---

## 🗝️ Create client & API key

1. Login to the dashboard  
2. **API Clients** → create client + set limits  
3. **API Keys** → **Generate key**  
4. 📋 Copy the raw key immediately (**shown once only**)  
5. Only `SHA-256(key)` is stored in the database  

Revoke / regenerate anytime from **API Keys**.

---

## ⏱️ Rate limiting

- Limits per client:
  - `rate_limit_per_minute`
  - `rate_limit_per_hour`
  - `rate_limit_per_day`
- Edit under **API Clients** or **Rate Limits**
- Live usage bars on **Rate Limits**
- Isolated counters → one client cannot burn another client’s quota
- Algorithm: **fixed window** + DB cache locks

---

## 📡 API endpoints

### Products

| Method | Endpoint |
|--------|----------|
| `GET` | `/api/v1/products` |
| `POST` | `/api/v1/products` |
| `GET` | `/api/v1/products/{id}` |
| `PUT` | `/api/v1/products/{id}` |
| `DELETE` | `/api/v1/products/{id}` |

### Orders

| Method | Endpoint |
|--------|----------|
| `GET` | `/api/v1/orders` |
| `POST` | `/api/v1/orders` |
| `PUT` | `/api/v1/orders/{id}` |
| `DELETE` | `/api/v1/orders/{id}` |

> Demo responses for local testing (not a real product DB).

---

## 🧪 Example requests

### GET (browser-friendly)

```
http://127.0.0.1:8000/api/v1/products?api_key=YOUR_API_KEY
```

### GET with Bearer token

```bash
curl http://127.0.0.1:8000/api/v1/products ^
  -H "Authorization: Bearer YOUR_API_KEY"
```

### POST product

```bash
curl -X POST http://127.0.0.1:8000/api/v1/products ^
  -H "Authorization: Bearer YOUR_API_KEY" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"name\":\"Gaming Headset\",\"price\":59.99,\"currency\":\"USD\"}"
```

### PUT product

```bash
curl -X PUT http://127.0.0.1:8000/api/v1/products/1 ^
  -H "Authorization: Bearer YOUR_API_KEY" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"name\":\"Wireless Mouse Pro\",\"price\":34.99}"
```

### DELETE product

```bash
curl -X DELETE http://127.0.0.1:8000/api/v1/products/1 ^
  -H "Authorization: Bearer YOUR_API_KEY" ^
  -H "Accept: application/json"
```

### Orders CRUD

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

---

## 📬 HTTP responses & headers

### ✅ Success (`200` / `201`)

Includes:

- `X-RateLimit-Limit`
- `X-RateLimit-Remaining`
- `X-RateLimit-Reset`
- `X-RateLimit-Window` (`minute` / `hour` / `day`)

### ❌ Auth errors

- `401` → missing / invalid / revoked key  
- `403` → inactive client  

### 🚫 Rate limit exceeded

- `429 Too Many Requests`
- `error = rate_limit_exceeded`
- `Retry-After` header included

---

## 🕵️ Usage logging (privacy-first)

**Stored**
- client id, key id, endpoint, method
- status code, response time
- rate-limit snapshot, blocked flag
- IP + timestamp

**Never stored**
- raw API keys
- Authorization headers
- passwords
- sensitive bodies
- `api_key` query values (path only)

---

## ✅ Testing

```bash
php artisan test
```

Covered:
- Admin authentication & dashboard authorization
- Valid / invalid / revoked API keys
- Successful API calls
- Per-client rate limits + HTTP `429`
- Independent client isolation
- Safe usage logging

---

## 📝 Notes

- 💾 Cache driver must stay `database` (no Redis)
- 🔐 Treat API keys like passwords
- 🪟 If Composer SSL fails on Windows/XAMPP, fix CA certificates first

---

<p align="center">
  <b>Built with ❤️ by Omais Javeed</b><br/>
  <a href="mailto:omaisjaveed7@gmail.com">omaisjaveed7@gmail.com</a> ·
  <a href="https://github.com/omaisjaveed/Laravel-API-Rate-Limiter-Usage-Monitor/">GitHub Repository</a>
</p>
