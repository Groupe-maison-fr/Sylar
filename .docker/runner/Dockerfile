FROM ubuntu:20.04
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
        php8.2 \
        php8.2-cli \
        php8.2-curl \
        php8.2-fpm \
        php8.2-mbstring \
        php8.2-sqlite3 \
        php8.2-xml \
        php8.2-redis \
        php8.2-zip;

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');";\
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;";\
    php composer-setup.php;\
    php -r "unlink('composer-setup.php');";\
    mv composer.phar /usr/local/bin/composer

RUN curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg;\
    echo   "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null;\
    sudo apt-get update;\
    sudo apt-get install -y docker-ce-cli


RUN processor=$(uname --processor); \
    if [ "${processor}" = "aarch64" ]; then \
        savior=arm64;\
    else \
        savior=amd64;\
    fi;\
    cd /tmp ;\
    apt-get install -y unzip;\
    curl -L https://github.com/grafana/loki/releases/download/v2.5.0/logcli-linux-${savior}.zip -o /tmp/logcli.zip ;\
    unzip logcli.zip ;\
    sudo mv logcli-linux-${savior} /usr/local/bin/logcli

ENV DOCKER_HOST=http://socket-proxy:2375

RUN mkdir /run/php /var/www;\
    chown www-data:www-data /var/www;\
    echo "www-data ALL=(ALL:ALL) NOPASSWD: ALL" >> /etc/sudoers;\
    echo "root ALL=(ALL:ALL) NOPASSWD: ALL" >> /etc/sudoers
