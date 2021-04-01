# Diagram 
```
graph TD
    subgraph "Legend"
        legendMysql[("Mysql Server")] 
        legendDocker[/"Docker"/]
    end
    subgraph "fa:fa-server Master 192.168.99.20"
        master[("Mysql Master")] 
    end
    subgraph "fa:fa-server Slave 192.168.99.21"
        slaveAndMaster[("Mysql Slave<br/>Mysql master")]

        sylarMaster[/"Sylar master<br/>Mysql slave<br/>/sylar/mysql:/var/lib/mysql"/]
        sylarReplica1[/"Mysql Sylar<br/>1st snapshot<br/>/sylar/mysql/slave1:/var/lib/mysql"/]
        sylarReplica2[/"Mysql Sylar<br/>2nd snapshot<br/>/sylar/mysql/slave2:/var/lib/mysql"/]
        sylarReplica3[/"Mysql Sylar<br/>3rd snapshot<br/>/sylar/mysql/slave3:/var/lib/mysql"/]
        zfsRoot{{"Zfs root:<br/>/sylar"}}
    end
    master --> slaveAndMaster
    slaveAndMaster --> sylarMaster
    sylarMaster --> sylarReplica1
    sylarMaster --> sylarReplica2
    sylarMaster --> sylarReplica3

classDef mysql fill:#BBB,stroke:#111,stroke-width:1px;
classDef sylar fill:#EEE,stroke:#111,stroke-width:1px;
class legendMysql,master,slaveAndMaster mysql;
class legendDocker,sylarMaster,sylarReplica1,sylarReplica2,sylarReplica3 sylar;
```
