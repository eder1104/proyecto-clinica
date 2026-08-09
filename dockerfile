# Imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# ── Configurar Apache: DocumentRoot → /public ───────────────────────────
COPY laravel.conf /etc/apache2/sites-available/laravel.conf
RUN a2dissite 000-default.conf && a2ensite laravel.conf

# ── Copiar código del proyecto ───────────────────────────────────────────
COPY . /var/www/html

WORKDIR /var/www/html

# ── Instalar Composer y dependencias PHP ─────────────────────────────────
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction

# ── Crear .env base desde .env.example (APP_KEY se genera en start.sh) ───
RUN cp .env.example .env

# ── Enlace simbólico de storage ──────────────────────────────────────────
RUN php artisan storage:link --no-interaction || true

# ── Permisos de escritura ────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── Script de arranque: genera key, migraciones, inicia Apache ───────────
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

# Usar start.sh como entrypoint (no CMD directo a apache)
ENTRYPOINT ["/start.sh"]

