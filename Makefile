.DEFAULT_GOAL := help

help:
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z0-9_-]+:.*?##/ { printf "  \033[36m%-27s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Setup

.PHONY: set-image-version
set-image-version: ## Set legacy version (branch, tag, commit): VERSION=v1.1.0
	sh ./.docker/scripts/set-image-version.sh VERSION=$(VERSION)


##@ Docker

.PHONY: down
down: ## Down legacy service
	cd .docker && docker-compose -f docker-compose.yml down

.PHONY: stop
stop: ## Stop legacy service
	cd .docker && docker-compose -f docker-compose.yml stop

.PHONY: restart
restart: ## Restart legacy service
	cd .docker && docker-compose -f docker-compose.yml restart


.PHONY: start-from-local
start-from-local: ## Start encoder in development environment
	cd .docker && docker-compose -f docker-compose.yml -f docker-compose.local.yml up -d


.PHONY: start-from-image
start-from-image: ## Start encoder in production environment from image
	cd .docker && docker-compose -f docker-compose.yml -f docker-compose.image.yml up -d


.PHONY: start-from-build
start-from-build: ## Start encoder in production environment from build
	cd .docker && docker-compose -f docker-compose.yml -f docker-compose.build.yml up -d


.PHONY: pull-image-from-registry
pull-image-from-registry: ## Pull legacy image from registry: URL, USERNAME, PASSWORD, IMAGE, VERSION
	echo $(PASSWORD) | docker login ${URL} --username ${USERNAME} --password-stdin &&\
	docker pull ${IMAGE}:${VERSION} &&\
	docker logout ${URL} &&\
	docker tag ${IMAGE}:${VERSION} anilibria.legacy:${VERSION} &&\
	docker rmi ${IMAGE}:${VERSION}



##@ Legacy

.PHONY: legacy
legacy: ## Enter legacy container
	docker exec -it anilibria.legacy sh
