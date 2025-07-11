# Image resmi PHP + Apache
FROM php:8.2-apache

RUN set -ex \
 && docker-php-ext-install mysqli pdo pdo_mysql \
 && a2enmod rewrite

# Set root ke /portal
ENV APACHE_DOCUMENT_ROOT=/var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

# --- Copy source code ---
COPY . /var/www/html/

# --- Siapkan volume uploads ---
ENV UPLOAD_DIR=/data/uploads

# Railway mount: /data → symlink ke /portal/uploads
RUN mkdir -p ${UPLOAD_DIR} \
    && chown -R www-data:www-data ${UPLOAD_DIR} \
    && chmod -R 755 ${UPLOAD_DIR} \
    && ln -sfn ${UPLOAD_DIR} /var/www/html/portal/uploads

# --- Izin .htaccess ---
RUN cat > /etc/apache2/conf-available/allowoverride.conf <<'EOF'
<Directory /var/www/html/portal>
    AllowOverride All
    Require all granted
</Directory>
EOF
RUN a2enconf allowoverride
