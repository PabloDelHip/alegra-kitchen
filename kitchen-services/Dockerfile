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
    supervisor \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite sockets

# Instala Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Habilita mod_rewrite para Laravel
RUN a2enmod rewrite

# Cambia el DocumentRoot a /var/www/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|' /etc/apache2/sites-available/000-default.conf

# Establece el directorio de trabajo
WORKDIR /var/www

# ✅ Copia el script wait-for-it y el supervisord.conf antes de copiar el resto
COPY docker/wait-for-it.sh /var/www/docker/wait-for-it.sh
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ✅ Da permisos de ejecución y dueño correcto al script
RUN chmod 755 /var/www/docker/wait-for-it.sh \
    && chown www-data:www-data /var/www/docker/wait-for-it.sh

# ✅ Ahora copia el proyecto completo y el .env
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data .env.prod .env

# Instala dependencias PHP
RUN composer install --no-interaction --optimize-autoloader

# Ajusta permisos para storage y bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expone puerto para Apache
EXPOSE 80

# Ejecuta supervisord
CMD ["/usr/bin/supervisord"]
