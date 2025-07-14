FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    sqlite3 \
    libsqlite3-dev \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Cambiar DocumentRoot a public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|' /etc/apache2/sites-available/000-default.conf

# Permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 storage bootstrap/cache

# Activa mod_rewrite
RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
