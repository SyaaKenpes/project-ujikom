#!/bin/sh

# Memastikan izin akses folder storage & bootstrap/cache selalu benar saat container start
mkdir -p /var/www/html/storage/framework/views \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/cache \
        /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Jalankan perintah utama Apache
exec "$@"