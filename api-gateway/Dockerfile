FROM php:8.2-apache

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite sockets

# Instala Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Activa mod_rewrite para Laravel
RUN a2enmod rewrite

# Cambia el DocumentRoot a /var/www/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|' /etc/apache2/sites-available/000-default.conf

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia el código del proyecto
COPY . .

# Copia archivo de entorno
COPY .env.prod .env

# Instala dependencias PHP
RUN composer install --no-interaction --optimize-autoloader

# Permisos adecuados
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Exponer puerto 80 para Apache
EXPOSE 80

# Iniciar Apache en primer plano
CMD ["apache2-foreground"]
