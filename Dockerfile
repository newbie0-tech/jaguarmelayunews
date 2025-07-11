FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

COPY . /var/www/html/

ENV UPLOAD_DIR=/data/uploads
RUN mkdir -p /var/www/html/portal/data \
 && chown -R www-data:www-data /var/www/html/portal/data \
 && chmod -R 775 /var/www/html/portal/data
chown -R www-data:www-data /var/www/html/portal/uploads

RUN ln -sfn /data/uploads /var/www/html/portal/uploads

# ⬇ Fix: Tulis <Directory> menggunakan echo baris per baris
RUN echo '<Directory /var/www/html/portal>' > /etc/apache2/conf-available/allowoverride.conf && \
    echo '    AllowOverride All' >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '    Require all granted' >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/allowoverride.conf

RUN a2enconf allowoverride
