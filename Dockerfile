FROM php:8.2-apache
WORKDIR /var/www/html
COPY . .
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/html
RUN php artisan key:generate
RUN php artisan config:cache
RUN chmod -R 777 storage bootstrap/cache
EXPOSE 80
CMD ["apache2-foreground"]