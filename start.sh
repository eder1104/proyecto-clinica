#!/bin/bash

echo "🚀 Iniciando contenedor Laravel en Render..."

cd /var/www/html

# ── 1. Crear .env ────────────────────────────────────────────────────────
echo "📄 Creando .env..."
cp .env.example .env

# ── 2. Crear directorios de Storage y Framework ─────────────────────────
echo "📁 Creando estructura de directorios storage..."
mkdir -p /var/www/html/storage/database
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/bootstrap/cache

SQLITE_PATH="/var/www/html/storage/database/database.sqlite"
touch "$SQLITE_PATH"

# ── 3. Configurar SQLite y drivers en .env ───────────────────────────────
echo "🗄️  Configurando SQLite y variables de producción..."
sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|g"      .env
sed -i "s|^# DB_HOST=.*|# DB_HOST=|g"                    .env
sed -i "s|^# DB_PORT=.*|# DB_PORT=|g"                    .env
sed -i "s|^# DB_USERNAME=.*|# DB_USERNAME=|g"            .env
sed -i "s|^# DB_PASSWORD=.*|# DB_PASSWORD=|g"            .env

if grep -q "^DB_DATABASE=" .env; then
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${SQLITE_PATH}|g" .env
elif grep -q "^# DB_DATABASE=" .env; then
    sed -i "s|^# DB_DATABASE=.*|DB_DATABASE=${SQLITE_PATH}|g" .env
else
    echo "DB_DATABASE=${SQLITE_PATH}" >> .env
fi

sed -i "s|^APP_ENV=.*|APP_ENV=production|g"     .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|g"      .env

if [ -n "${APP_URL:-}" ]; then
    sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|g" .env
fi

sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=file|g"      .env
sed -i "s|^CACHE_STORE=.*|CACHE_STORE=file|g"            .env
sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=sync|g"  .env

# ── 4. Generar APP_KEY ───────────────────────────────────────────────────
echo "🔑 Generando APP_KEY..."
php artisan key:generate --force || true

# ── 5. Permisos de carpetas ─────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# ── 6. Limpiar y refrescar cache de configuración ────────────────────────
echo "🧹 Preparando configuración..."
php artisan config:clear || true
php artisan cache:clear  || true
php artisan view:clear   || true

# ── 7. Migraciones y Seeders ─────────────────────────────────────────────
echo "📦 Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️ Migraciones ejecutadas con advertencias"

echo "🌱 Ejecutando seeders..."
php artisan db:seed --force || echo "⚠️ Seeders ejecutados con advertencias"

# ── 8. Re-aplicar permisos tras migraciones/seeders ───────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Todo listo. Iniciando Apache..."
exec apache2-foreground
