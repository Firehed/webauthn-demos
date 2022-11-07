FROM composer:2 AS deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev

FROM php:8.1-cli-alpine AS server
ENV BUILTIN_EXTENSIONS="opcache"
RUN docker-php-ext-install $BUILTIN_EXTENSIONS
ENV PECL_EXTENSIONS="apcu"
RUN apk add --update --no-cache --virtual .build-deps $PHPIZE_DEPS \
  && pecl install $PECL_EXTENSIONS \
  && pecl clear-cache \
  && docker-php-ext-enable $PECL_EXTENSIONS \
  && apk del .build-deps
WORKDIR /app
COPY --from=deps /app/vendor/ ./vendor
COPY . .
ENV PORT=8000
ENV HOST=http://localhost:$PORT
CMD php -S 0.0.0.0:$PORT public/index.php
