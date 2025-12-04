#!/bin/bash
set -euo pipefail

echo "🚀 Iniciando instalación del proyecto historias clinicas"

if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist
else
    echo "📦 'vendor/' ya existe. Saltando composer install."
fi

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

sed -i 's/SESSION_DRIVER=database/SESSION_DRIVER=file/g' .env

php artisan key:generate --force
php artisan config:clear
php artisan cache:clear

echo "🗄️ Ejecutando migraciones..."
php artisan migrate:fresh --seed --force

echo "✅ Instalación completada correctamente."