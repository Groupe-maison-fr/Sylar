# Sylar

Service cloner for development purpose

![general](http://www.plantuml.com/plantuml/proxy?cache=no&src=https://raw.githubusercontent.com/Groupe-maison-fr/Sylar/master/docs/general.iuml)

# Stack
![general](http://www.plantuml.com/plantuml/proxy?cache=no&src=https://raw.githubusercontent.com/Groupe-maison-fr/Sylar/master/docs/stack.iuml)


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
   - http://192.168.xxx.xxx/app/system


## Development Virtual Machine setup

### Initial system setup

```bash
sudo apt dist-upgrade
sudo apt-get update
sudo apt-get install -y vim
sudo visudo
sudo reboot
```

### Docker daemon setup

```bash
sudo groupadd docker
sudo usermod -aG docker $USER
newgrp docker
sudo apt install -y apt-transport-https ca-certificates curl software-properties-common zfsutils-linux jq make
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
sudo bash -c 'echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null '
cat /etc/apt/sources.list.d/docker.list
sudo apt update
apt-cache policy docker-ce
sudo apt install -y docker-ce docker-compose
```

### Project setup

```bash
sudo mkdir /opt/sylar
sudo chown sylar:sylar /opt/sylar
```

### Development user setup

```bash
sudo apt-get install -y fish
sudo chsh $USER --shell /usr/bin/fish
```

### Simple ZFS sample

```bash
sudo mkdir /zpool
sudo dd if=/dev/zero of=/zpool/sylar bs=1M count=500
sudo zpool create sylar /zpool/sylar
zfs list
```

### Project startup

```bash
cd /opt/sylar
make docker-compose-up-dev-amd64
```

### Test environment setup (mysql primary)

```bash
sudo apt-get install -y mariadb-client
docker-compose  -f tests/test-env/docker-compose.yaml up -d
sleep 5
tests/test-env/init.sh
```

### Test environment setup (sylar master on replication to mysql-primary)

```bash
docker-compose exec runner bin/console service:start-master mysql
docker ps
```

# TODO
- psalm
- log viewer


# To synchronize local development folder and virtual one, you can use mutagen

- create `mutagen.yml`
```
sync:
  defaults:
    flushOnCreate: true
    watch:
      mode: "force-poll"
      pollingInterval: 2
    ignore:
      paths:
        - var/cache
        - node_modules
        - .DS_Store
        - .git
        - .idea
    permissions:
      defaultFileMode: 0666
      defaultDirectoryMode: 0777
  sylar-session:
    alpha: .
    beta: sylar@sylar-dev:/opt/sylar
```

- add in `~/.ssh/config`
```
Host sylar-dev
    HostName 192.168.xxx.xxx
    User sylar
```
- `mutagen project start`
