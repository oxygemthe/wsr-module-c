FROM php:8.2.7-fpm

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
        git \
        file \
        libcurl4-gnutls-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libgmp-dev \
		libmagickwand-dev \
        libmcrypt-dev\
        libmhash-dev \
        libpng-dev \
        libxml2-dev \
		libzip-dev \
        re2c \
        zlib1g-dev \
        zip \
        curl \
        unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR /var/www

USER $user

EXPOSE 9000
CMD ["php-fpm"]