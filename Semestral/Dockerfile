FROM php:8.2-apache

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Asegurar que Apache utilice solamente mpm_prefork
RUN a2dismod mpm_event 2>/dev/null || true \
    && a2dismod mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar el proyecto
COPY . .

# Instalar dependencias
RUN if [ -f composer.json ]; then \
        composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction; \
    fi

# Crear carpeta de sesiones y establecer permisos
RUN mkdir -p /var/www/html/storage/sessions \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage

# Configurar Apache para utilizar el puerto asignado por Railway
CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/<VirtualHost \\*:80>/<VirtualHost *:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
