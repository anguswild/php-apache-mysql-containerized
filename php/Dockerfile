FROM php:7.3-fpm-alpine


## Comment this if you dont have a proxy
ENV HTTP_PROXY http://168.88.170.2:8000
ENV HTTPS_PROXY http://168.88.170.2:8000

RUN apk update; \
    apk upgrade;

RUN docker-php-ext-install mysqli
