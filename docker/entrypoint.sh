#!/bin/bash
set -e

echo "──────────────────────────────────────"
echo "  Laravel Octane – Container Startup"
echo "──────────────────────────────────────"

# ── Wait for MySQL to be reachable ──────────────────────────────
if [[ "${DB_CONNECTION}" == "mysql" ]]; then
    echo "⏳ Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
    until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
        sleep 2
    done
    echo "✅ MySQL is ready."
fi

# ── Generate app key if missing ─────────────────────────────────
if [[ -z "${APP_KEY}" ]]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# ── Run migrations ───────────────────────────────────────────────
echo "🗃️  Running migrations..."
php artisan migrate --force --no-interaction

# ── Optimize for production ─────────────────────────────────────
if [[ "${APP_ENV}" == "production" ]]; then
    echo "⚙️  Caching config / routes / views..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# ── Ensure storage directories exist (bind-mount may hide Dockerfile-created ones) ──
mkdir -p storage/framework/{sessions,views,cache} \
         storage/logs \
         bootstrap/cache

# ── Fix permissions ─────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ── Start Octane (Swoole) ──────────────────────────────────────
echo "🚀 Starting Laravel Octane (Swoole)..."
exec php artisan octane:start \
    --server=swoole \
    --host=0.0.0.0 \
    --port=8000 \
    --workers=${OCTANE_WORKERS:-4} \
    --max-requests=${OCTANE_MAX_REQUESTS:-500}
