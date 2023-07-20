#!/bin/bash

#set -x
shopt -s expand_aliases
argumentErrorMessage="This script will destroy mysql-master docker, you must use '--force' argument"
if [ "$1" != "--force" ]; then
  echo $argumentErrorMessage
  exit
fi

alias mysql_primary="mysql -u root --password=password -h 127.0.0.1 -P 13306 "
alias mysql_secondary="mysql -u root --password=sylar_root_password -h 127.0.0.1 -P 33306 "
alias mysql_dump_primary="mariadb-dump -u root --password=password -h 127.0.0.1 -P 13306 "
alias mysql_ping_primary="mysqladmin ping -h 127.0.0.1 -P 13306 --silent"
alias mysql_ping_secondary="mysqladmin ping -h 127.0.0.1 -P 33306 --silent"

function waitPrimary(){
  echo "🔧 Wait mysql primary"
  while ! $(mysql_ping_primary); do
      echo -n "."
      sleep 1
  done
  echo "🔧 -"
}

function waitSecondary(){
  echo "🔧 Wait mysql secondary"
  while ! $(mysql_ping_secondary); do
      echo -n "."
      sleep 1
  done
  echo "🔧 -"
}

function cleanup(){
  echo "🔧 Cleanup"
  docker-compose down --volumes --remove-orphans
  docker rm -f mysql-master-test
  docker rm -f mysql
  docker network disconnect -f test-env_test-network mysql
  docker network disconnect -f test-env_test-network mysql-master-test
  docker network rm test-env_test-network
  sudo zfs destroy -f -R sylar/mysql
  sudo zfs list
  docker ps --all
}

function dockerComposerUpMaster(){
  echo "🔧 Docker composer up_master"
  docker-compose up -d
  waitPrimary
}

function createSynchronizationUser(){
  echo "🔧 Create synchronization user"
  mysql_primary -e "CREATE USER 'replication_user'@'%' IDENTIFIED BY 'replication_password';"
  mysql_primary -e "GRANT REPLICATION SLAVE ON *.* TO 'replication_user'@'%';"
}

function insertTestData(){
  echo "🔧 Insert test data"
  mysql_primary -e "create database replicated;"
  mysql_primary -e "create table replicated.persons (
                   person_id int,
                   last_name varchar(255),
                   first_name varchar(255),
                   address varchar(255),
                   city varchar(255),
                   PRIMARY KEY (person_id)
               );"
  mysql_primary -e "insert into replicated.persons values (1,'a','a','a','a');"
  mysql_primary -e "insert into replicated.persons values (2,'b','b','b','b');"
  mysql_primary -e "insert into replicated.persons values (3,'c','c','c','c');"
  mysql_primary -e "insert into replicated.persons values (4,'d','d','d','d');"
  mysql_primary -e "insert into replicated.persons values (5,'e','e','e','e');"
  mysql_primary -e "select * from replicated.persons;"
}

function startSecondary(){
  echo "🔧 Start secondary"
  docker-compose -f ../../docker-compose.yaml  exec runner bash -c 'bin/console service:start-master mysql'
  waitSecondary
}

function connectDockerNetworks(){
  echo "🔧 Connect docker networks"
  docker network connect test-env_test-network mysql
}

function synchronizeDatabases(){
  echo "🔧 Synchronize databases"
  mysql_secondary -e "stop slave;"
  mysql_dump_primary --flush-logs --hex-blob --master-data=2 --hex-blob --disable-keys --add-drop-database --databases replicated > /tmp/dump.sql
  mysql_secondary < /tmp/dump.sql
  cat /tmp/dump.sql | grep 'CHANGE MASTER' | sed 's/^--\s*//' > /tmp/change-master.sql
  mysql_secondary -e "CHANGE MASTER TO MASTER_HOST='mysql-master-test', MASTER_USER='replication_user', MASTER_PASSWORD='replication_password';"
  mysql_secondary < /tmp/change-master.sql
  mysql_secondary -e "start slave;"
}

function showSlaveStatus(){
  echo "🔧 Show slave status"
  sleep 3
  mysql_secondary -e "show slave status \G;"
}

function testSynchronization(){
  echo "🔧 Test synchronization"
  mysql_primary -e "insert into replicated.persons values (6,'f','f','f','f');"
  mysql_secondary -e "select * from replicated.persons where person_id=6;"
}


cleanup;cleanup;cleanup

dockerComposerUpMaster
createSynchronizationUser
insertTestData
startSecondary
connectDockerNetworks
synchronizeDatabases
showSlaveStatus
testSynchronization
