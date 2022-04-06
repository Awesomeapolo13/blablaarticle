FROM php:8.0-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libpq-dev \
        && docker-php-ext-install intl opcache pdo pdo_pgsql pgsql \
        && pecl install apcu \
        && docker-php-ext-enable apcu \
        && docker-php-ext-configure zip \
        && docker-php-ext-install zip

WORKDIR /var/www/blablaarticle

# установка composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# чтобы вызывать глобально из контейнера команду symfony
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Установка xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Переносим файл xdebug.ini
ADD deployment/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Создаем пользователя с домашним каталогом и оболочкой bash
RUN useradd -m -s /bin/bash apps
