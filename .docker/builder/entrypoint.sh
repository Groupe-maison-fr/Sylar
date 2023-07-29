#!/bin/sh
export NODE_OPTIONS=--openssl-legacy-provider
cd /app
yarn install --network-timeout 1000000 --frozen-lockfile

/usr/bin/supervisord
