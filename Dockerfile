# syntax=docker/dockerfile:1
FROM dunglas/frankenphp:1.5.0-php8.3-alpine AS base

ARG APP_USER_ID=1000
ARG APP_GROUP_ID=1000
ARG FRANKENPHP_WORKER_MODE_ENABLED=false

ENV FRANKENPHP_WORKER_MODE_ENABLED=${FRANKENPHP_WORKER_MODE_ENABLED}

RUN apk add --no-cache git unzip bash icu-libs su-exec \
 && install-php-extensions @composer pdo_pgsql intl opcache zip

# Sync user/group with host to avoid permission issues on bind mounts
RUN if [ "$(id -u www-data)" != "$APP_USER_ID" ]; then \
      deluser www-data || true; \
      addgroup -g $APP_GROUP_ID -S www-data || true; \
      adduser -u $APP_USER_ID -G www-data -S www-data; \
    fi

WORKDIR /app

# Choose Caddyfile depending on FRANKENPHP_WORKER_MODE_ENABLED at container start
COPY .docker/caddy/classic.caddyfile /etc/caddy/classic.Caddyfile
COPY .docker/caddy/worker.caddyfile  /etc/caddy/worker.Caddyfile

# ---------- DEV ----------
FROM base AS dev

RUN install-php-extensions xdebug
COPY .docker/php/conf.d/zz-php.ini /usr/local/etc/php/conf.d/zz-php.ini
COPY .docker/php/conf.d/dev/       /usr/local/etc/php/conf.d/

ENV APP_ENV=dev
CMD ["sh", "-c", "if [ \"$FRANKENPHP_WORKER_MODE_ENABLED\" = \"true\" ]; then frankenphp run --config /etc/caddy/worker.Caddyfile; else frankenphp run --config /etc/caddy/classic.Caddyfile; fi"]

# ---------- PROD ----------
FROM base AS prod

COPY .docker/php/conf.d/zz-php.ini /usr/local/etc/php/conf.d/zz-php.ini
COPY .docker/php/conf.d/prod/      /usr/local/etc/php/conf.d/

COPY --chown=www-data:www-data . /app
USER www-data
RUN composer install --no-dev --no-progress --optimize-autoloader

ENV APP_ENV=prod
CMD ["frankenphp", "run", "--config", "/etc/caddy/classic.Caddyfile"]
