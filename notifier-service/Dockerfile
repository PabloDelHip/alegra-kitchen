FROM php:8.2-cli

# Instala dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql sockets zip pcntl

# Instala Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia el código del proyecto
COPY . .

# Copia el entorno de producción
COPY .env.prod .env

# Copia el script que ejecuta ambos procesos
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Instala dependencias PHP sin entorno de desarrollo
RUN composer install --no-dev --optimize-autoloader \
    && chmod -R 755 storage bootstrap/cache

# Ejecuta el script que arranca Reverb y el listener
CMD ["/start.sh"]
