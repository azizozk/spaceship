FROM php:8.3-fpm-alpine

# ─── System dependencies ───────────────────────────────────────────────────
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    curl \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    rabbitmq-c-dev \
    autoconf \
    g++ \
    make

# ─── PHP extensions ────────────────────────────────────────────────────────
RUN docker-php-ext-install \
    intl \
    zip \
    mbstring \
    opcache \
    sockets

# AMQP extension (required for Symfony Messenger + RabbitMQ)
RUN pecl install amqp && docker-php-ext-enable amqp

# ─── OPcache config ────────────────────────────────────────────────────────
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.memory_consumption=192'; \
    echo 'opcache.max_wasted_percentage=10'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# ─── PHP-FPM config ────────────────────────────────────────────────────────
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf

# ─── Composer ──────────────────────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ─── Symfony CLI (optional but handy) ─────────────────────────────────────
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add --no-cache symfony-cli

# ─── Working directory ─────────────────────────────────────────────────────
WORKDIR /var/www/app

# ─── Entrypoint ────────────────────────────────────────────────────────────
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
