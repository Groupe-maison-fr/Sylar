#!/bin/sh

cd /app
which make || apt-get install -y make
chmod a+rwx /app/sqlite
sudo -u www-data composer install
env | sort
 [ -f /app/sqlite/app.db ] || sudo -u www-data -E bin/console doctrine:database:create
/usr/bin/supervisord
