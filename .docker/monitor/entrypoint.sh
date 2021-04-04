#!/bin/sh

cd /monitor
yarn install
#--frozen-lockfile
yarn run start-server
#sleep 7200
