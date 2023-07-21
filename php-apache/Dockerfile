FROM php:8.2-apache
COPY php.ini /usr/local/etc/php/

# install packages
RUN apt-get update -y && \
    apt-get -y install --no-install-recommends \
        git \
        unzip \
        less \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        vim
# remove cache file
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# install PHP extensions
RUN docker-php-ext-install \
    intl \
    pdo_mysql \
    zip \
    bcmath

# install xdebug
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

# install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Laravelで必要になるmodRewriteを有効化する
RUN mv /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled
RUN /bin/sh -c a2enmod rewrite