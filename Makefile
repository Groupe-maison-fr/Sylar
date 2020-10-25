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
	vagrant down

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

docker-stats:
	@docker stats --format "{{.ID}} {{.CPUPerc}} {{.MemUsage}} {{.Name}}"

watch-assets:
	yarn run watch
