set -euo pipefail

echo "🚀 Iniciando instalación del proyecto..."

composer install

php artisan migrate:fresh --seed

php artisan serve