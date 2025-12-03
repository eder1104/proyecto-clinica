set -euo pipefail

echo "Iniciando instalación del proyecto Laravel..."

composer install

echo "Creando y configurando entorno .env para MySQL..."
rm -f .env
cp .env.example .env

sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g' .env
sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/g' .env
sed -i 's/# DB_PORT=3306/DB_PORT=3306/g' .env
sed -i 's/DB_DATABASE=laravel/DB_DATABASE=clinica/g' .env
sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=clinica/g' .env
sed -i 's/# DB_USERNAME=root/DB_USERNAME=root/g' .env
sed -i 's/# DB_PASSWORD=/DB_PASSWORD=/g' .env

php artisan key:generate --force
php artisan config:clear
php artisan cache:clear

echo " Reiniciando db y cargando seeders"
php artisan migrate:fresh --seed --force

echo "✅ Instalación finalizada."
php artisan serve