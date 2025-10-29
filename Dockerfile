# -------- ASSETS BUILD (Vite) --------
    FROM node:20 AS node_build
    WORKDIR /app
    
    # Copiar apenas arquivos essenciais pra dependências primeiro
    COPY package*.json ./
    RUN npm ci || npm install
    
    # Copiar restante do projeto necessário p/ Vite
    COPY vite.config.* postcss.config.* tailwind.config.* ./
    COPY resources ./resources
    COPY public ./public
    
    # Build assets
    RUN npm run build
    
    # -------- RUNTIME (PHP-FPM + NGINX) --------
    FROM php:8.4-fpm-bookworm
    
    # Dependências do sistema
    RUN apt-get update && apt-get install -y \
        nginx git unzip libpq-dev libzip-dev curl \
        && docker-php-ext-install pdo pdo_pgsql zip \
        && rm -rf /var/lib/apt/lists/*
    
    WORKDIR /var/www/html
    
    # Copiar o código do app
    COPY . /var/www/html
    
    # Instalar composer
    RUN curl -sS https://getcomposer.org/installer | php \
        && php composer.phar install --no-dev --prefer-dist --no-interaction --no-progress \
        && rm composer.phar
    
    # Copiar assets buildados
    COPY --from=node_build /app/public/build /var/www/html/public/build
    
    # Copiar config do nginx
    COPY ./nginx.conf /etc/nginx/conf.d/default.conf
    
    # Entrypoint
    COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
    RUN chmod +x /usr/local/bin/entrypoint.sh
    
    # Ajuste permissões
    RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    
    ENV APP_ENV=production
    ENV PORT=8080
    
    CMD ["/usr/local/bin/entrypoint.sh"]
    