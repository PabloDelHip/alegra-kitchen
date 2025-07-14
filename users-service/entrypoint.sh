#!/bin/bash

echo "⏳ Esperando MySQL..."
/var/www/docker/wait-for-it.sh mysql:3306 --timeout=60 --strict -- echo "✅ MySQL listo"

echo "🚀 Ejecutando migraciones..."
php artisan migrate --force

echo "🎯 Iniciando Apache..."
exec apache2-foreground
