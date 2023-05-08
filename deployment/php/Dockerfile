FROM php:8.1.0-fpm-alpine

# Install packages
# --no-cache - for the fast packeges installation
RUN apk add --no-cache curl git build-base zlib-dev oniguruma-dev autoconf bash

# Если аргумент INSTALL_XDEBUG будет равен true, тоустанавливаем xdebug (теперь можно исп. этот файл и для прод. деплоя)
ARG INSTALL_XDEBUG=false
# Подтягиваем зависимости для xdebug и включаем расширение xdebug в php.ini
RUN if [ ${INSTALL_XDEBUG} = true ];  \
    then \
      pecl install xdebug && docker-php-ext-enable xdebug; \
    fi;
# Пробрасываем xdebug.ini в контейнер php
COPY ./deployment/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Postgres
RUN apk add --no-cache libpq-dev && docker-php-ext-install pdo_pgsql

## Configure non-root user
ARG PUID=1000
ARG PGID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

# Source project
RUN mkdir /var/www/blablaarticle
RUN chown www-data:www-data /var/www/blablaarticle
COPY --chown=www-data:www-data ./ /var/www/blablaarticle
WORKDIR /var/www/blablaarticle

USER www-data

# Composer instalation
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD php-fpm

EXPOSE 9000
