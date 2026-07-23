FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar el proyecto al directorio público de Apache
COPY . /var/www/html/

# Permisos para carpetas que necesiten escritura
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Railway proporciona el puerto mediante la variable PORT
CMD sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf \
    && sed -i "s/:80>/:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf \
    && apache2-foreground
