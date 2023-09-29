#!/bin/bash

# set -x
shopt -s expand_aliases
argumentErrorMessage="This script will destroy postgresql-master docker, you must use '--force' argument"
if [ "$1" != "--force" ]; then
  echo $argumentErrorMessage
  exit
fi

alias postgresql_primary="PGPASSWORD=password psql --host 127.0.0.1 --port 15432 --username postgres "
alias postgresql_secondary="PGPASSWORD=password psql --host 127.0.0.1 --port 25432 --username postgres "
alias postgresql_dump_primary="pg_basebackup --host 127.0.0.1 --port 15432 "
alias postgresql_ping_primary="pg_isready --host 127.0.0.1 --port 15432"
alias postgresql_ping_secondary="pg_isready --host 127.0.0.1 --port 25432"
alias docker_exec_primary="docker exec postgresql-master-test bash -c "
alias docker_exec_secondary="docker exec postgresql bash -c "

function getDockerIp(){
  docker inspect $2 | jq -r --arg network "$1" '.[0].NetworkSettings.Networks[$network].IPAddress'
}

function waitPrimary(){
  echo "🔧 Wait postgresql primary"
  while ! postgresql_ping_primary; do
      echo -n "."
      sleep 1
  done
  echo "🔧 -"
}


function waitSecondary(){
  echo "🔧 Wait postgresql secondary"
  while ! postgresql_ping_secondary; do
      echo -n "."
      sleep 1
  done
  echo "🔧 -"
}

function cleanup(){
  echo "🔧 Cleanup"
  docker-compose down --volumes --remove-orphans
  docker rm -f postgresql-master-test
  docker rm -f postgresql
  docker volume rm -f test-env_test-postgresql-db
  docker network disconnect -f test-env_test-network postgresql
  docker network disconnect -f test-env_test-network postgresql-master-test
  docker network rm test-env_test-network
  sudo zfs destroy -f -R sylar/postgresql
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
  postgresql_primary -c "CREATE ROLE replicator WITH LOGIN NOSUPERUSER NOCREATEDB NOCREATEROLE NOINHERIT REPLICATION CONNECTION LIMIT -1 PASSWORD 'password';"
  #postgresql_primary -c "SELECT * FROM pg_create_physical_replication_slot('slot1');"
  #postgresql_primary -c "SELECT * FROM pg_replication_slots;"
}

function insertTestData(){
  echo "🔧 Insert test data"
  postgresql_primary replicated -c "create table persons (
                   person_id int,
                   last_name varchar(255),
                   first_name varchar(255),
                   address varchar(255),
                   city varchar(255),
                   PRIMARY KEY (person_id)
               );"
  postgresql_primary replicated -c "insert into persons values (1,'a','a','a','a');"
  postgresql_primary replicated -c "insert into persons values (2,'b','b','b','b');"
  postgresql_primary replicated -c "insert into persons values (3,'c','c','c','c');"
  postgresql_primary replicated -c "insert into persons values (4,'d','d','d','d');"
  postgresql_primary replicated -c "insert into persons values (5,'e','e','e','e');"
  postgresql_primary replicated -c "select * from persons;"
}

function startSecondary(){
  echo "🔧 Start secondary"
  docker-compose -f ../../docker-compose.yaml  exec runner bash -c 'bin/console service:start-master postgresql'
  waitSecondary
}

function connectDockerNetworks(){
  echo "🔧 Connect docker networks"
  docker network connect test-env_test-network postgresql
}

function synchronizeDatabases(){
  echo "🔧 Synchronize databases"
  primaryIP=$(getDockerIp "test-env_test-network" postgresql-master-test)
  secondaryIP=$(getDockerIp "test-env_test-network" postgresql)
  postgresUid=$(docker_exec_secondary "id postgres -u")
  postgresGid=$(docker_exec_secondary "id postgres -g")

  docker_exec_primary "rm -rf /tmp/postgresslave"
  docker_exec_primary "pg_basebackup --host 127.0.0.1 --port 5432 -D /tmp/postgresslave -U replicator --write-recovery-conf --progress --verbose"
  docker_exec_primary "cd /tmp; tar cfz postgresslave.tgz -C postgresslave ."
  docker cp postgresql-master-test:/tmp/postgresslave.tgz /tmp/postgresslave.tgz
  docker_exec_primary "echo 'host all all ${secondaryIP}/32 trust' >> /var/lib/postgresql/data/pgdata/pg_hba.conf"
  docker_exec_primary "echo 'host replication all ${secondaryIP}/32 trust' >> /var/lib/postgresql/data/pgdata/pg_hba.conf"
  docker_exec_primary "cat /etc/postgresql/pg_hba.conf"
  postgresql_primary -c "select pg_reload_conf();"
  docker stop postgresql

  sudo mkdir /sylar/postgresql/pgdata
  sudo tar xfz /tmp/postgresslave.tgz  -C /sylar/postgresql/pgdata
  echo "" > /tmp/postgresql.auto.conf
  echo "primary_conninfo='host=${primaryIP} port=5432 user=postgres password=password channel_binding=prefer sslmode=prefer sslcompression=0 sslsni=1 ssl_min_protocol_version=TLSv1.2 gssencmode=prefer krbsrvname=postgres target_session_attrs=any'" >> /tmp/postgresql.auto.conf
  sudo bash -c "cat /tmp/postgresql.auto.conf > /sylar/postgresql/pgdata/postgresql.auto.conf"
  sudo bash -c "touch /sylar/postgresql/pgdata/standby.signal"
  #sudo bash -c "echo 'log_statement=all' >> /sylar/postgresql/pgdata/postgresql.conf"
  #sudo bash -c "echo 'log_min_error_statement=DEBUG5' >> /sylar/postgresql/pgdata/postgresql.conf"
  #sudo bash -c "echo 'log_min_messages=DEBUG5' >> /sylar/postgresql/pgdata/postgresql.conf"
  sudo chown -R ${postgresUid}:${postgresGid} /sylar/postgresql/pgdata/
  docker start postgresql
  sleep 3
}

function showSlaveStatus(){
  echo "🔧 Show slave status"
  postgresql_primary -c "select client_addr, state, reply_time from pg_stat_replication;"
  postgresql_secondary -c "select sender_host, status,last_msg_receipt_time from pg_stat_wal_receiver;"
}


function testSynchronization(){
  echo "🔧 Test synchronization"
  postgresql_primary replicated -c "insert into persons values (6,'f','f','f','f');"
  sleep 2
  postgresql_secondary replicated -c "select * from persons where person_id=6;"
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
