FROM php:8.2-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    vim \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN addgroup -g 1000 www && adduser -u 1000 -G www -s /bin/sh -D www

COPY . /var/www

RUN chown -R www:www /var/www && chmod -R 755 /var/www

USER www

EXPOSE 9000

CMD ["php-fpm"]

