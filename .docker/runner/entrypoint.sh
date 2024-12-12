#!/bin/sh

cd /app
chmod a+rwx /app/sqlite
sudo -u www-data composer install
sudo -u www-data mkdir -p config/jwt
sudo -u www-data bin/console lexik:jwt:generate-keypair --skip-if-exists
env | sort
 [ -f /app/sqlite/app.db ] || sudo -u www-data -E bin/console doctrine:database:create
/usr/bin/supervisord
