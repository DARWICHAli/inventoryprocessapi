# Static Nginx PHP server
FROM php:apache

# Install required extensions to connect to the database
RUN docker-php-source extract \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-source delete


COPY ./static /var/www/html
COPY ./vendor ./vendor

EXPOSE 80
