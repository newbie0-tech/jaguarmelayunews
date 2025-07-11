# Gunakan image PHP dengan Apache
FROM php:8.2-apache

# Install ekstensi PHP dan aktifkan mod_rewrite
RUN docker-php-ext-install mysqli pdo pdo_mysql && a2enmod rewrite

# Set dokumen root ke dalam folder /portal
ENV APACHE_DOCUMENT_ROOT=/var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

# Salin semua source code ke dalam container
COPY . /var/www/html/

# Siapkan folder volume Railway di dalam container
ENV UPLOAD_DIR=/data/uploads

# Buat folder uploads dan beri izin akses
RUN mkdir -p ${UPLOAD_DIR} \
 && chown -R www-data:www-data /data/uploads \
 && chmod -R 755 /data/uploads

# Symlink agar URL /portal/uploads bisa digunakan
RUN ln -sfn /data/uploads /var/www/html/portal/uploads

# Aktifkan .htaccess
RUN bash -c "cat > /etc/apache2/conf-available/allowoverride.conf <<EOF
<Directory /var/www/html/portal>
    AllowOverride All
    Require all granted
</Directory>
EOF"

RUN a2enconf allowoverride
