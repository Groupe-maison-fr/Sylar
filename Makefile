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
	- rm -r public/build/*
	$(MAKE) host-dev-destroy

.PHONY: host-dev-up
host-dev-up:
	vagrant up --provision --parallel
	#vagrant ssh sylar -c 'cd /opt/sylar;make docker-up'

.PHONY: host-dev-down
host-dev-down:
	vagrant halt

.PHONY: host-dev-destroy
host-dev-destroy:
	vagrant destroy -f

.PHONY: host-shell-runner
host-shell-runner:
	vagrant ssh sylar -c 'cd /opt/sylar;docker-compose exec runner bash'

.PHONY: host-shell-builder
host-shell-builder:
	vagrant ssh sylar -c 'cd /opt/sylar;docker-compose exec builder sh'

.PHONY: host-shell-monitor
host-shell-monitor:
	vagrant ssh sylar -c 'cd /opt/sylar;docker-compose exec monitor sh'

.PHONY: host-tests
host-tests:
	vagrant ssh sylar -- docker-compose -f /opt/sylar/docker-compose.yaml exec runner make test


.PHONY: host-shell-sylar
host-shell-sylar:
	vagrant ssh sylar -c 'cd /opt/sylar;fish'

.PHONY: shell
shell:
	$(MAKE) host-shell

.PHONY: host-vagrant-init-docker-compose
host-vagrant-init-docker-compose:
	vagrant ssh sylar -- "docker-compose -f /opt/sylar/docker-compose.yaml up -d --build"

.PHONY: host-watch-assets
host-watch-assets:
	 vagrant ssh sylar -- docker-compose -f /opt/sylar/docker-compose.yaml exec builder yarn run watch

.PHONY: cs-fixer-fix
cs-fixer-fix:
	vendor/bin/php-cs-fixer fix --verbose

.PHONY: docker-stats
docker-stats:
	@docker stats --format "{{.ID}} {{.CPUPerc}} {{.MemUsage}} {{.Name}}"

.PHONY: test
test:
	APP_ENV=test composer install --prefer-dist
	vendor/bin/phpstan analyse src --level 5
	vendor/bin/php-cs-fixer fix --verbose --dry-run
	vendor/bin/phpunit --configuration phpunit.xml.dist tests --testdox

.PHONY: tests
tests:
	docker-compose exec runner make test

.PHONY: docker-down
docker-down:
	docker-compose down --remove-orphans

.PHONY: docker-up
docker-up:
	docker-compose up --build -d

.PHONY: docker-logs
docker-logs:
	docker-compose logs -f

.PHONY: host-restart-worker
host-restart-worker:
	vagrant ssh sylar -c 'docker-compose -f /opt/sylar/docker-compose.yaml exec runner supervisorctl restart php-worker'
