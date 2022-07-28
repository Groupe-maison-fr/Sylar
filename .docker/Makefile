.DEFAULT_GOAL := docker-stats

.PHONY: docker-stats
docker-stats:
	@docker stats --format "{{.ID}} {{.CPUPerc}} {{.MemUsage}} {{.Name}}"

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
