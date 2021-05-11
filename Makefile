SHELL := /bin/bash

setup:
	symfony console doctrine:schema:create
.PHONY: setup

tests:
	php bin/console doctrine:database:drop --env=test --force
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:create --env=test
	php bin/console doctrine:fixtures:load --env=test --no-interaction
	php bin/phpunit --configuration phpunit.xml.dist --coverage-text tests
.PHONY: tests