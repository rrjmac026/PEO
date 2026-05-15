#!/bin/sh
set -e

# ── Storage symlink ──────────────────────────────────────────────────────────
# Must run AFTER the volume is mounted, so it can't be a RUN step in Dockerfile.
# Creates public/storage -> storage/app/public if it doesn't already exist.
if [ ! -L /var/www/html/public/storage ]; then
    php /var/www/html/artisan storage:link --force
fi

# ── Ensure correct permissions on the mounted storage volume ─────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── Hand off to supervisord ──────────────────────────────────────────────────
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
