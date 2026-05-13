# ⚡ Laravel Octane Setup

![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)
![Laravel](https://img.shields.io/badge/Laravel-12.x-red?logo=laravel)
![Octane](https://img.shields.io/badge/Laravel%20Octane-Swoole-orange)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?logo=docker)
![License](https://img.shields.io/badge/license-MIT-green)

A fully working **Laravel 12** application powered by **Laravel Octane + Swoole** for high-performance PHP serving. Ships with a multi-page Blade UI (Home, About, Blog, Contact) and a Docker Compose stack with Nginx and Redis. The MySQL database is intentionally **external** (runs on the host machine), making the containers lightweight and easy to manage.

---

## 📋 Table of Contents

- [About the Project](#about-the-project)
- [What is Laravel Octane?](#what-is-laravel-octane)
- [Tech Stack](#tech-stack)
- [Pages & Routes](#pages--routes)
- [Requirements](#requirements)
- [Getting Started](#getting-started)
    - [1. Clone & Configure](#1-clone--configure)
    - [2. Prepare the Database](#2-prepare-the-database)
    - [3. Running with Docker](#3-running-with-docker)
    - [4. Running Locally (without Docker)](#4-running-locally-without-docker)
- [Docker Architecture](#docker-architecture)
- [Project Structure](#project-structure)
- [Configuration](#configuration)
- [Common Commands](#common-commands)
- [License](#license)

---

## About the Project

This repository demonstrates a production-ready Laravel + Octane setup using best practices:

- **Swoole** as the Octane server (compiled into the Docker image)
- Clean MVC separation — Controller → Blade View
- Shared Blade layout with navigation
- Form handling with CSRF protection and validation
- Redis for sessions, cache, and queues
- External MySQL — connect to your local database server, no extra container needed
- Multi-stage Docker build for a small, secure image

---

## What is Laravel Octane?

Laravel Octane keeps the application **booted in memory** between requests via high-performance server adapters, eliminating the typical PHP bootstrap overhead on every request.

| Adapter        | Description                                                            |
| -------------- | ---------------------------------------------------------------------- |
| **Swoole**     | PHP extension — async I/O, coroutines, WebSocket support _(used here)_ |
| **RoadRunner** | Go-powered application server with HTTP/2 and gRPC                     |
| **FrankenPHP** | Modern PHP server built on top of Caddy                                |

**Key benefits:**

- ⚡ Up to 10× faster than traditional PHP-FPM
- 🔄 Concurrent task execution
- 📦 Shared memory between workers
- 🚀 Zero-downtime rolling restarts

---

## Tech Stack

| Layer      | Technology               |
| ---------- | ------------------------ |
| Language   | PHP 8.2                  |
| Framework  | Laravel 12               |
| Octane     | Laravel Octane v2        |
| Server     | **Swoole**               |
| Proxy      | Nginx (Alpine)           |
| Cache      | Redis 7                  |
| Database   | MySQL 8 _(host machine)_ |
| Templating | Blade                    |
| Containers | Docker + Compose v2      |

---

## Pages & Routes

| Method | URI        | Name             | Description                    |
| ------ | ---------- | ---------------- | ------------------------------ |
| GET    | `/`        | `home`           | Hero landing page              |
| GET    | `/about`   | `about`          | Project info and tech stack    |
| GET    | `/blog`    | `blog`           | Sample blog posts about Octane |
| GET    | `/contact` | `contact`        | Contact form with validation   |
| POST   | `/contact` | `contact.submit` | Handles form submission        |

---

## Requirements

### To run with Docker

| Requirement                                                                                                           | Version |
| --------------------------------------------------------------------------------------------------------------------- | ------- |
| [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) or Docker Engine + Compose v2 (Linux) | Latest  |
| MySQL server running on your **host machine**                                                                         | 8.0+    |

> Docker Desktop on Windows automatically provides the `host.docker.internal` hostname so containers can reach services on your host (e.g. MySQL). No extra configuration needed.

### To run locally (without Docker)

| Requirement          | Version               |
| -------------------- | --------------------- |
| PHP                  | 8.2+                  |
| Swoole PHP extension | `pecl install swoole` |
| Composer             | 2.x                   |
| MySQL                | 8.0+                  |
| Redis                | 7+                    |

---

## Getting Started

### 1. Clone & Configure

```bash
# Clone the repository
git clone https://github.com/<your-username>/laravel-octane-setup.git
cd laravel-octane-setup

# Copy the environment file
cp .env.example .env
```

Open `.env` and update the following values:

```dotenv
# Application
APP_KEY=          # Leave blank — entrypoint generates it automatically on first start
APP_URL=http://localhost

# Octane (Swoole)
OCTANE_SERVER=swoole
OCTANE_WORKERS=4
OCTANE_MAX_REQUESTS=500

# Database — points to your HOST machine MySQL
DB_CONNECTION=mysql
DB_HOST=host.docker.internal   # Use 127.0.0.1 when running without Docker
DB_PORT=3306
DB_DATABASE=laravel_octane_db
DB_USERNAME=root
DB_PASSWORD=your_password

# Redis (handled by the redis container)
REDIS_HOST=redis
REDIS_PORT=6379
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. Prepare the Database

Create the database on your **host machine** MySQL before starting the containers:

```sql
CREATE DATABASE laravel_octane_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

The entrypoint script runs `php artisan migrate --force` automatically on every container start, so no manual migration step is needed.

### 3. Running with Docker

```bash
# Build images and start all containers in the background
docker compose up -d --build
```

| Container      | Role                      | Exposed port (host)  |
| -------------- | ------------------------- | -------------------- |
| `octane_app`   | Laravel + Octane (Swoole) | none (internal only) |
| `octane_nginx` | Nginx reverse proxy       | **80**, **8000**     |
| `octane_redis` | Redis 7                   | 6379                 |

App available at **http://localhost** or **http://localhost:8000**.

> **MySQL is NOT a container.** It runs on your host machine and is reached via `host.docker.internal:3306`.

```bash
# View live logs
docker compose logs -f

# Stop containers
docker compose down

# Rebuild after code / dependency changes
docker compose up -d --build
```

### 4. Running Locally (without Docker)

```bash
# Install dependencies
composer install

# Generate app key
php artisan key:generate

# Update DB_HOST in .env to 127.0.0.1 instead of host.docker.internal
# then run migrations
php artisan migrate

# Start Octane (requires Swoole extension)
php artisan octane:start --server=swoole --port=8000 --workers=4

# Or use the built-in server (no Swoole required)
php artisan serve
```

---

## Docker Architecture

```
  Your machine
  ┌────────────────────────────────────────────────────────┐
  │                                                        │
  │  MySQL 8  (host :3306)                                 │
  │                                                        │
  │  ┌──────────────── Docker network: octane_net ──────┐  │
  │  │                                                   │  │
  │  │  ┌─────────────┐  proxy :8000  ┌──────────────┐  │  │
  │  │  │   octane_   │ ────────────► │  octane_app  │  │  │
  │  │  │   nginx     │               │  (Swoole)    │  │  │
  │  │  │   :80       │               │  internal    │  │  │
  │  │  └─────────────┘               └──────┬───────┘  │  │
  │  │        ▲                              │           │  │
  │  │   host :80 / :8000             host.docker.       │  │
  │  │                                internal:3306      │  │
  │  │  ┌─────────────┐                      │           │  │
  │  │  │ octane_redis│ ◄────────────────────┘           │  │
  │  │  │   :6379     │                                   │  │
  │  │  └─────────────┘                                   │  │
  │  └───────────────────────────────────────────────────┘  │
  └────────────────────────────────────────────────────────┘
```

---

## Project Structure

```
laravel-octane-setup/
├── app/
│   └── Http/Controllers/
│       └── PageController.php      # Home, About, Blog, Contact actions
├── config/
│   └── octane.php                  # Octane server configuration
├── docker/
│   ├── entrypoint.sh               # Container startup: waits for DB, migrates, starts Octane
│   ├── nginx/
│   │   └── default.conf            # Nginx → Octane proxy + static asset serving
│   └── php/
│       └── opcache.ini             # OPcache settings optimised for Octane
├── resources/views/
│   ├── layouts/app.blade.php       # Shared Blade layout (nav + footer)
│   ├── home.blade.php
│   ├── about.blade.php
│   ├── blog.blade.php
│   └── contact.blade.php
├── routes/
│   └── web.php                     # All web routes
├── .env                            # Local environment (not committed)
├── .env.example                    # Environment template
├── docker-compose.yml              # Compose: app + nginx + redis
└── Dockerfile                      # Multi-stage build (composer deps → app image)
```

---

## Configuration

Key `.env` variables for Octane and the Docker setup:

| Variable              | Default                | Description                                  |
| --------------------- | ---------------------- | -------------------------------------------- |
| `OCTANE_SERVER`       | `swoole`               | Octane adapter — `swoole`, `roadrunner`      |
| `OCTANE_WORKERS`      | `4`                    | Number of Swoole worker processes            |
| `OCTANE_MAX_REQUESTS` | `500`                  | Requests per worker before recycling         |
| `DB_HOST`             | `host.docker.internal` | Use `127.0.0.1` when running without Docker  |
| `REDIS_HOST`          | `redis`                | Docker service name; use `127.0.0.1` locally |

---

## Common Commands

```bash
# Run artisan commands inside the container
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
docker compose exec app php artisan cache:clear

# View logs
docker compose logs -f app
docker compose logs -f nginx

# Rebuild after Composer changes
docker compose up -d --build app

# Stop all containers (keeps volumes)
docker compose down

# Stop and wipe all volumes
docker compose down -v
```

---

## License

This project is open-sourced under the [MIT license](LICENSE).

A fully working **Laravel 12** demo application integrated with **Laravel Octane** for high-performance PHP serving. The project ships with a responsive multi-page Blade UI — Home, About, Blog, and Contact — showcasing how a real Laravel + Octane project is structured.

---

## 📋 Table of Contents

- [About the Project](#about-the-project)
- [What is Laravel Octane?](#what-is-laravel-octane)
- [Tech Stack](#tech-stack)
- [Pages & Routes](#pages--routes)
- [Getting Started](#getting-started)
    - [Prerequisites](#prerequisites)
    - [Installation](#installation)
    - [Running with Octane](#running-with-octane)
    - [Running with Standard PHP](#running-with-standard-php)
    - [Running with Docker](#running-with-docker)
- [Project Structure](#project-structure)
- [Configuration](#configuration)
- [License](#license)

---

## About the Project

This repository demonstrates a production-ready Laravel project setup using Laravel Octane. It was built following best-practices for:

- Clean MVC separation (Controller → View)
- Blade templating with a shared layout
- Form handling with CSRF protection and validation
- Octane configuration ready for Swoole or RoadRunner
- Docker setup with multi-stage builds, Nginx, Redis, MySQL, and Supervisor

---

## What is Laravel Octane?

Laravel Octane supercharges your application's performance by **keeping the app booted in memory** between requests, using high-performance server adapters:

| Adapter        | Description                                                          |
| -------------- | -------------------------------------------------------------------- |
| **Swoole**     | PHP extension providing async I/O, coroutines, and WebSocket support |
| **RoadRunner** | Go-powered application server with HTTP/2 and gRPC                   |
| **FrankenPHP** | Modern PHP server built on top of Caddy                              |

**Key benefits:**

- ⚡ Up to 10× faster than traditional PHP-FPM
- 🔄 Concurrent task execution
- 📦 Shared memory between workers
- 🛠 Tick & interval callbacks
- 🚀 Zero-downtime rolling restarts

---

## Tech Stack

| Layer         | Technology                |
| ------------- | ------------------------- |
| Language      | PHP 8.2                   |
| Framework     | Laravel 12                |
| Performance   | Laravel Octane v2         |
| Templating    | Blade (built-in)          |
| Styling       | Plain CSS (no build step) |
| Server (opt.) | Swoole / RoadRunner       |

---

## Pages & Routes

| Method | URI        | Name             | Description                                    |
| ------ | ---------- | ---------------- | ---------------------------------------------- |
| GET    | `/`        | `home`           | Hero landing page with Octane quick-start info |
| GET    | `/about`   | `about`          | Project info, tech stack, how Octane works     |
| GET    | `/blog`    | `blog`           | Six sample blog posts about Octane topics      |
| GET    | `/contact` | `contact`        | Contact form with validation                   |
| POST   | `/contact` | `contact.submit` | Handles form submission with redirect          |

---

## Getting Started

### Prerequisites

- PHP **8.2+**
- Composer **2.x**
- Git

> **Optional for Octane:** Swoole PHP extension (`pecl install swoole`) or RoadRunner binary.

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/<your-username>/laravel-octane-setup.git
cd laravel-octane-setup

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Link storage (optional)
php artisan storage:link
```

### Running with Octane

```bash
# Install your chosen server (first time only)
php artisan octane:install --server=swoole
# or
php artisan octane:install --server=roadrunner

# Start the Octane server
php artisan octane:start

# Start with specific worker count
php artisan octane:start --workers=4 --task-workers=2

# Start with file watching (development)
php artisan octane:start --watch
```

The server will be available at **http://localhost:8000**.

### Running with Standard PHP

If you don't have Swoole or RoadRunner, you can still run the app with the built-in artisan server:

```bash
php artisan serve
```

### Running with Docker

> **Requirements:** [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or Docker Engine + Compose v2)

#### 1. Copy and configure the environment file

```bash
cp .env.example .env
```

Edit `.env` and set a strong `APP_KEY` (or let the entrypoint generate it automatically on first start).

#### 2. Build and start all containers

```bash
docker compose up -d --build
```

This starts three containers:

| Container      | Service                               | Port   |
| -------------- | ------------------------------------- | ------ |
| `octane_app`   | Laravel + Octane (RoadRunner) + Nginx | `8000` |
| `octane_db`    | MySQL 8.0                             | `3306` |
| `octane_redis` | Redis 7                               | `6379` |

The app will be available at **http://localhost:8000**.

#### 3. Optional – phpMyAdmin

```bash
docker compose --profile tools up -d
# → http://localhost:8080
```

#### Common Docker commands

```bash
# View logs
docker compose logs -f app

# Run artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker

# Restart only the Octane process
docker compose exec app supervisorctl restart octane

# Rebuild after Composer changes
docker compose up -d --build app

# Stop all containers
docker compose down

# Stop and remove volumes (wipes database!)
docker compose down -v
```

#### Docker architecture

```
┌─────────────────────────────────────────────┐
│  octane_app container                       │
│                                             │
│   ┌──────────┐   proxy    ┌─────────────┐  │
│   │  Nginx   │ ─────────► │   Octane    │  │
│   │  :80     │            │ (RoadRunner)│  │
│   └──────────┘            │   :8080     │  │
│        ▲                  └─────────────┘  │
│        │ host port 8000                     │
└────────┼────────────────────────────────────┘
         │
   Browser / Client
```

---

## Project Structure

```
laravel-octane-setup/
├── app/
│   └── Http/
│       └── Controllers/
│           └── PageController.php       # Home, About, Blog, Contact actions
├── config/
│   └── octane.php                       # Octane server configuration
├── docker/
│   ├── entrypoint.sh                    # Container startup script
│   ├── nginx/
│   │   └── default.conf                 # Nginx → Octane proxy config
│   ├── php/
│   │   └── opcache.ini                  # OPcache settings optimised for Octane
│   └── supervisor/
│       └── supervisord.conf             # Manages Nginx + Octane processes
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php            # Shared Blade layout (nav + footer)
│       ├── home.blade.php               # Home page
│       ├── about.blade.php              # About page
│       ├── blog.blade.php               # Blog listing page
│       └── contact.blade.php            # Contact form page
├── routes/
│   └── web.php                          # All web routes
├── .dockerignore                        # Files excluded from Docker build context
├── .env.example                         # Environment template (Docker-ready)
├── docker-compose.yml                   # Compose: app + MySQL + Redis + phpMyAdmin
└── Dockerfile                           # Multi-stage build (composer → app)
```

---

## Configuration

Key settings in `config/octane.php`:

```php
'server'       => env('OCTANE_SERVER', 'swoole'),
'workers'      => env('OCTANE_WORKERS', 'auto'),   // auto = CPU count
'task_workers' => env('OCTANE_TASK_WORKERS', 'auto'),
'max_requests' => env('OCTANE_MAX_REQUESTS', 500),  // recycle worker every N requests
'warm'         => [
    // Bind services to warm on boot
],
'flush' => [
    // Services to flush between requests
],
```

You can also set these in `.env`:

```dotenv
OCTANE_SERVER=swoole
OCTANE_WORKERS=4
OCTANE_MAX_REQUESTS=500
```

---

## License

This project is open-sourced under the [MIT license](LICENSE).
