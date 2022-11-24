FROM node:current-alpine as frontend
WORKDIR /app

RUN apk update && apk add yarn && mkdir public

COPY assets assets
COPY package.json yarn.lock webpack.config.js ./

RUN yarn install && yarn build || cat yarn-error.log

# Dockerfile
FROM php:8.1-alpine

RUN apk add --no-cache autoconf openssl-dev g++ make pcre-dev icu-dev zlib-dev libzip-dev && \
    docker-php-ext-install bcmath intl opcache zip sockets && \
    apk del --purge autoconf g++ make

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --prefer-dist --no-progress --no-interaction

RUN ./vendor/bin/rr get-binary --location /usr/local/bin

COPY . .

COPY --from=frontend /app/public/build /app/public/build

ENV APP_ENV=prod \
    APP_TZ=UTC \
    DATABASE_FILE=/data/data.db

VOLUME /data

RUN chmod +x entrypoint.sh && \
    composer dump-autoload --optimize && \
    composer check-platform-reqs

RUN ln -s  /app/entrypoint.sh /entrypoint.sh # backwards compat
ENTRYPOINT ["ash","/app/entrypoint.sh"]

EXPOSE 8080

CMD ["rr", "serve",".rr.yaml"]