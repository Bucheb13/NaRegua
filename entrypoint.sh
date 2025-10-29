#!/usr/bin/env bash
set -e

# Substitui a porta no nginx.conf (Render injeta $PORT)
PORT="${PORT:-8080}"
sed -i "s/%%PORT%%/${PORT}/g" /etc/nginx/conf.d/default.conf

# Ajusta permissões de runtime
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Aguarda o banco ficar acessível antes de migrar
echo "Aguardando banco de dados..."
until php artisan db:connection &> /dev/null; do
    sleep 2
done
echo "Banco disponível!"

# Otimizações Laravel
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Link do storage -> public/storage
php artisan storage:link || true

# Migrações (forçadas em prod)
php artisan migrate --force || true

# Sobe php-fpm em background e nginx em foreground
php-fpm -D
exec nginx -g "daemon off;"
