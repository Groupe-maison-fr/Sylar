FROM ubuntu:18.04
RUN apt-get update;\
    apt-get install -y \
        apt-transport-https \
        ca-certificates \
        curl \
        dirmngr \
        lsb-release \
        software-properties-common \
        ssh \
        sudo \
        supervisor \
        zfsutils;

RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php ;\
    apt-get update ;\
    apt-get install -y \
        php7.4-cli \
        php7.4-curl \
        php7.4-fpm \
        php7.4-json \
        php7.4-mbstring \
        php7.4-sqlite3 \
        php7.4-xml \
        php7.4-zip;

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');";\
    php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;";\
    php composer-setup.php;\
    php -r "unlink('composer-setup.php');";\
    mv composer.phar /usr/local/bin/composer

RUN curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg;\
    echo   "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null;\
    sudo apt-get update;\
    sudo apt-get install -y docker-ce-cli

ENV DOCKER_HOST=tcp://docker-socket-proxy:2375

RUN mkdir /run/php /var/www;\
    chown www-data:www-data /var/www;\
    echo "www-data ALL=(ALL:ALL) NOPASSWD: ALL" >> /etc/sudoers;\
    echo "root ALL=(ALL:ALL) NOPASSWD: ALL" >> /etc/sudoers