FROM ubuntu:20.04

ARG COMPOSER_VERSION=2.8.4

RUN apt-get update;\
    apt-get install -y \
        apt-transport-https \
        ca-certificates \
        curl \
        dirmngr \
        lsb-release \
        software-properties-common \
        sudo \
        make \
        supervisor; \
    LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php ;\
    apt-get update ;\
    apt-get install -y \
        git \
        php8.2 \
        php8.2-curl \
        php8.2-mbstring \
        php8.2-sqlite3 \
        php8.2-xml \
        php8.2-zip; \
    curl -L https://github.com/composer/composer/releases/download/${COMPOSER_VERSION}/composer.phar -o /usr/local/bin/composer; \
    chmod +x /usr/local/bin/composer

RUN mkdir /app; \
    cd /app; \
    git clone https://github.com/bpolaszek/freddie; \
    cd freddie; \
    git checkout 6fc70c78fea27c7feda1aeb22486de71a52e0fa2; \
    composer install --no-scripts

WORKDIR /app/freddie
