#!/bin/sh
set -e

# ================>>This script is for CentOS 9 Only

# Full paths to commands
# npm install
composer install

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

chown -R $USER:www-data /var/www/html/public
chmod -R 775 /var/www/html/public

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
