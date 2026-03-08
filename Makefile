.PHONY: up down build logs shell consume

## Start all services
up:
	docker compose up -d --build

## Stop all services
down:
	docker compose down

## Rebuild images
build:
	docker compose build --no-cache

## Tail logs
logs:
	docker compose logs -f

## Open a shell in the PHP container
shell:
	docker compose exec php bash

## Start Symfony Messenger worker
consume:
	docker compose exec php php bin/console messenger:consume async -vv

## Run composer install inside the container
composer-install:
	docker compose exec php composer install
