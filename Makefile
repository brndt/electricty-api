current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))
RUN = docker exec -t electricity-php

.PHONY: build
build: dependencies start wait-databases-start schema-update

.PHONY: dependencies
dependencies: composer-install

composer-env-file:
	@if [ ! -f .env.local ]; then echo '' > .env.local; fi

.PHONY: composer-install
composer-install: CMD=install

.PHONY: composer-update
composer-update: CMD=update

.PHONY: composer-require
composer-require: CMD=require
composer-require: INTERACTIVE=-ti --interactive

.PHONY: composer-require-module
composer-require-module: CMD=require $(module)
composer-require-module: INTERACTIVE=-ti --interactive

.PHONY: composer
composer composer-install composer-update composer-require composer-require-module: composer-env-file
	@docker run --rm $(INTERACTIVE) --volume $(current-dir):/app --user $(id -u):$(id -g) \
		composer:2 $(CMD) \
			--ignore-platform-reqs \
			--no-ansi

.PHONY: start
start:
	docker-compose up --build -d

.PHONY: down
down:
	docker-compose down

.PHONY: schema-update
schema-update:
	$(RUN) ./bin/console doctrine:schema:update --force

.PHONY: test-acceptance
test-acceptance:
	$(RUN) ./vendor/bin/behat --format=pretty -v

.PHONY: wait-databases-start
wait-databases-start:
	chmod +x ./ping-mysql.sh
	./ping-mysql.sh

.PHONY: ping-mysql
ping-mysql:
	@docker exec electricity-mysql mysqladmin --user=root --host "127.0.0.1" ping --silent