FROM php:7.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends git zip

RUN curl -sS https://getcomposer.org/installer | php -- --version 2.0.7 --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /var/www

WORKDIR /var/www
