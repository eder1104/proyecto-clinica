#!/bin/bash

echo "🚀 Iniciando contenedor Laravel en Render..."

cd /var/www/html

# ── 1. Crear .env ────────────────────────────────────────────────────────
echo "📄 Creando .env..."
cp .env.example .env

# ── 2. Configurar SQLite (sin base de datos externa) ────────────────────
echo "🗄️  Configurando SQLite..."
SQLITE_PATH="/var/www/html/storage/database/database.sqlite"
mkdir -p /var/www/html/storage/database
touch "$SQLITE_PATH"

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

# ── 3. Configurar entorno producción y drivers ──────────────────────────
sed -i "s|^APP_ENV=.*|APP_ENV=production|g"     .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|g"      .env

if [ -n "${APP_URL:-}" ]; then
    sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|g" .env
fi

sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=file|g"      .env
sed -i "s|^CACHE_STORE=.*|CACHE_STORE=file|g"            .env
sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=sync|g"  .env

# ── 4. Permisos de carpetas antes de ejecutar artisan ────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# ── 5. Generar APP_KEY ───────────────────────────────────────────────────
echo "🔑 Generando APP_KEY..."
php artisan key:generate --force || true

# ── 6. Limpiar y refrescar cache de configuración ────────────────────────
echo "🧹 Preparando configuración..."
php artisan config:clear || true
php artisan cache:clear  || true
php artisan view:clear   || true
php artisan config:cache || true

# ── 7. Migraciones sobre SQLite ──────────────────────────────────────────
echo "📦 Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️ Migraciones ejecutadas con advertencias"

# ── 8. Iniciar Apache ────────────────────────────────────────────────────
echo "✅ Todo listo. Iniciando Apache..."
exec apache2-foreground
