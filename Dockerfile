FROM php:8.2-apache
RUN set -ex \ && docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite
COPY . /var/www/html/
