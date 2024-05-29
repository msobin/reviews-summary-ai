#syntax=docker/dockerfile:1.4
FROM php:8.3-fpm-alpine3.17

RUN set -eux && \
    apk update && \
    apk add --no-cache patch bash supervisor && \
    apk del && \
    rm -rf /var/lib/apt/lists/* && \
    mkdir /root/history

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions @composer opcache intl pcov pcntl  xdebug && \
    ln -s composer /usr/local/bin/c

RUN mkdir -p /var/log/supervisord

COPY --link fpm.conf /usr/local/etc/php-fpm.d/zzz-docker.conf

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
