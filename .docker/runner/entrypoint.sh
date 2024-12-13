#!/bin/sh
set -e

cd /app
install -o www-data -g www-data -d /app/data
install -o www-data -g www-data -d /app/var
install -o www-data -g www-data -d /app/config/jwt
cd /app
composer install
rm -rf /app/var/*
sudo -u www-data -E bin/console lexik:jwt:generate-keypair --skip-if-exists
env | sort
 [ -f /app/sqlite/app.db ] || sudo -u www-data -E bin/console doctrine:database:create
/usr/bin/supervisord
