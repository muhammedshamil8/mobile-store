FROM php:8.1-apache
RUN apt-get update && apt-get install -y  \
curl  \
g++  \
git \
libbz2-dev \
libpq-dev \
libfreetype6-dev \
libicu-dev \
libjpeg-dev \
libonig-dev \
libzip-dev \
libmcrypt-dev \
libpng-dev \
libreadline-dev \
sudo \
unzip \
zip  && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
bcmath \
bz2 \
calendar \
iconv \
pdo \
pdo_pgsql \
intl \
mbstring \
opcache \
pdo_mysql \
zip exif

RUN docker-php-ext-enable \
bcmath \
bz2 \
calendar \
iconv \
pdo \
pdo_pgsql \
intl \
mbstring \
opcache \
pdo_mysql \
zip exif

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg

RUN docker-php-ext-install gd

RUN apt-get update && apt-get upgrade -y

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
# COPY conf/custom.ini /usr/local/etc/php/conf.d/custom.ini


