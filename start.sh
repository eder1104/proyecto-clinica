#!/bin/bash
set -e

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

# Apuntar DB_DATABASE al archivo sqlite
if grep -q "^DB_DATABASE=" .env; then
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${SQLITE_PATH}|g" .env
elif grep -q "^# DB_DATABASE=" .env; then
    sed -i "s|^# DB_DATABASE=.*|DB_DATABASE=${SQLITE_PATH}|g" .env
else
    echo "DB_DATABASE=${SQLITE_PATH}" >> .env
fi

# ── 3. Configurar entorno producción ────────────────────────────────────
sed -i "s|^APP_ENV=.*|APP_ENV=production|g"     .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|g"      .env

# Inyectar APP_URL si viene de Render
if [ -n "${APP_URL:-}" ]; then
    sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|g" .env
fi

# Usar drivers file/sync (no requieren Redis ni DB)
sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=file|g"      .env
sed -i "s|^CACHE_STORE=.*|CACHE_STORE=file|g"            .env
sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=sync|g"  .env

# ── 4. Generar APP_KEY ───────────────────────────────────────────────────
echo "🔑 Generando APP_KEY..."
php artisan key:generate --force

# ── 5. Limpiar y cachear config ──────────────────────────────────────────
echo "🧹 Preparando configuración..."
php artisan config:clear || true
php artisan cache:clear  || true
php artisan view:clear   || true
php artisan config:cache || true

# ── 6. Migraciones sobre SQLite ──────────────────────────────────────────
echo "📦 Ejecutando migraciones..."
php artisan migrate --force

# ── 7. Permisos finales ──────────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Todo listo. Iniciando Apache..."
exec apache2-foreground
