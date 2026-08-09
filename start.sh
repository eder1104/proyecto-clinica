#!/bin/bash

echo "🚀 Iniciando contenedor Laravel en Render..."

cd /var/www/html

# ── 1. Forzar variables de entorno para SQLite (anular variables erróneas de Render) ──
unset DB_HOST DB_PORT DB_USERNAME DB_PASSWORD DB_URL
export DB_CONNECTION=sqlite
export DB_DATABASE="/var/www/html/storage/database/database.sqlite"
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync

# ── 2. Crear .env ────────────────────────────────────────────────────────
echo "📄 Creando .env..."
cp .env.example .env

# ── 3. Crear directorios de Storage y Framework ─────────────────────────
echo "📁 Creando estructura de directorios storage..."
mkdir -p /var/www/html/storage/database
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/bootstrap/cache

touch "$DB_DATABASE"

# ── 4. Configurar .env ───────────────────────────────────────────────────
sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|g"      .env
sed -i "s|^# DB_HOST=.*|# DB_HOST=|g"                    .env
sed -i "s|^# DB_PORT=.*|# DB_PORT=|g"                    .env
sed -i "s|^# DB_USERNAME=.*|# DB_USERNAME=|g"            .env
sed -i "s|^# DB_PASSWORD=.*|# DB_PASSWORD=|g"            .env

if grep -q "^DB_DATABASE=" .env; then
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|g" .env
else
    echo "DB_DATABASE=${DB_DATABASE}" >> .env
fi

sed -i "s|^APP_ENV=.*|APP_ENV=production|g"     .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|g"      .env

if [ -n "${APP_URL:-}" ]; then
    sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|g" .env
fi

sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=file|g"      .env
sed -i "s|^CACHE_STORE=.*|CACHE_STORE=file|g"            .env
sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=sync|g"  .env

# ── 5. Permisos de carpetas ─────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# ── 6. Generar APP_KEY ───────────────────────────────────────────────────
echo "🔑 Generando APP_KEY..."
php artisan key:generate --force || true

# ── 7. Limpiar cache previo ──────────────────────────────────────────────
echo "🧹 Limpiando caché previo..."
php artisan config:clear || true
php artisan cache:clear  || true
php artisan view:clear   || true

# ── 8. Migraciones y Seeders sobre SQLite ────────────────────────────────
echo "📦 Ejecutando migraciones..."
php artisan migrate --force

echo "🌱 Ejecutando seeders..."
php artisan db:seed --force

# ── 9. Cachear configuración final para bloquear SQLite en producción ────
echo "⚡ Cacheando configuración para producción..."
php artisan config:cache || true

# ── 10. Permisos finales ─────────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Todo listo. Iniciando Apache..."
exec apache2-foreground
