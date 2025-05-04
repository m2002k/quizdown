FROM php:8.4.6-apache

# Install PostgreSQL PDO extension
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite


COPY . /var/www/html/
WORKDIR /var/www/html
EXPOSE 80
USER www-data