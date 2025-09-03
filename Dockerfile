FROM php:8.3-fpm

WORKDIR /app

COPY . .

# Instala tanto o driver PDO para MySQL quanto a extensão mysqli
RUN docker-php-ext-install pdo_mysql mysqli
