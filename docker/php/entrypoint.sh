#!/usr/bin/env sh
set -e

export COMPOSER_ALLOW_SUPERUSER=1

# 1) Install dependencies jika vendor belum ada
if [ ! -f /var/www/vendor/autoload.php ]; then
  echo ">> composer install (vendor not found)"
  composer install --no-interaction --prefer-dist
fi

# 2) Siapkan .env & app key
if [ ! -f /var/www/.env ] && [ -f /var/www/.env.example ]; then
  echo ">> creating .env from .env.example"
  cp /var/www/.env.example /var/www/.env
fi

if ! grep -qE '^APP_KEY=base64:' /var/www/.env 2>/dev/null; then
  echo ">> php artisan key:generate"
  php artisan key:generate
fi

# 3) Permission storage & cache (aman untuk bind mount)
mkdir -p /var/www/storage /var/www/bootstrap/cache
chmod -R ug+rwx /var/www/storage /var/www/bootstrap/cache || true

# 4) Jalankan server Laravel
echo ">> starting artisan serve on :8000"
exec php artisan serve --host=0.0.0.0 --port=8000
