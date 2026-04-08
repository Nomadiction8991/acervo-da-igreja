FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        default-mysql-client \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        pcntl \
        pdo_mysql \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/apache/000-default.conf.template /usr/local/share/apache2/000-default.conf.template
COPY docker/apache/ports.conf.template /usr/local/share/apache2/ports.conf.template
COPY docker/apache/servername.conf /etc/apache2/conf-available/servername.conf
COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint

RUN a2enmod rewrite \
    && a2enconf servername \
    && chmod +x /usr/local/bin/app-entrypoint

WORKDIR /var/www/html
EXPOSE 8000

ENTRYPOINT ["app-entrypoint"]
CMD ["apache2-foreground"]
