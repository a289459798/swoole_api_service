FROM php:7.1.3-fpm
RUN pecl install swoole-2.0.7 \
    && docker-php-ext-enable swoole