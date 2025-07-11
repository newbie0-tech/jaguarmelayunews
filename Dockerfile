# Image resmi PHP + Apache
FROM php:8.2-apache

# Instal ekstensi dan aktifkan mod_rewrite
RUN docker-php-ext-install mysqli pdo pdo_mysql \
 && a2enmod rewrite

# Set dokumen root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

# --- copy source code ---
COPY . /var/www/html/

# --- Siapkan folder volume Railway di dalam container ---
ENV UPLOAD_DIR=/data/uploads

# Pastikan folder dan izin benar
RUN mkdir -p ${UPLOAD_DIR} \
 && chown -R www-data:www-data /data/uploads \
 && chmod -R 755 /data/uploads

# Buat symlink ke /portal/uploads agar bisa diakses dari URL
RUN ln -sfn /data/uploads /var/www/html/portal/uploads

# --- Izinkan .htaccess ---
RUN echo '<Directory /var/www/html/portal>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/allowoverride.conf

RUN a2enconf allowoverride
