.DEFAULT_GOAL := help

help:
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z0-9_-]+:.*?##/ { printf "  \033[36m%-27s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)


##@ Docker

.PHONY: down
down: ## Down legacy service
	cd .docker && docker-compose -f docker-compose.yml down

.PHONY: stop
stop: ## Stop legacy service
	cd .docker && docker-compose -f docker-compose.yml stop


.PHONY: rebuild
start-from-local: ## Start legacy in development environment
	docker build -t anilibria.legacy:build .


.PHONY: start
start-from-local: ## Start legacy in development environment
	cd .docker && docker-compose -f docker-compose.yml up -d


##@ Legacy

.PHONY: legacy
legacy: ## Enter legacy container
	docker exec -it anilibria.legacy sh
