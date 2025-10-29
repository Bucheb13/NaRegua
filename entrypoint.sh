#!/usr/bin/env bash
set -e

# Substitui a porta no nginx.conf (Render injeta $PORT)
PORT="${PORT:-8080}"
sed -i "s/%%PORT%%/${PORT}/g" /etc/nginx/conf.d/default.conf

# Ajusta permissões de runtime
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Otimizações Laravel e migrações
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Link do storage -> public/storage
php artisan storage:link || true

# Migrações (força no deploy)
php artisan migrate --force || true

# Sobe php-fpm em background e nginx em foreground
php-fpm -D
exec nginx -g "daemon off;"
