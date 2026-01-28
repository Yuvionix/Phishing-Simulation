# Phishing-Simulation

A Laravel web application for **authorized, internal security-awareness testing**. It hosts a spoofed login page to capture submitted credentials, lets an admin define phishing "campaigns" (subject, body text, target link), and provides a dashboard to review captured data.

> ⚠️ **Educational / authorized-use only.** This project simulates credential-harvesting phishing techniques. Using it against any target without that target's explicit, informed authorization is illegal in most jurisdictions. It exists to demonstrate and test human susceptibility to phishing as part of a sanctioned security-awareness program — nothing more.

---

## What this project actually does

This README describes only what is implemented in the codebase. Nothing below is aspirational.

1. An admin registers/logs in using Laravel's built-in auth scaffolding (`laravel/ui`).
2. The admin creates a **campaign**: a subject line, an email body (plain text), and a target/phishing link. This is stored in the database — the app does **not** send this as an actual email.
3. A separate route, `/facebook-login`, serves a static HTML clone of the Facebook login page.
4. When a victim submits that form, their email, password, IP address, and user agent are written to the database, and they are redirected to the real `facebook.com` (so nothing looks broken to them).
5. The admin can view all captured submissions on `/dashboard`, sorted newest-first.

That's the entire functional scope. See [Known Limitations](#known-limitations--unimplemented-features) below for things the project does *not* do, despite what a casual read of the code's naming might suggest.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend framework | Laravel 12 (PHP ^8.2) |
| Auth scaffolding | `laravel/ui` ^4.6 (Blade-based login/register) |
| Database | SQLite by default (MySQL supported via `.env` config) |
| ORM | Eloquent |
| Frontend templating | Blade |
| CSS | Bootstrap 5 + Tailwind CSS 4 |
| Build tool | Vite 6 (`laravel-vite-plugin`) |
| Dev tooling | `laravel/tinker` |

---

## Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── CampaignController.php    # create + list campaigns (no edit/show/delete)
│   │   ├── DashboardController.php   # lists captured phishing_logs
│   │   ├── PhishingController.php    # serves fake login page + captures credentials
│   │   └── HomeController.php        # default authenticated landing page
│   └── Models/
│       ├── Campaign.php              # subject, email_body, phishing_link
│       ├── PhishingLogs.php          # email, password, ip_address, user_agent
│       ├── ClickLog.php              # model exists, but see Known Limitations
│       └── User.php                  # standard Laravel auth user
├── database/
│   └── migrations/
│       ├── create_campaigns_table.php
│       ├── create_click_logs_table.php   # table exists, is never written to
│       └── create_phishing_logs_table.php
├── resources/
│   └── views/
│       ├── auth/                     # login.blade.php, register.blade.php, verify.blade.php
│       ├── campaigns/                # create.blade.php, index.blade.php
│       ├── phishing/
│       │   └── facebook.blade.php    # the spoofed login page
│       ├── dashboard.blade.php       # table of captured logs
│       └── home.blade.php
├── routes/
│   └── web.php
└── database/database.sqlite
```

---

## Routes

| Method | URI | Controller@method | Auth required |
|---|---|---|---|
| GET | `/` | closure → `welcome` view | No |
| GET/POST | `/login`, `/register`, etc. | `laravel/ui` Auth scaffolding | No |
| GET | `/home` | `HomeController@index` | Yes |
| GET | `/dashboard` | `DashboardController@index` | Yes |
| GET | `/campaigns` | `CampaignController@index` | Yes |
| GET | `/campaigns/create` | `CampaignController@create` | Yes |
| POST | `/campaigns` | `CampaignController@store` | Yes |
| GET | `/facebook-login` | `PhishingController@showLoginPage` | No (public — it's the lure page) |
| POST | `/facebook-login` | `PhishingController@captureCredentials` | No |

Note: `/campaigns` is declared as a full `Route::resource`, which registers routes for `show`, `edit`, `update`, and `destroy` as well — but `CampaignController` does not implement those methods, so hitting them will error.

---

## Database Schema

**`campaigns`**
| Column | Type |
|---|---|
| id | bigint, PK |
| subject | string |
| email_body | text |
| phishing_link | string |
| timestamps | created_at, updated_at |

**`phishing_logs`**
| Column | Type |
|---|---|
| id | bigint, PK |
| email | string |
| password | string (stored in **plaintext** — intentional, since the purpose of this table is to show trainers exactly what a victim entered; not suitable for storing real production credentials) |
| ip_address | string, nullable |
| user_agent | text, nullable |
| timestamps | created_at, updated_at |

**`click_logs`** (schema only — not currently used, see below)
| Column | Type |
|---|---|
| id | bigint, PK |
| user_id | foreign key → users |
| campaign_id | foreign key → campaigns |
| ip_address | string |
| timestamps | created_at, updated_at |

**`users`, `password_reset_tokens`, `sessions`, `cache`, `jobs`, `job_batches`, `failed_jobs`** — standard Laravel framework tables, unmodified from the default scaffolding.

---

## Known Limitations / Unimplemented Features

These are called out explicitly so the project isn't misrepresented:

- **No email sending.** A "campaign" is just a stored subject/body/link — there is no `Mail::` call anywhere in the codebase. To actually run a phishing test, you'd need to manually distribute the `phishing_link` yourself, or implement a mailer.
- **`click_logs` table is dead schema.** The migration and the `ClickLog` model both exist, but no controller or route ever writes to this table. There's currently no separate tracking of "link clicked" vs. "credentials submitted."
- **`CampaignController` only implements `index`, `create`, and `store`.** No edit, update, delete, or single-campaign view, despite `Route::resource` registering routes for all of those.
- **Only one lure page exists** (a Facebook login clone). There's no multi-template or multi-brand system.
- **Passwords are stored in plaintext** in `phishing_logs` by design — this is a captured-training-data table, not a real credentials store, but it means the database itself becomes sensitive and should be protected/purged accordingly in any real deployment.
- **No rate limiting, CAPTCHA, or bot filtering** on the `/facebook-login` capture endpoint.
- **No CSRF-free API** — this is a monolithic Blade app, not a decoupled frontend/backend.

---

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- SQLite (default) or MySQL

---

## Installation & Running Locally

```bash
# 1. Enter the project root (the Laravel app root, not any nested/duplicate folder)
cd PhisingSim

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install

# 4. Set up environment
cp .env.example .env
php artisan key:generate

# 5. Set up the database
touch database/database.sqlite    # if it doesn't already exist
php artisan migrate

# 6. Build frontend assets
npm run build
# — or, for development with hot reload, run in a separate terminal:
# npm run dev

# 7. Serve the app
php artisan serve
```

Then open `http://localhost:8000`:
1. Register an account (this becomes your admin login — there's no separate role/permission system; any registered user has full dashboard/campaign access).
2. Go to `/campaigns` → create a campaign (stores the subject/body/link — does not send anything).
3. Visit `/facebook-login` and submit the form to test the capture flow.
4. Check `/dashboard` to see the logged submission (email, password, IP, user agent).

---

## Security & Ethics Notice

This tool implements a real credential-harvesting technique (MITRE ATT&CK T1566 – Phishing). It is intended solely for use in **authorized security-awareness programs**, where the organization running the test has clear internal authorization to simulate phishing against its own users. Do not point this at any target — including public services like the real Facebook — without explicit, documented authorization. Unauthorized use of phishing techniques is illegal.

---

## License

MIT License. Built on the Laravel framework (also MIT-licensed).
