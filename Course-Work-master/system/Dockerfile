FROM php:8.1.1-apache

RUN apt-get update -y
RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update -y && apt-get install -y apache2-utils libaprutil1-dbd-mysql
RUN a2enmod authn_dbd
RUN docker-php-ext-install mysqli
