set -euo pipefail

echo "🚀 Iniciando instalación del proyecto Laravel..."

if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist
else
    echo "📦 'vendor/' ya existe. Saltando composer install."
fi

if [ ! -f ".env" ]; then
    echo "🔧 Creando archivo .env..."
    cp .env.example .env
else
    echo "🔧 Archivo .env ya existe. No se sobrescribirá."
fi

echo "🔑 Generando clave de la aplicación..."
php artisan key:generate --force

echo "🗄️  Reiniciando base de datos y cargando datos de prueba..."
php artisan migrate:fresh --seed --force
