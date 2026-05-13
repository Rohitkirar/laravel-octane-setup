# ──────────────────────────────────────────────────────────────
#  Stage 1 – Composer dependencies
# ──────────────────────────────────────────────────────────────
FROM composer:2.9 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader \
    --ignore-platform-reqs

# ──────────────────────────────────────────────────────────────
#  Stage 2 – Production image (php-cli + Swoole)
# ──────────────────────────────────────────────────────────────
FROM php:8.2-cli AS app

LABEL maintainer="Laravel Octane Demo"

# ── System dependencies ──
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libssl-dev \
    libsodium-dev \
    libonig-dev \
    libicu-dev \
    unzip \
    curl \
 && rm -rf /var/lib/apt/lists/*

# ── PHP extensions ──
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        bcmath \
        intl \
        opcache \
        pcntl \
        sockets

# ── Swoole (Octane server) ──
RUN pecl install swoole && docker-php-ext-enable swoole

# ── Redis PHP extension ──
RUN pecl install redis && docker-php-ext-enable redis

# ── php.ini + OPcache ──
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# ── Application code ──
WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

# Clear stale bootstrap cache that may reference host-only dev providers
RUN rm -f bootstrap/cache/packages.php bootstrap/cache/services.php

# Pull pre-built vendor from Stage 1
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor

# ── Storage & bootstrap cache directories ──
RUN mkdir -p storage/framework/{sessions,views,cache} \
             storage/logs \
             bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

# Discover packages now that artisan, vendor, and bootstrap/cache are all present
RUN php artisan package:discover --ansi

# ── Entrypoint ──
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000", "--workers=4", "--task-workers=2"]
