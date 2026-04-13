FROM php:8.2-apache
WORKDIR /var/www/html

# Instalar Git y Composer
RUN apt-get update && apt-get install -y git && rm -rf /var/lib/apt/lists/*
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN if [ ! -f .env ]; then cp .env.example .env; fi

COPY . .
RUN docker-php-ext-install pdo pdo_mysql
RUN composer install --no-dev --optimize-autoloader

RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/html
RUN php artisan key:generate
RUN php artisan config:cache
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]