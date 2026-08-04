FROM php:8.4-apache

# Instal dependensi sistem dan ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql zip

# --- TAMBAHKAN BARIS INI UNTUK MENGINSTALL COMPOSER ---
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# -----------------------------------------------------

# Mengaktifkan Apache mod_rewrite untuk routing Laravel
RUN a2enmod rewrite

# Mengubah DocumentRoot Apache agar mengarah ke folder public/ Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Salin script entrypoint dan berikan izin eksekusi
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]