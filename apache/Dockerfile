FROM httpd:2.4-alpine

## Comment this if you dont have a proxy
ENV HTTP_PROXY http://168.88.170.2:8000
ENV HTTPS_PROXY http://168.88.170.2:8000

RUN apk update; \
    apk upgrade;

# Copy apache vhost file to proxy php requests to php-fpm container
COPY demo.apache.conf /usr/local/apache2/conf/demo.apache.conf
RUN echo "Include /usr/local/apache2/conf/demo.apache.conf" \
    >> /usr/local/apache2/conf/httpd.conf
