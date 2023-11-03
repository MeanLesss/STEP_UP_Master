FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --fix-missing \
    libonig-dev \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install exif
RUN docker-php-ext-install pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY . /var/www/html

#COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www:www . /var/www/html

USER root
# RUN chown -R www:www /var/www/html
COPY start.sh /start.sh
RUN chmod +x /start.sh
USER www

EXPOSE 9000

CMD ["/start.sh"]
