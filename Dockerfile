# Image resmi PHP + Apache
FROM php:8.2-apache

# 1. Ekstensi PHP
RUN set -ex && docker-php-ext-install mysqli pdo pdo_mysql

# 2. Aktifkan mod_rewrite
RUN a2enmod rewrite

# 3. Ubah DocumentRoot → /var/www/html/portal
ENV APACHE_DOCUMENT_ROOT /var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

# 4. Izinkan .htaccess di DocumentRoot baru
RUN echo '<Directory /var/www/html/portal>' >  /etc/apache2/conf-available/allowoverride.conf && \
    echo '    AllowOverride All'           >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '    Require all granted'         >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '</Directory>'                    >> /etc/apache2/conf-available/allowoverride.conf && \
    a2enconf allowoverride

# 5. Salin source code (repo) → /var/www/html
COPY . /var/www/html/

