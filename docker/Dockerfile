# syntax=docker/dockerfile:1

#############################################
# Base PHP-FPM
#############################################
FROM php:8.4-fpm-alpine AS php-base

RUN apk add --no-cache \
    git \
    icu-dev \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install \
        intl \
        opcache \
        pdo \
        pdo_mysql \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

#############################################
# Dépendances Composer (prod)
#############################################
FROM php-base AS vendor-prod

COPY composer.json composer.lock symfony.lock ./
COPY package.json package-lock.json ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    && npm ci \
    && npm run build:css

#############################################
# Image PHP — développement
#############################################
FROM php-base AS php-dev

COPY docker/php/php.dev.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint

RUN chmod +x /usr/local/bin/app-entrypoint

ENTRYPOINT ["/usr/local/bin/app-entrypoint"]
CMD ["php-fpm"]

#############################################
# Image PHP — production
#############################################
FROM php-base AS php-prod

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV APP_SECRET=build_time_secret_replace_at_runtime

COPY docker/php/php.prod.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint

COPY --from=vendor-prod /var/www/html/vendor ./vendor
COPY --from=vendor-prod /var/www/html/node_modules ./node_modules
COPY . .

RUN npm run build:css \
    && composer dump-autoload --optimize --classmap-authoritative --no-dev \
    && php bin/console asset-map:compile --env=prod --no-debug \
    && php bin/console cache:warmup --env=prod --no-debug \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var \
    && rm -rf node_modules

RUN chmod +x /usr/local/bin/app-entrypoint

USER www-data

ENTRYPOINT ["/usr/local/bin/app-entrypoint"]
CMD ["php-fpm"]

#############################################
# Image Nginx — production
#############################################
FROM nginx:1.27-alpine AS nginx-prod

COPY docker/nginx/default.prod.conf /etc/nginx/conf.d/default.conf
COPY --from=php-prod /var/www/html/public /var/www/html/public
