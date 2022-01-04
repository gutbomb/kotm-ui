FROM php:7.4-apache
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
RUN apt-get update
RUN apt-get install -y apt-utils
RUN apt-get install -y \
        apt-utils \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        zlib1g-dev \
        libicu-dev \
        g++ \
        libpq-dev \
        libssl-dev \
        gettext \
        libgmp-dev \
        re2c \
        libmhash-dev \
        libmcrypt-dev \
        file \
        libmagickwand-dev \
        libc-client-dev \
        libkrb5-dev \
        libzip-dev \
        zip
RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/
RUN echo extension=gettext.so > /usr/local/etc/php/conf.d/gettext.ini
RUN rm -r /var/lib/apt/lists/*
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-configure intl
RUN docker-php-ext-configure gmp
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-configure zip
RUN docker-php-ext-install \
        intl \
        gd \
        gettext \
        exif \
        gmp \
        imap \
        mysqli \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        soap \
        sockets \
        xmlrpc \
        zip
RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN a2enmod rewrite