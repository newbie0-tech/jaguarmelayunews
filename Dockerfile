# Image resmi PHP + Apache
FROM php:8.2-apache

RUN set -ex \
 && docker-php-ext-install mysqli pdo pdo_mysql \
 && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

# --- copy source code ---
COPY . /var/www/html/

# --- siapkan volume uploads ---
# Railway Volume akan ter‑mount di /data
ENV UPLOAD_DIR=/data/uploads
RUN mkdir -p ${UPLOAD_DIR} \
 && chown -R www-data:www-data ${UPLOAD_DIR}
RUN mkdir -p /data && chown -R www-data:www-data /data
# Symlink agar URL publik tetap /portal/uploads/...
RUN ln -sfn ${UPLOAD_DIR} /var/www/html/portal/uploads

# --- izin .htaccess ---
RUN cat > /etc/apache2/conf-available/allowoverride.conf <<'EOF'
<Directory /var/www/html/portal>
    AllowOverride All
    Require all granted
</Directory>
EOF
RUN a2enconf allowoverride

