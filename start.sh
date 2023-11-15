#!/bin/sh
# npm install
composer install

# chown -R www-data:$USER /var/www/html

# Check if the directories exist before changing their ownership and permissions

if [ -d "storage" ]; then \
    chown -R $USER:www-data storage && \
    chmod -R 775 storage; \
fi

if [ -d "bootstrap/cache" ]; then \
    chown -R $USER:www-data bootstrap/cache && \
    chmod -R 775 bootstrap/cache; \
fi

if [ -d "public" ]; then \
    php artisan storage:link
fi

php artisan config:clear
php artisan cache:clear


# Exit the script
exit 0
# php-fpm
