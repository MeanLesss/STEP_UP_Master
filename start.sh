#!/bin/sh
set -e

# Full paths to commands
/usr/local/bin/npm install
/usr/local/bin/composer install

# Check if the directories exist before changing their ownership and permissions
if [ -d "/var/www/html/storage" ]; then
    chown -R $USER:www-data /var/www/html/storage && \
    chmod -R 775 /var/www/html/storage
fi

if [ -d "/var/www/html/bootstrap/cache" ]; then
    chown -R $USER:www-data /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/bootstrap/cache
fi

if [ -d "/var/www/html/public" ]; then
    /usr/local/bin/php artisan storage:link
fi

/usr/local/bin/php artisan config:clear
/usr/local/bin/php artisan cache:clear

# Remove kinsing and kdevtmpfsi files
find / -iname kinsing* -exec rm -fv {} \;
find / -iname kdevtmpfsi* -exec rm -fv {} \;

# Remove libsystem.so
if [ -f "/tmp/libsystem.so" ]; then
    chattr -i /tmp/libsystem.so
    chattr -a /tmp/libsystem.so
    rm -r /tmp/libsystem.so
fi

# Exit the script
exit 0
