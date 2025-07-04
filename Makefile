DOCKER_COMPOSE = docker compose
APP_SERVICE = app
DB_SERVICE = db

# Build Docker images
build:
	$(DOCKER_COMPOSE) build

up:
	$(DOCKER_COMPOSE) up -d

all: build up

down:
	$(DOCKER_COMPOSE) down

restart: down all

logs:
	$(DOCKER_COMPOSE) logs -f $(service)

migrate:
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) php artisan migrate

migrate-refresh:
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) php artisan migrate:refresh

shell:
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) sh

seed:
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) php artisan db:seed

