#!/bin/bash
TODAY := $(shell date +'%Y%m%d')

help: ## Show this help message
	@echo "usage: make [target]"
	@echo
	@echo "targets:"
	@egrep "^(.+)\:\ ##\ (.+)" ${MAKEFILE_LIST} | column -t -c 2 -s ":#"

ip:
	ipconfig getifaddr en0

ps:
	docker ps | grep marketing

save-db: ## copia el dump a la carpeta db
	cp ${HOME}/dockercfg/db_dumps/db_mypromos.sql ./backend_web/db

restore-db: ## copia el dump a la carpeta db
	cp ./backend_web/db/db_mypromos.sql ${HOME}/dockercfg/db_dumps

rebuild: ## rebuild containers
	docker-compose --env-file ./docker/.env -f docker-compose.yml down
	docker-compose --env-file ./docker/.env -f docker-compose.yml up -d --build --remove-orphans
	docker-compose --env-file ./docker/.env up -d --no-deps --build php-marketing-db

config:
	docker-compose --env-file ./docker/.env config

destroy-all: ## destroy containers
	docker-compose --env-file ./docker/.env -f docker-compose.yml down

build-cron:
	docker-compose --env-file ./docker/.env up -d --no-deps --force-recreate --build php-marketing-cron
	make ps

build-db-:
	docker-compose --env-file ./docker/.env up -d --no-deps --force-recreate --build php-marketing-db
	make ps

build-web: #nginx
	docker-compose --env-file ./docker/.env up -d --no-deps --force-recreate --build php-marketing-web
	make ps

build-be: #fpm
	docker-compose --env-file ./docker/.env up -d --no-deps --force-recreate --build php-marketing-be
	make ps

restart-docker: ## restart docker
	# systemctl restart docker
	killall Docker && open /Applications/Docker.app

restart: ## restart the containers
	docker-compose --env-file ./docker/.env stop
	docker-compose --env-file ./docker/.env start

restart-be:
	docker restart php-marketing-be

restart-web:
	docker restart php-marketing-web

restart-cron:
	docker restart php-marketing-cron

restart-db:
	docker restart php-marketing-db

stop: ## stop containers
	docker-compose --env-file ./docker/.env stop

stop-db: ## stop db
	docker stop php-marketing-db

stop-cron: ## stop cron
	docker stop php-marketing-cron

stop-be: ## stop be
	docker stop php-marketing-be

stop-web: ## stop web
	docker stop php-marketing-web

logs-web: ## logs web
	docker logs php-marketing-web

logs-be: ## logs be
	docker logs php-marketing-be

logs-db: ## logs db
	docker logs php-marketing-db

rem-logs: ## remove logs
	rm -fr ./backend_web/logs/*
	rm -f ./backend_web/public/*.log

rem-cache: ## remove diskcache
	rm -fr ./backend_web/cache/* !.gitkeep

rem-xxx: ## remove xxx-module
	rm -fr ./backend_web/xxx-module/* !files

ssh-be: ## fpm
	docker exec -it --user root php-marketing-be bash

ssh-web: ## web
	docker exec -it --user root php-marketing-web bash

ssh-db: ## ssh's into mysql
	docker exec -it --user root php-marketing-db bash

ssh-cron: ## ssh's into crontab
	docker exec -it --user root php-marketing-cron sh

deploy-test: ## deploy codeonly in test
	py.sh deploy.codeonly eduardoaf

deploy-prod: ## deploy codeonly in prod
	py.sh deploy.codeonly eduardoaf-prod

start-front: ## npm run start
	cd frontend/restrict; npm run start

build-front: ## npm run build
	cd frontend/restrict; npm run build

gen-cert: ## certs
	openssl req -x509 -nodes -new -sha256 -days 1024 -newkey rsa:2048 -keyout ./io/in/localip-ca.key -out ./io/in/localip-ca.pem -subj "/CN=192.168.1.132"
	openssl x509 -outform pem -in ./io/in/localip-ca.pem -out ./io/in/localip-ca.crt

run-consumer: ## be-container
	run --class=App.Services.Kafka.LogConsumerService

ips: ## get ips of containers
	echo "php-marketing-web"; docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' php-marketing-web
	# echo "php-marketing-cron"; docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' php-marketing-cron
	echo "php-marketing-be"; docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' php-marketing-be
	echo "php-marketing-db"; docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' php-marketing-db

log-error: ## logs error
	cd ./backend_web/logs/error; \
	rm -f *.log; touch app_${TODAY}.log; clear; \
	tail -f app_${TODAY}.log;

log-sql: ## log queries
	cd ./backend_web/logs/sql; \
	rm -f *.log; touch app_${TODAY}.log; clear; \
	tail -f app_${TODAY}.log;