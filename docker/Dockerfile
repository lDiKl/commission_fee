FROM php:8.2-fpm-bullseye

MAINTAINER Dmytro Kostynenko <dik.work@gmail.com>

# Create a group and user
ARG APP_ID=1000
RUN groupadd --gid "$APP_ID" app
RUN useradd -ms /bin/bash -l -u "$APP_ID" -g root app

RUN apt-get update && apt-get install -y libmcrypt-dev git --no-install-recommends \
    zlib1g-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install bcmath zip

# Install Composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --no-scripts --ansi --version --no-interaction

USER app:app

WORKDIR /var/www/html
