# Common workflows for the fixico-assignment stack.
#
# First clone:   make bootstrap
# Day to day:    make up | make down
# Reset:         make fresh

.PHONY: help up down restart migrate seed bootstrap fresh test logs flush-flags env

help: ## Show available targets
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-12s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

env: ## Create api/.env from .env.example (idempotent)
	@if [ ! -f api/.env ]; then \
		cp api/.env.example api/.env; \
		echo "Created api/.env — edit it and 'make restart' to override defaults."; \
	fi

up: env ## Start the stack (no DB side-effects)
	docker compose up -d

down: ## Stop the stack
	docker compose down

restart: env ## Apply changes (recreates containers if compose config or env changed)
	docker compose up -d

migrate: ## Run pending migrations
	docker compose exec api php artisan migrate

seed: ## Run database seeders
	docker compose exec api php artisan db:seed

bootstrap: up migrate seed ## First-time setup: start, migrate, seed

fresh: ## Wipe volumes and bootstrap from scratch
	docker compose down -v
	$(MAKE) bootstrap

test: ## Run the API test suite
	docker compose exec api php artisan test --compact

flush-flags: ## Invalidate the flag cache (use after editing flags directly in the DB)
	docker compose exec api php artisan cache:forget flags:index:v1

logs: ## Tail logs from every service
	docker compose logs -f
