#!/bin/bash

# Credit of creation of unit file https://techoverflow.net/2020/10/24/create-a-systemd-service-for-your-docker-compose-project-in-10-seconds/
# by Uli Köhler - https://techoverflow.net, Licensed as CC0 1.0 Universal

SCRIPT_PATH=$(dirname $(dirname $(dirname $(realpath "$0"))))
SYLAR_ROOT_PATH=${SYLAR_PATH:-${SCRIPT_PATH}}
SERVICE_NAME=sylar

rm -f /usr/local/bin/sylar-cli || true
ln -s ${SCRIPT_PATH}/bin/sys/sylar-cli /usr/local/bin/sylar-cli

echo "Creating systemd service... /etc/systemd/system/${SERVICE_NAME}.service"

sudo cat >/etc/systemd/system/$SERVICE_NAME.service <<EOF
[Unit]
Description=$SERVICE_NAME
Requires=docker.service
After=docker.service

[Service]
Restart=always
User=root
Group=docker
WorkingDirectory=$(pwd)

ExecStartPre=$(which docker-compose) -f $SYLAR_ROOT_PATH/docker-compose.yaml down
ExecStart=$(which docker-compose) -f $SYLAR_ROOT_PATH/docker-compose.yaml up --build
ExecStop=$(which docker-compose) -f $SYLAR_ROOT_PATH/docker-compose.yaml down

[Install]
WantedBy=multi-user.target
EOF

echo "Enabling & starting $SERVICE_NAME"
systemctl enable --now $SERVICE_NAME.service
systemctl status --no-pager $SERVICE_NAME.service

sylar-cli cache:clear
