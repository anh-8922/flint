# Flint — Docker Compose Setup Guide

> **Prerequisites:** Docker Desktop installed and running. Nothing else needed — no PHP, no Composer, no Node installed locally.

---

## Overview

We use **Laravel Sail**, which is Laravel's official Docker Compose wrapper. It gives you a pre-configured `docker-compose.yml` with PHP, MySQL, and Redis out of the box — no manual Docker configuration needed to get started.

---

## Step 1 — Create the Laravel Project

Run this from your terminal in whatever directory you keep your projects:

```bash
curl -s "https://laravel.build/flint?with=mysql,redis" | bash
```

What this does:
- Pulls a temporary Docker container
- Runs `composer create-project laravel/laravel` inside it
- Installs Laravel Sail and configures `docker-compose.yml` for MySQL + Redis
- Drops everything into a `flint/` folder

When it finishes, move into the project:

```bash
cd flint
```

---

## Step 2 — Start the Docker Environment

```bash
./vendor/bin/sail up -d
```

The `-d` flag runs it in detached mode (background). First run takes a few minutes to pull the Docker images. Subsequent starts are fast.

Verify all containers are running:

```bash
./vendor/bin/sail ps
```

You should see three containers running:

| Container | What it is |
|---|---|
| `flint-laravel.test-1` | PHP 8.x + your Laravel app |
| `flint-mysql-1` | MySQL 8 database |
| `flint-redis-1` | Redis (for caching TMDB responses) |

Open `http://localhost:8088` — you should see the Laravel welcome page.

---

## Step 3 — Set Up the Shell Alias

Typing `./vendor/bin/sail` every time is tedious. Add a permanent alias:

**Mac/Linux (bash):**
```bash
echo "alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc
```

**Mac (zsh — default on modern Macs):**
```bash
echo "alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'" >> ~/.zshrc
source ~/.zshrc
```

**Windows (Git Bash):**
```bash
echo "alias sail='sh vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc
```

From here on, every `sail` command in this guide assumes this alias is active.

---

## Step 4 — Configure the `.env` File

The `.env` file at the root of the project holds all your environment config. Sail pre-fills the database section — verify it looks like this:

```env
APP_NAME=Flint
APP_PORT=8088
APP_URL=http://localhost:8088

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3406
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis
REDIS_PORT=6379

CACHE_STORE=database
SESSION_DRIVER=database
```

> **Important:** `DB_HOST=mysql` and `REDIS_HOST=redis` use Docker's internal network hostnames — not `127.0.0.1`. This is correct and must stay as-is.

Now add your TMDB credentials at the bottom of `.env`:

```env
# TMDB API
TMDB_API_KEY=your_api_key_here
TMDB_BASE_URL=https://api.themoviedb.org/3
TMDB_IMAGE_BASE_URL=https://image.tmdb.org/t/p
```

> Get your free TMDB API key at: https://www.themoviedb.org/settings/api

---

## Step 5 — Run the Database Migrations

```bash
sail artisan migrate
```

This creates all the tables in MySQL. You should see output like:

```
INFO  Running migrations.
  2014_10_12_000000_create_users_table ........... 12ms DONE
  2014_10_12_100000_create_password_reset_tokens_table ... 8ms DONE
  ...
```

---

## Step 6 — Install Laravel Breeze (Auth)

```bash
sail composer require laravel/breeze --dev
sail artisan breeze:install blade
```

