FROM php:8.2-apache

# Install ekstensi mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Ubah root ke folder /portal
ENV APACHE_DOCUMENT_ROOT=/var/www/html/portal
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

# Copy semua file dari repo ke dalam container
COPY . /var/www/html/

# Buat folder uploads & beri izin penuh
RUN mkdir -p /var/www/html/portal/uploads && chmod -R 777 /var/www/html/portal/uploads
RUN mkdir -p /var/www/html/portal/assets/uploads && chmod -R 777 /var/www/html/portal/assets/uploads

# Atur hak milik ke www-data (user Apache)
RUN chown -R www-data:www-data /var/www/html

# Atur direktori kerja
WORKDIR /var/www/html

# Fix kesalahan ketik: EXSPOSE → EXPOSE
EXPOSE 80

# (Opsional) Simbolik link ke volume data
RUN ln -sfn /data/uploads /var/www/html/portal/uploads
RUN ln -sfn /data/assets /var/www/html/portal/assets/uploads

# Tambahkan konfigurasi agar AllowOverride All aktif (htaccess bisa jalan)
RUN echo '<Directory /var/www/html/portal>' > /etc/apache2/conf-available/allowoverride.conf && \
    echo '    AllowOverride All' >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '    Require all granted' >> /etc/apache2/conf-available/allowoverride.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/allowoverride.conf

RUN a2enconf allowoverride
