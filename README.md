Docker: PHP/Apache/MySQL
===================================

### Structure

```
/php-apache-mysql/
├── apache
│   ├── Dockerfile
│   └── demo.apache.conf
├── docker-compose.yml
├── php
│   └── Dockerfile
└── public_html
    └── index.php
```

#### index.php
```
<h1>Docker Setup OK!</h1>
<h4>Attempting MySQL connection from php...</h4>
<?php

$mysqli = new MySQLi("mysql","root","rootpassword");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} else {
    echo "Connected to MySQL successfully!";
}
?>
```
#### docker-compose.yml
```
version: "3.7"
services:
  php:
    build:
      context: './php/'
      args:
       PHP_VERSION: 7.3
    networks:
      - backend
    volumes:
      - ./public_html/:/var/www/html/
    restart: unless-stopped
    container_name: php_test
  composer:
    image: composer/composer:php7
    command: install
    volumes:
      - ./public_html/:/var/www/html/
  apache:
    build:
      context: './apache/'
      args:
       APACHE_VERSION: 2.4
    depends_on:
      - php
      - mysql
    networks:
      - frontend
      - backend
    ports:
      - "8181:80"
    volumes:
      - ./public_html/:/var/www/html/
    restart: unless-stopped
    container_name: apache_test
  mysql:
    image: mysql:5.7
    restart: always
    ports:
      - "3316:3306"
    volumes:
            - data:/var/lib/mysql
    networks:
      - backend
    environment:
      MYSQL_ROOT_PASSWORD: "rootpassword"
      MYSQL_DATABASE: "dbtest"
      MYSQL_USER: "otherUser"
      MYSQL_PASSWORD: "password"
    container_name: mysql
networks:
  frontend:
  backend:
volumes:
    data:

```
#### apache/Dockerfile
```
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

```

#### php/Dockerfile
```
FROM php:7.3-fpm-alpine


## Comment this if you dont have a proxy
ENV HTTP_PROXY http://168.88.170.2:8000
ENV HTTPS_PROXY http://168.88.170.2:8000

RUN apk update; \
    apk upgrade;

RUN docker-php-ext-install mysqli
```

#### apache/demo.apache.conf
```
ServerName localhost

LoadModule deflate_module /usr/local/apache2/modules/mod_deflate.so
LoadModule proxy_module /usr/local/apache2/modules/mod_proxy.so
LoadModule proxy_fcgi_module /usr/local/apache2/modules/mod_proxy_fcgi.so

<VirtualHost *:80>
    # Proxy .php requests to port 9000 of the php-fpm container
    ProxyPassMatch ^/(.*\.php(/.*)?)$ fcgi://php:9000/var/www/html/$1
    DocumentRoot /var/www/html/
    <Directory /var/www/html/>
        DirectoryIndex index.php
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Send apache logs to stdout and stderr
    CustomLog /proc/self/fd/1 common
    ErrorLog /proc/self/fd/2
</VirtualHost>
```
