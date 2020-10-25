# Sylar

Service cloner for development purpose


```
sudo mkdir /data
sudo fallocate -l 16G /data/replica.zfs
sudo zpool create replica /data/replica.zfs
sudo zpool list
sudo zfs create replica/master.mysql
ls /replica/master.mysql
```
