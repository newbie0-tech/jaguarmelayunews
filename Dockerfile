# Image resmi PHP + Apache
FROM php:8.2-apache

RUN set -ex \
    && docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

# Tambah blok <Directory> agar .htaccess dibaca
RUN echo '<Directory /var/www/html>' >  /etc/apache2/conf-available/allowoverride.conf && \
    echo '    AllowOverride All'      >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '    Require all granted'    >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '</Directory>'               >> /etc/apache2/conf-available/allowoverride.conf && \
    a2enconf allowoverride

COPY . /var/www/html/
