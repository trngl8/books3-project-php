SHELL := /bin/bash

setup:
	php bin/console doctrine:schema:create
	php bin/console assets:install --symlink
.PHONY: setup

tests:
	php bin/console doctrine:database:drop --env=test --force
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:create --env=test
	php bin/console doctrine:fixtures:load --env=test --no-interaction
	php bin/phpunit --configuration phpunit.xml.dist tests
.PHONY: tests