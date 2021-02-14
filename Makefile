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

.PHONY: dev-up
dev-up:
	vagrant up --provision
	vagrant ssh slave -c 'cd /app;make install-composer;make vendor-dev;make node_modules'

.PHONY: dev-down
dev-down:
	vagrant halt

.PHONY: dev-destroy
dev-destroy:
	vagrant destroy -f

.PHONY: build-dev
build-dev: bin/box vendor-dev node_modules
	yarn run build

.PHONY: cs-fixer-fix
cs-fixer-fix:
	vendor/bin/php-cs-fixer fix --verbose

.PHONY: test
test:
	APP_ENV=test composer install --prefer-dist
	vendor/bin/phpstan analyse src --level 5
	vendor/bin/php-cs-fixer fix --verbose --dry-run
	/usr/bin/php /app/vendor/phpunit/phpunit/phpunit --configuration /app/phpunit.xml.dist tests --testdox

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

.PHONY: watch-assets
watch-assets:
	yarn run watch

.PHONY: vagrant-mysql-user-master
vagrant-mysql-user-master:
	vagrant ssh master -c 'mysql -u root -ptheR00tP455w0rdmaster -h 127.0.0.1  -e "select Host,User from mysql.user;"'

.PHONY: vagrant-mysql-master-replication-status
vagrant-mysql-master-replication-status:
	vagrant ssh master -c 'mysql -u root -ptheR00tP455w0rdmaster -h 127.0.0.1  -e "SHOW SLAVE STATUS;"'

.PHONY: vagrant-mysql-slave-replication-status
vagrant-mysql-slave-replication-status:
	vagrant ssh slave -c 'mysql -u root -ptheR00tP455w0rdslave -h 127.0.0.1  -e "SHOW SLAVE STATUS;"'

.PHONY: shell-master
shell-master:
	vagrant ssh master

.PHONY: shell
shell:
	vagrant ssh slave -c 'cd /app;bash'

.PHONY: tests
tests:
	vagrant ssh slave -c 'cd /app;make test'

.PHONY: build
build: node_modules vendor-prod
	rm -rf var/cache
	APP_ENV=prod bin/console
	yarn build
	box compile -vvv
	mv build/sylar.phar build/sylar
	ls -lah build

#start-local:
#	rm data/mysql*.json
#	docker rm -f mysql mysql_slave1 mysql_slave2 mysql_slave3 mysql_slave4 mysql_slave5
#	sudo zpool destroy local-mysql-pool
#	- sudo zfs destroy -Rf local-mysql-pool || true
#	- sudo zpool destroy -f local-mysql-pool || true
#	- sudo rm -rf /tmp/localroot || true
#	- sudo rm -rf /local-mysql-pool || true
#	- sudo mkdir /tmp/localroot || true
#	sudo fallocate -l 2G /tmp/localroot/local-mysql-root
#	sudo zpool create local-mysql-pool /tmp/localroot/local-mysql-root
#	bin/console --no-debug service:start-master mysql
#	bin/console --no-debug service:start mysql slave1
#	bin/console --no-debug service:start mysql slave2

start-local:
	- rm data/mysql*.json
	- docker rm -f mysql mysql_slave1 mysql_slave2 mysql_slave3 mysql_slave4 mysql_slave5
	sudo zpool destroy sylar
	sudo zpool create -f sylar /dev/sdb /dev/sdc
	bin/console --no-debug service:start-master mysql
	bin/console --no-debug service:start mysql slave1
	bin/console --no-debug service:start mysql slave2


# stop slave;CHANGE MASTER TO MASTER_HOST='192.168.99.21', MASTER_USER='replication_user', MASTER_PASSWORD='replication_password';start slave;