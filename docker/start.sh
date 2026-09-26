#!/usr/bin/env bash

set -e

# Render fornece a porta através da variável PORT.
# Usa 8080 apenas como fallback local.
export PORT="${PORT:-8080}"

echo "Starting Laravel application on port ${PORT}..."

# Gera a configuração final do Nginx
envsubst '${PORT}' \
    < /etc/nginx/nginx.conf.template \
    > /etc/nginx/nginx.conf

# Garante permissões corretas
chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Executa migrations
php artisan migrate --force

# Cache de configurações
php artisan config:cache

# Cache de rotas
php artisan route:cache

echo "Starting PHP-FPM and Nginx..."

# Inicia Supervisor
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf