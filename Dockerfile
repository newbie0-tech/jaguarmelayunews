# Image resmi PHP + Apache
FROM php:8.2-apache

# 1. Install ekstensi yang dibutuhkan
RUN set -ex \
    && docker-php-ext-install mysqli pdo pdo_mysql

# 2. Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# 3. Izinkan .htaccess & buka akses root
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/allowoverride.conf && \
    echo '    AllowOverride All' >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '    Require all granted' >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/allowoverride.conf && \
    a2enconf allowoverride

# 4. Salin source code ke web root
COPY . /var/www/html/

# (Opsional) atur owner & izin
# RUN chown -R www-data:www-data /var/www/html
