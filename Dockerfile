FROM php:8.2-apache
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y git && rm -rf /var/lib/apt/lists/*
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN docker-php-ext-install pdo pdo_mysql
RUN composer install --no-dev --optimize-autoloader

RUN a2dismod mpm_prefork mpm_worker || true
RUN a2enmod mpm_event rewrite
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]