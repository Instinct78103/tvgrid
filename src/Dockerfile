FROM php:8.0.5-fpm-alpine

# RUN apt-get update && apt-get install -y
RUN docker-php-source extract && docker-php-source delete && docker-php-ext-install mysqli

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chmod -R 0777 /var/www