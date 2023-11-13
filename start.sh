#!/bin/sh
# npm install
composer install

chown -R www-data:www-data /var/www

# Check if the directories exist before changing their ownership and permissions
if [ -d "storage" ]; then \
    chown -R www-data:www-data storage && \
    chmod -R 775 storage; \
fi
if [ -d "bootstrap/cache" ]; then \
    chown -R www-data:www-data bootstrap/cache && \
    chmod -R 775 bootstrap/cache; \
fi

# Exit the script
exit 0
# php-fpm
