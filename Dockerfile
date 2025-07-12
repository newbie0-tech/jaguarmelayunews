FROM php:8.2-apache

# Install ekstensi PHP yang dibutuhkan
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Aktifkan Apache rewrite module
RUN a2enmod rewrite

# Atur document root ke /portal
ENV APACHE_DOCUMENT_ROOT=/var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

# Copy semua file dari project ke dalam container
COPY . /var/www/html/

# Buat folder uploads & assets/uploads jika belum ada
RUN mkdir -p /var/www/html/portal/uploads \
    && mkdir -p /var/www/html/portal/assets/uploads \
    && chmod -R 777 /var/www/html/portal/uploads \
    && chmod -R 777 /var/www/html/portal/assets/uploads

# Atur kepemilikan file/folder ke user Apache
RUN chown -R www-data:www-data /var/www/html

# Direktori kerja default
WORKDIR /var/www/html

# Buka port 80
EXPOSE 80

# (Opsional) Buat symlink volume storage
RUN ln -sfn /data/uploads /var/www/html/portal/uploads
RUN ln -sfn /data/assets /var/www/html/portal/assets/uploads

# Aktifkan konfigurasi agar .htaccess berjalan
RUN echo '<Directory /var/www/html/portal>' > /etc/apache2/conf-available/allowoverride.conf && \
    echo '    AllowOverride All' >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '    Require all granted' >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/allowoverride.conf && \
    a2enconf allowoverride
