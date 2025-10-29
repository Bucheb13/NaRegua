# -------- ASSETS BUILD (Vite) --------
    FROM node:20 AS node_build
    WORKDIR /app
    # Copia configs do front apenas para cache eficiente
    COPY package*.json ./
    # Se não tiver lockfile, cai para npm i
    RUN npm ci || npm i
    # Copia fontes para build
    COPY resources ./resources
    COPY public ./public
    # Se usar Tailwind/PostCSS/Vite config, copie também:
    # COPY vite.config.* postcss.config.* tailwind.config.* ./
    # Build
    RUN npm run build || echo "Vite build skipped"
    
    # -------- COMPOSER (deps PHP) --------
    FROM composer:2 AS composer_deps
    WORKDIR /app
    COPY composer.json composer.lock ./
    RUN composer install --no-dev --prefer-dist --no-interaction --no-progress
    
    # -------- RUNTIME (PHP-FPM + NGINX) --------
    FROM php:8.4-fpm-bookworm
    
    # Dependências do sistema
    RUN apt-get update && apt-get install -y \
        nginx git unzip libpq-dev libzip-dev curl \
     && docker-php-ext-install pdo pdo_pgsql zip \
     && rm -rf /var/lib/apt/lists/*
    
    # Composer no runtime
    COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
    
    WORKDIR /var/www/html
    
    # Copia código do app
    COPY . /var/www/html
    
    # Copia vendor (já resolvido) e assets buildados
    COPY --from=composer_deps /app/vendor /var/www/html/vendor
    COPY --from=node_build /app/public/build /var/www/html/public/build
    
    # Nginx config
    COPY ./nginx.conf /etc/nginx/conf.d/default.conf
    
    # Entrypoint
    COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
    RUN chmod +x /usr/local/bin/entrypoint.sh
    
    # Permissões necessárias do Laravel
    RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
     && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    
    # O Render injeta PORT; vamos substituir no entrypoint
    ENV APP_ENV=production
    ENV PORT=8080
    
    # Não usar daemon do php-fpm (nginx ficará em foreground)
    CMD ["/usr/local/bin/entrypoint.sh"]
    