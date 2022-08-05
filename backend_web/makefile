#!/bin/bash
OS := $(shell uname)

help: ## Show this help message
	@echo "usage:\n\tmake [target]"
	@echo "targets:"
	@egrep "^(.+)\:\ ##\ (.+)" ${MAKEFILE_LIST} | column -t -c 2 -s ":#"

remlogs: ## elimina los logs
	rm -fr logs/*

test: ## run test
ifeq ($(OS),Linux)
	/usr/bin/php8.0-cli ./vendor/bin/phpunit tests
else
	vendor/bin/phpunit --bootstrap ./vendor/theframework/bootstrap.php ./tests/Services/Apify/Security/LoginServiceTest.php --color=auto
endif