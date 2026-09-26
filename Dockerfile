# ---------- Stage 1: dependências do Composer ----------
FROM composer:2 AS vendor

WORKDIR /app

# Copia apenas os arquivos necessários para instalar as dependências
COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader

# Agora copia o restante do projeto
COPY . .

# Garante que o autoload esteja atualizado
RUN composer dump-autoload --optimize --no-dev


# ---------- Stage 2: aplicação ----------
FROM php:8.3-fpm-alpine

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

# Copia a aplicação com as dependências do Composer
COPY --from=vendor /app /var/www/html

# Copia configurações do Docker
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh

# Permissões e preparação dos diretórios do Laravel
RUN chmod +x /usr/local/bin/start.sh \
    && mkdir -p /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /run/nginx \
    && chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Render fornece a porta através da variável PORT
EXPOSE 8080

# Inicialização
CMD ["/usr/local/bin/start.sh"]