FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

COPY . /var/www/html/

ENV UPLOAD_DIR=/data/uploads

RUN bash -c "cat > /etc/apache2/conf-available/allowoverride.conf <<EOF
<Directory /var/www/html/portal>
    AllowOverride All
    Require all granted
</Directory>
EOF"


RUN a2enconf allowoverride
