# Use a specific version of the image
FROM php:8.2.11-fpm-alpine

# Install composer
RUN echo "\e[1;33mInstall COMPOSER\e[0m" \
    && cd /tmp \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && docker-php-ext-install pdo pdo_mysql \
    && apk update

# Install useful tools
RUN apk add --no-cache nano wget dialog vim

# Install important libraries
RUN echo "\e[1;33mInstall important libraries\e[0m" \
    && apk add --no-cache \
    build-base \
    git \
    curl \
    zlib-dev \
    zip \
    bzip2-dev \
    libmcrypt-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev

# Copy existing application directory
COPY . /var/www

# Run as non-root user
USER www-data


# RUN chmod +x /var/www/start.sh

# CMD ["/var/www/start.sh"]

# RUN echo "\e[1;33mInstall important docker dependencies\e[0m"
# RUN docker-php-ext-install \
#     exif \
#     pcntl \
#     bcmath \
#     ctype \
#     curl \
#     iconv \
#     xml \
#     soap \
#     pcntl \
#     mbstring \
#     tokenizer \
#     bz2 \
#     zip \
#     intl

# Install Postgre PDO
# RUN apt-get install -y libpq-dev \
#     && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
#     && docker-php-ext-install pdo pdo_pgsql pgsql
