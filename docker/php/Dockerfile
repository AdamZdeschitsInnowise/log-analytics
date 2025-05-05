FROM php:8.4-cli

ARG UID=1000

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN set -xeu pipefail \
    && usermod -u "${UID}" www-data \
    && groupmod -g "${UID}" www-data \
    && mkdir /app && chown www-data:www-data /app
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install mysqli pdo pdo_mysql zip

WORKDIR /app

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "/app/public", "/app/public/index.php"]
