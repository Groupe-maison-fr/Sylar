FROM ubuntu:20.04
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
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');";\
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;";\
    php composer-setup.php;\
    php -r "unlink('composer-setup.php');";\
    mv composer.phar /usr/local/bin/composer

RUN mkdir /app; \
    cd /app; \
    git clone https://github.com/bpolaszek/freddie; \
    cd freddie; \
    git checkout 6fc70c78fea27c7feda1aeb22486de71a52e0fa2; \
    composer install --no-scripts

WORKDIR /app/freddie
