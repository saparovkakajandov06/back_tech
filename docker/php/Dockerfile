FROM php:8.0-fpm

RUN apt-get update && apt-get install -y \
    supervisor \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    iputils-ping \
    nano \
    procps \
    libpq-dev \
    whois

RUN docker-php-ext-install pdo_mysql \
    mbstring exif pcntl bcmath gd

# Install Postgres PDO
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN apt-get install -y libmemcached-dev zlib1g-dev \
    && pecl install memcached-3.1.4 \
    && docker-php-ext-enable memcached

RUN apt install -y --no-install-recommends ntp

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./local.ini /usr/local/etc/php/conf.d/local.ini
COPY ./www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www
