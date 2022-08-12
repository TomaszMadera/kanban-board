FROM php:8.1.9-fpm

WORKDIR /home/kanban-board

RUN apt-get update
RUN apt-get install -y nano mc
RUN apt-get -y dist-upgrade

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN useradd -p $(openssl passwd -1 docker) docker
