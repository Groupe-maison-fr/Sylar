#!/bin/sh

cd /app
which make || apt-get install -y make
composer install

/usr/bin/supervisord
