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


echo "🗄️ Ejecutando migraciones y seeders..."
php artisan migrate --seed --force

echo "🔗 Creando storage link..."
php artisan storage:link || true


if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias de NPM..."
    npm install
else
    echo "📦 'node_modules/' ya existe. Saltando npm install."
fi

echo "✔️ Todo listo. Ahora puedes ejecutar:"
echo "👉 npm run dev"
