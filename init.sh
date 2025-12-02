#!/bin/bash
set -euo pipefail

echo "🚀 Iniciando instalación del proyecto Laravel..."

if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist
else
    echo "📦 'vendor/' ya existe. Saltando composer install."
fi

echo "🔧 Regenerando archivo .env desde cero..."
rm -f .env
cp .env.example .env

echo "⚙️ Configurando base de datos (MySQL)..."
sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g' .env
sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/g' .env
sed -i 's/# DB_PORT=3306/DB_PORT=3306/g' .env
sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=clinica/g' .env
sed -i 's/DB_DATABASE=laravel/DB_DATABASE=clinica/g' .env
sed -i 's/# DB_USERNAME=root/DB_USERNAME=root/g' .env
sed -i 's/# DB_PASSWORD=/DB_PASSWORD=/g' .env

echo "🔑 Generando clave de la aplicación..."
php artisan key:generate --force

echo "🧹 Limpiando cachés del sistema..."
php artisan config:clear
php artisan cache:clear

echo "🗄️  Reiniciando base de datos y cargando datos de prueba..."
php artisan migrate:fresh --seed --force

echo ""
echo "✔️  Instalación finalizada correctamente."
echo "⚠️  NOTA: Asegúrate de haber subido la carpeta 'public/build' al repositorio."
echo "👉 Para iniciar el servidor:"
echo "   php artisan serve"