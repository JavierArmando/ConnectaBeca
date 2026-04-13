FROM php:8.2-apache
WORKDIR /var/www/html
COPY . .

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Instalar dependencias del proyecto
RUN composer install --no-dev --optimize-autoloader

RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/html
RUN php artisan key:generate
RUN php artisan config:cache
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]