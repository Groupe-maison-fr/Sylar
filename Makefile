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

.PHONY: dev-up
dev-up:
	vagrant up --provision

.PHONY: dev-down
dev-down:
	vagrant halt

.PHONY: dev-destroy
dev-destroy:
	vagrant destroy -f

.PHONY: vendor
vendor:
	composer install
	yarn install

.PHONY: build
build:
	composer install
	yarn install
	yarn run build

cs-fixer-fix:
	vendor/bin/php-cs-fixer fix --verbose

test:
	APP_ENV=test composer install --prefer-dist
	vendor/bin/phpstan analyse src --level 5
	vendor/bin/php-cs-fixer fix --verbose --dry-run
	/usr/bin/php /app/vendor/phpunit/phpunit/phpunit --configuration /app/phpunit.xml.dist tests --testdox

docker-stats:
	@docker stats --format "{{.ID}} {{.CPUPerc}} {{.MemUsage}} {{.Name}}"

watch-assets:
	yarn run watch

vagrant-mysql-user-master:
	vagrant ssh master -c 'mysql -u root -ptheR00tP455w0rdmaster -h 127.0.0.1  -e "select Host,User from mysql.user;"'

vagrant-mysql-master-replication-status:
	vagrant ssh master -c 'mysql -u root -ptheR00tP455w0rdmaster -h 127.0.0.1  -e "SHOW SLAVE STATUS;"'

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

