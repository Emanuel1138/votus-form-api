# ---------- Stage 1: Composer ----------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --no-scripts \
    --no-autoloader

COPY . .

RUN composer dump-autoload \
    --optimize \
    --no-dev


# ---------- Stage 2: Aplicação ----------
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# Dependências do sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    gettext \
    libpng-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    gd \
    bcmath \
    opcache

# Copia aplicação + vendor
COPY --from=vendor /app /var/www/html

# Configurações
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh

# Permissões
RUN chmod +x /usr/local/bin/start.sh \
    && mkdir -p \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
        /run/nginx \
    && chown -R www-data:www-data \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]