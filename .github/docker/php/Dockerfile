FROM php:8.3-fpm-bookworm

RUN apt-get update \
    && apt-get install -y build-essential zlib1g-dev default-mysql-client curl gnupg procps vim git unzip libzip-dev libpq-dev libicu-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get update \
    && apt-get install -y \
    libicu-dev \
    && docker-php-ext-install -j$(nproc) \
    intl \
    && apt-get purge -y \
    libicu-dev

RUN apt-get update \
    && apt-get install -y \
    libc-client-dev \
    libkrb5-dev && \
    docker-php-ext-configure imap \
    --with-kerberos \
    --with-imap-ssl \
    && docker-php-ext-install -j$(nproc) \
    imap \
    && apt-get purge -y \
    libc-client-dev \
    libkrb5-dev

RUN apt-get update \
    && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) \
    zip \
    && apt-get purge -y \
    libzip-dev

RUN docker-php-ext-install -j$(nproc) sockets
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install bcmath 
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable mysqli mbstring bcmath pdo pdo_mysql

ADD ./docker/php/www.conf /usr/local/etc/php-fpm.d/

RUN apt update && apt upgrade -y && apt install -y git
RUN git clone https://github.com/MythicalLTD/Framework.git /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-suggest

USER root