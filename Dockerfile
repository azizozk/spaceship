FROM dunglas/frankenphp

RUN apt-get update && apt-get install -y --no-install-recommends \
	unzip \
	git \
	&& rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
	amqp \
	ctype \
	iconv \
	intl \
	mbstring \
	opcache \
	gd \
	pdo_pgsql \
	sockets \
	bcmath \
	xml

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY --link Caddyfile /etc/caddy/Caddyfile

WORKDIR /app

COPY --link app/ .

ENV APP_ENV=prod

RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction \
	&& composer dump-autoload --classmap-authoritative \
	&& composer run-script post-install-cmd
