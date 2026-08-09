#!/bin/bash
set -e

echo "🚀 Iniciando contenedor Laravel en Render..."

# ── 1. Crear .env si no existe ──────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    echo "📄 Creando .env desde .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# ── 2. Inyectar variables de entorno de Render en .env ──────────────────
# Render pasa las variables como env vars del sistema → las escribimos en .env
# para que Laravel las lea correctamente desde el archivo.

inject_env() {
    local key=$1
    local value="${!key:-}"
    if [ -n "$value" ]; then
        # Si la key ya existe en .env la reemplaza, si no la agrega
        if grep -q "^${key}=" /var/www/html/.env; then
            sed -i "s|^${key}=.*|${key}=${value}|" /var/www/html/.env
        else
            echo "${key}=${value}" >> /var/www/html/.env
        fi
    fi
}

inject_env APP_ENV
inject_env APP_URL
inject_env APP_DEBUG
inject_env DB_CONNECTION
inject_env DB_HOST
inject_env DB_PORT
inject_env DB_DATABASE
inject_env DB_USERNAME
inject_env DB_PASSWORD
inject_env SESSION_DRIVER
inject_env CACHE_STORE
inject_env QUEUE_CONNECTION
inject_env REDIS_HOST
inject_env REDIS_PORT
inject_env REDIS_PASSWORD

# Asegurar producción si no está seteado
if ! grep -q "^APP_ENV=" /var/www/html/.env; then
    echo "APP_ENV=production" >> /var/www/html/.env
fi
sed -i "s|^APP_ENV=local|APP_ENV=production|g" /var/www/html/.env
sed -i "s|^APP_DEBUG=true|APP_DEBUG=false|g"   /var/www/html/.env

# ── 3. Generar APP_KEY si está vacío ────────────────────────────────────
if grep -q "^APP_KEY=$" /var/www/html/.env || ! grep -q "^APP_KEY=" /var/www/html/.env; then
    echo "🔑 Generando APP_KEY..."
    php /var/www/html/artisan key:generate --force
fi

# ── 4. Limpiar caches de configuración ──────────────────────────────────
echo "🧹 Limpiando caches..."
php /var/www/html/artisan config:clear  || true
php /var/www/html/artisan cache:clear   || true
php /var/www/html/artisan view:clear    || true

# ── 5. Optimizar para producción ────────────────────────────────────────
echo "⚡ Optimizando..."
php /var/www/html/artisan config:cache  || true
php /var/www/html/artisan route:cache   || true

# ── 6. Migraciones (solo si hay DB configurada) ─────────────────────────
DB_HOST_VAL=$(grep "^DB_HOST=" /var/www/html/.env | cut -d= -f2 | tr -d '"')
if [ -n "$DB_HOST_VAL" ] && [ "$DB_HOST_VAL" != "127.0.0.1" ] || [ -n "${DB_HOST:-}" ]; then
    echo "🗄️  Ejecutando migraciones..."
    php /var/www/html/artisan migrate --force || echo "⚠️  Migraciones fallaron (puede que ya existan)"
fi

# ── 7. Permisos finales ─────────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Listo. Iniciando Apache..."
exec apache2-foreground
