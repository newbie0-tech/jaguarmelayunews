# Image resmi PHP + Apache
FROM php:8.2-apache

# 1. Install ekstensi PHP yang diperlukan
RUN set -ex \
    && docker-php-ext-install mysqli pdo pdo_mysql

# 2. Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# 3. Tentukan DocumentRoot → /var/www/html/portal
ENV APACHE_DOCUMENT_ROOT=/var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

# 4. Salin source code ke container
COPY . /var/www/html/

# 5. Folder uploads — buat & ubah owner supaya Apache bisa menulis
RUN mkdir -p /var/www/html/portal/uploads \
/var/www/html/portal/assets/ads \
 && chown -R www-data:www-data /var/www/html/portal/uploads

# 6. Izinkan .htaccess di DocumentRoot baru
RUN echo '<Directory /var/www/html/portal>'  > /etc/apache2/conf-available/allowoverride.conf \
 && echo '    AllowOverride All'            >> /etc/apache2/conf-available/allowoverride.conf \
 && echo '    Require all granted'          >> /etc/apache2/conf-available/allowoverride.conf \
 && echo '</Directory>'                     >> /etc/apache2/conf-available/allowoverride.conf \
 && a2enconf allowoverride