When prompted, select:
- **Dark mode:** No (we'll handle this ourselves)
- **PHPUnit / Pest:** Pest (or PHPUnit — either is fine for now)

Then install and build the frontend assets:

```bash
sail npm install
sail npm run dev
```

> Keep `npm run dev` running in a terminal tab while you're developing — it watches for file changes and rebuilds assets (Tailwind CSS, etc.) instantly.

---

## Step 7 — Re-run Migrations (Breeze adds tables)

```bash
sail artisan migrate
```

Breeze adds a `password_reset_tokens` and `sessions` table. Running migrate again picks those up safely.

---

## Step 8 — Verify Everything Works

| Check | How |
|---|---|
| App loads | Visit `http://localhost:8088` |
| Register works | Visit `http://localhost:8088/register`, create an account |
| Login works | Visit `http://localhost:8088/login` |
| Database connected | Login succeeded = DB is working |
| Redis working | `./vendor/bin/sail redis ping` → should return `PONG` |

---

## Daily Workflow

### Starting work

```bash
cd flint
sail up -d
sail npm run dev
```

### Stopping work

```bash
# Stop npm watcher: Ctrl+C in its terminal tab

# Stop Docker containers
sail down
```

### Common commands during development

```bash
# Run a migration
sail artisan migrate

# Create a new migration
sail artisan make:migration create_watchlists_table

# Create a controller
sail artisan make:controller MovieController

# Create a model + migration + factory in one go
sail artisan make:model Review -mf

# Create a seeder
sail artisan make:seeder ReviewSeeder

# Run seeders
sail artisan db:seed

# Clear all caches
sail artisan optimize:clear

# Drop all tables and re-migrate + seed (useful in development)
sail artisan migrate:fresh --seed

# Open a MySQL shell
sail mysql

# Open a PHP REPL (great for testing Eloquent queries)
sail artisan tinker

# Run tests
sail artisan test

# Tail the Laravel log
sail logs -f
```

---

## Project File Structure

After setup, your project root will look like this:

```
flint/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/               ← Breeze auth controllers
│   │   │   ├── Controller.php
│   │   │   └── ProfileController.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       └── ProfileUpdateRequest.php
│   ├── Models/
│   │   └── User.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   ├── Services/                   ← we'll add TmdbService.php here
│   └── View/
│       └── Components/
│           ├── AppLayout.php
│           └── GuestLayout.php
├── database/
│   ├── migrations/
│   ├── factories/
│   │   └── UserFactory.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── views/
│   │   ├── auth/                   ← Breeze auth views
│   │   ├── layouts/
│   │   ├── components/
│   │   ├── profile/
│   │   └── dashboard.blade.php
│   ├── css/
│   │   └── app.css                 ← Tailwind directives live here
│   └── js/
│       ├── app.js
│       └── bootstrap.js
├── routes/
│   ├── web.php                     ← main routes
│   ├── auth.php                    ← Breeze auth routes
│   └── console.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env                            ← your config (never commit this)
├── compose.yaml                    ← Sail's Docker setup
└── vite.config.js
```

---

## Troubleshooting

### Port already in use

If `sail up -d` fails with "port is already allocated", find a free port and update `.env`:

```bash
# Check what's using common ports
sudo lsof -iTCP -sTCP:LISTEN -P | grep -E ':(80|8080|8088)\s'
```

Then set a free port in `.env`:
```env
APP_PORT=8088          # change to any free port
APP_URL=http://localhost:8088
```

The `compose.yaml` port mapping is already parameterised — no changes needed there.

### MySQL connection refused

The MySQL container might still be starting up. Give it 10–15 seconds after `sail up -d`, then retry:

```bash
sail artisan migrate
```

If it persists, check the container is actually running:

```bash
sail ps
# If mysql shows as "exited", restart it:
sail down && sail up -d
```

### npm run dev fails / Tailwind not loading

```bash
# Delete node_modules and reinstall
rm -rf node_modules
sail npm install
sail npm run dev
```

### Permission errors on Linux

```bash
sudo chown -R $USER:$USER .
```

### "Class not found" after adding a new file

```bash
sail composer dump-autoload
```

### Completely reset everything (nuclear option)

```bash
sail down -v          # stops containers AND deletes volumes (wipes the database)
sail up -d
sail artisan migrate --seed
```

---

## Docker Compose — What's Actually Running

Sail uses `compose.yaml` at the project root. Here's what the key parts mean:

```yaml
services:
  laravel.test:          # Your PHP app container
    build: ...           # Uses Sail's pre-built PHP image
    ports:
      - '${APP_PORT:-8080}:80'  # Host port (from .env) → container port 80
    volumes:
      - '.:/var/www/html' # Your local files mounted live into the container
                          # Edit files locally, changes appear instantly
    depends_on:
      - mysql
      - redis

  mysql:                 # MySQL 8.4 database
    image: mysql:8.4
    ports:
      - '${FORWARD_DB_PORT:-3406}:3406'  # Accessible from local DB clients
    volumes:
      - sail-mysql:/var/lib/mysql   # Data persists between restarts

  redis:                 # Redis
    image: redis:alpine
    ports:
      - '6379:6379'
```

> **The most important part:** `.:/var/www/html` — this mounts your local project folder directly into the container. You edit files in VS Code locally, and the running app inside Docker sees the changes immediately. No rebuilding required.

---

## Connecting a Database GUI (Optional)

If you want to browse the database visually (recommended):

**TablePlus** (free tier, Mac/Windows/Linux) or **DBeaver** (free, all platforms):

| Setting | Value |
|---|---|
| Host | `127.0.0.1` |
| Port | `3406` |
| Database | `laravel` |
| Username | `sail` |
| Password | `password` |

---

## Next Step

Once `http://localhost:8088` loads and you can register/login, you're ready to start building.

**Next:** Create the `TmdbService` and make your first live API call to TMDB. →

---

*Setup guide v1.0 — part of the Flint project docs.*