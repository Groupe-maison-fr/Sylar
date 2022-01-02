# Sylar

Service cloner for development purpose

```plantuml
@startuml
   allowmixing
   frame server1{
      database MysqlMaster {
      }
   }
   frame server2{
      node dockerSlave {
         database MysqlSlave [
            port: 13306
            /sylar/mysql:/var/lib/mysql
         ]
      }
      node dockerMysqlReplica1 {
         database MysqlReplica1 [
            port: 13307 (index 1)
            1st snapshot
            /sylar/mysql/slave1:/var/lib/mysql
         ]
      }
      node dockerMysqlReplica2 {
         database MysqlReplica2 [
            port: 13307 (index 2)
            2nd snapshot
            /sylar/mysql/slave1:/var/lib/mysql
         ]
      }
      node dockerMysqlReplicaN {
         database MysqlReplicaN [
            port: 13306+N (index N)
            3rd snapshot
            /sylar/mysql/slave1:/var/lib/mysql
         ]
      }
   }
   MysqlMaster --> MysqlSlave : replication
   MysqlSlave --> MysqlReplica1 : zfs snapshot
   MysqlSlave --> MysqlReplica2 : zfs snapshot
   MysqlSlave --> MysqlReplicaN : zfs snapshot
@enduml
```

# Stack
```plantuml
@startuml
    allowmixing

    queue "/var/run/docker.sock" as dockerSock
    rectangle Stack {
       node runner {
         node worker
         node "php-fpm" as phpFpm
       }
       node builder
       node "docker-socket-proxy" as dockerSocketProxy
       node webserver #yellow
       node mercure
       node redis
       node grafana
       node prometheus
       node nodeExporter
       node cadvisor
       node loki
       cloud system{
       }
    }
    webserver <-- phpFpm #orange
    phpFpm --> redis #green
    worker <--> redis #green
    phpFpm --> mercure #orange
    mercure --> webserver #orange
    nodeExporter --> prometheus  #orange
    cadvisor --> prometheus #orange
    prometheus --> grafana #orange
    webserver <-- builder #orange
    webserver <-- grafana #orange
    webserver <-- loki #orange
    phpFpm --> loki #blue
    worker --> loki #blue
    phpFpm --> dockerSocketProxy
    dockerSock --> dockerSocketProxy
    dockerSock --> cadvisor
    worker <--> dockerSocketProxy
    system --> loki
    system --> nodeExporter
@enduml
```

## Development setup
```
vagrant plugin install vagrant-fsnotify

make host-dev-up
make host-vagrant-init-docker-compose
make host-shell-runner
```

GUI can be accessed on `http://192.168.99.21:8080/app/services`

### In vagrant docker php runner
```
make test
```

## Installation

1. Prepare your host with zfs packages

    ```
    apt-get install zfsutils-linux
    zpool create sylar /dev/sdb /dev/sdc
    ```

2. Install source

    ```
    mkdir /opt/sylar
    cd /opt/sylar
    git clone https://github.com/Groupe-maison-fr/Sylar.git
    ```

3. Customize the `/opt/sylar/data` service configurations

4. Start the services
    ```
    docker-compose up -d
    docker-compose logs -f
    ```

5. GUI can be accessed on `http://xxx.xxx.xxx.xxx:8080/app/services`

## Development

1. Install source and start the stack

    ```
    cd ~/src
    git clone https://github.com/Groupe-maison-fr/Sylar.git
    cd sylar
    make host-dev-up host-vagrant-init-docker-compose
    ```
   this can take a while
2. Open the app the your browser
   - http://192.168.99.21:8080/app/system
3. If you want to modify assets
   ```
   make host-watch-assets
   ```
# TODO
 - psalm
 - log viewer
