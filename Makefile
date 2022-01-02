.DEFAULT_GOAL := help
###.silent := true

.PHONY: help
help:
	@echo "Usage:"
	@echo "     make [command]"
	@echo
	@echo "Available commands:"
	@grep -h '^[^#[:space:]].*:' Makefile | \
	  grep -v '^default' |\
	  grep -v '^\.' |\
	  grep -v '=' |\
	  grep -v '^_' |\
	  sed 's/://' |\
	  xargs -n 1 echo ' -' |\
	  sort
	@echo

.PHONY: clean
clean:
	- rm -r vendor/*
	- rm -r node_modules/*
	- rm -r public/build/*
	$(MAKE) dev-destroy

bin/box:
	wget https://github.com/box-project/box/releases/download/3.11.1/box.phar -O bin/box;chmod +x bin/box

.PHONY: install-composer
install-composer:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"
	sudo chmod +x composer.phar
	sudo mv composer.phar /usr/bin/composer

.PHONY: host-dev-up
host-dev-up:
	vagrant up --provision --parallel
	#vagrant ssh slave -c 'cd /app;make install-composer;make vendor-dev;make node_modules'

.PHONY: host-dev-down
host-dev-down:
	vagrant halt

.PHONY: host-dev-destroy
host-dev-destroy:
	vagrant destroy -f

.PHONY: host-shell-runner
host-shell-runner:
	vagrant ssh slave -c 'cd /opt/sylar;docker-compose exec runner bash'

.PHONY: host-shell-builder
host-shell-builder:
	vagrant ssh slave -c 'cd /opt/sylar;docker-compose exec builder sh'

.PHONY: host-tests
host-tests:
	vagrant ssh slave -c 'cd /opt/sylar;make tests'

.PHONY: host-mysql-user-master
host-mysql-user-master:
	vagrant ssh master -c 'mysql -u root -ptheR00tP455w0rdmaster -h 127.0.0.1  -e "select Host,User from mysql.user;"'

.PHONY: host-mysql-master-replication-status
host-mysql-master-replication-status:
	vagrant ssh master -c 'mysql -u root -ptheR00tP455w0rdmaster -h 127.0.0.1  -e "SHOW SLAVE STATUS;"'

.PHONY: host-mysql-slave-replication-status
host-mysql-slave-replication-status:
	vagrant ssh slave -c 'mysql -u root -ptheR00tP455w0rdslave -h 127.0.0.1  -e "SHOW SLAVE STATUS;"'

.PHONY: host-shell-master
host-shell-master:
	vagrant ssh master

.PHONY: host-shell-slave
host-shell-slave:
	vagrant ssh slave -c 'cd /opt/sylar;zsh'

.PHONY: shell
shell:
	$(MAKE) host-shell-slave

.PHONY: host-fsnotify
host-fsnotify:
	vagrant fsnotify slave

.PHONY: host-vagrant-init-docker-compose
host-vagrant-init-docker-compose:
	vagrant ssh slave -- "\
		sudo chmod 666 /var/run/docker.sock;\
		rm ~/.ssh/id_rsa ~/.ssh/readable.id_rsa || true;\
		ssh-keygen -q -t rsa -N '' -f ~/.ssh/id_rsa <<<y 2>&1 >/dev/null;\
		sshpass -p vagrant ssh-copy-id -o StrictHostKeyChecking=no vagrant@127.0.0.1; \
		cp ~/.ssh/id_rsa ~/.ssh/readable.id_rsa; \
		chmod a+r ~/.ssh/readable.id_rsa;\
		docker-compose -f /opt/sylar/docker-compose.yaml up -d --build;\
	"

.PHONY: host-watch-assets
host-watch-assets:
	 vagrant ssh slave -- docker-compose -f /opt/sylar/docker-compose.yaml exec builder yarn run watch

.PHONY: build-dev
build-dev: bin/box vendor-dev node_modules
	yarn run build

.PHONY: cs-fixer-fix
cs-fixer-fix:
	vendor/bin/php-cs-fixer fix --verbose

.PHONY: docker-stats
docker-stats:
	@docker stats --format "{{.ID}} {{.CPUPerc}} {{.MemUsage}} {{.Name}}"

.PHONY: node_modules
node_modules:
	yarn install

.PHONY: vendor-prod
vendor-prod:
	rm -rf vendor
	APP_ENV=prod composer install --prefer-dist --no-scripts --no-dev

.PHONY: vendor-dev
vendor-dev:
	APP_ENV=dev composer install --prefer-dist

.PHONY: test
test:
	APP_ENV=test composer install --prefer-dist
	vendor/bin/phpstan analyse src --level 5
	vendor/bin/php-cs-fixer fix --verbose --dry-run
	vendor/bin/phpunit --configuration phpunit.xml.dist tests --testdox

.PHONY: tests
tests:
	docker-compose exec runner make test

.PHONY: build
build: node_modules vendor-prod
	rm -rf var/cache
	APP_ENV=prod bin/console
	yarn build
	box compile -vvv
	mv build/sylar.phar build/sylar
	ls -lah build

.PHONY: docker-down
docker-down:
	docker-compose down --remove-orphans

.PHONY: docker-up
docker-up:
	docker-compose up --build -d

.PHONY: docker-logs
docker-logs:
	docker-compose logs -f

.PHONY: docker-volume-clean
docker-volume-clean: docker-down
	docker volume rm --force \
		sylarinternal_builder-build \
		sylarinternal_node-modules-builder \
		sylarinternal_node-modules-monitor \
		sylarinternal_vendor-sylar

start-local:
	- rm data/mysql*.json
	- docker rm -f mysql mysql_slave1 mysql_slave2 mysql_slave3 mysql_slave4 mysql_slave5
	sudo zpool destroy sylar
	sudo zpool create -f sylar /dev/sdb /dev/sdc
	bin/console --no-debug service:start-master mysql
	bin/console --no-debug service:start mysql slave1
	bin/console --no-debug service:start mysql slave2

start-local-docker:
	- rm data/mysql*.json
	- docker rm -f mysql mysql_slave1 mysql_slave2 mysql_slave3 mysql_slave4 mysql_slave5
#	sudo zpool destroy sylar
#	sudo zpool create -f sylar /dev/sdb /dev/sdc
	sylar --no-debug service:start-master mysql
	sylar --no-debug service:start mysql slave1
	sylar --no-debug service:start mysql slave2

host-restart-worker:
	vagrant ssh slave -c 'cd /opt/sylar;docker-compose exec runner supervisorctl restart php-worker'
# stop slave;CHANGE MASTER TO MASTER_HOST='192.168.99.21', MASTER_USER='replication_user', MASTER_PASSWORD='replication_password';start slave;
