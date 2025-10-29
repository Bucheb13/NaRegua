# -------- ASSETS BUILD (Vite) --------
    FROM node:20 AS node_build
    WORKDIR /app
    COPY package*.json ./
    RUN npm ci || npm i
    COPY resources ./resources
    COPY public ./public
    # Se tiver configs do Vite/Tailwind/PostCSS, copie também
    COPY vite.config.* postcss.config.* tailwind.config.* ./
    RUN npm run build || echo "Vite build skipped"
    
    # -------- RUNTIME (PHP-FPM + NGINX) --------
    FROM php:8.4-fpm-bookworm
    
    # Dependências
    RUN apt-get update && apt-get install -y \
        nginx git unzip libpq-dev libzip-dev curl \
        && docker-php-ext-install pdo pdo_pgsql zip \
        && rm -rf /var/lib/apt/lists/*
    
    WORKDIR /var/www/html
    
    # Copia código do app
    COPY . /var/www/html
    
    # Agora sim: composer install com artisan presente
    RUN curl -sS https://getcomposer.org/installer | php \
        && php composer.phar install --no-dev --prefer-dist --no-interaction --no-progress \
        && rm composer.phar
    
    # Copia assets buildados
    COPY --from=node_build /app/public/build /var/www/html/public/build
    
    # Nginx config
    COPY ./nginx.conf /etc/nginx/conf.d/default.conf
    
    # Entrypoint
    COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
    RUN chmod +x /usr/local/bin/entrypoint.sh
    
    # Permissões
    RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    
    ENV APP_ENV=production
    ENV PORT=8080
    
    CMD ["/usr/local/bin/entrypoint.sh"]
    