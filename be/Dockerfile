FROM composer:2 AS deps
WORKDIR /app
COPY composer.json composer.lock .
RUN composer install --no-dev

FROM php:8.1-cli-alpine AS server
WORKDIR /app
COPY --from=deps /app/vendor/ ./vendor
COPY . .
ENV PORT=8000
ENV HOST=http://localhost:$PORT
CMD php -S 0.0.0.0:$PORT public/index.php
