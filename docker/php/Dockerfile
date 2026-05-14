# =============================================================
# Stage 1: Build frontend assets
# =============================================================
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm run build

# =============================================================
# Stage 2: PHP production image
# =============================================================
FROM php:8.3-fpm-bookworm AS app

# ── System dependencies ──────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    # Core utilities
    curl \
    unzip \
    git \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libgd-dev \
    # Supervisor (process manager)
    supervisor \
    # Nginx
    nginx \
 && rm -rf /var/lib/apt/lists/*

# ── PHP extensions ──────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    gd \
    bcmath \
    opcache \
    pcntl

# ── Composer ────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ── Install PHP dependencies (production only) ───────────────
COPY composer.json composer.lock* ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --prefer-dist

# ── Copy application source ──────────────────────────────────
COPY . .

# ── Copy compiled frontend assets from Stage 1 ───────────────
COPY --from=frontend /app/public/build ./public/build

# ── Finish Composer autoloader ───────────────────────────────
RUN composer dump-autoload --optimize --no-dev

# ── Permissions ─────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage \
 && chmod -R 755 /var/www/html/bootstrap/cache

# ── Copy config files ────────────────────────────────────────
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 80

# Supervisor manages nginx + php-fpm + queue worker
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
