#!/bin/sh
echo "[Entrypoint] Ejecutando composer install..."
if [ -f /var/www/html/composer.json ]; then
    composer install --no-interaction
else
    echo "composer.json no encontrado. Saltando install."
fi

echo "[Entrypoint] Ejecutando artisan migrate..."
if [ -f /var/www/html/artisan ]; then
    php artisan migrate --force || echo "Migración falló o no necesaria"
fi

exec apache2-foreground
