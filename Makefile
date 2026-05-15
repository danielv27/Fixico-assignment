# Common workflows for the fixico-assignment stack.
#
# First clone:   make bootstrap
# Day to day:    make up | make down
# Reset:         make fresh

.PHONY: help up down migrate seed bootstrap fresh test logs flush-flags

help: ## Show available targets
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-12s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up:  ## Start the stack
	docker compose up -d

down: ## Stop the stack
	docker compose down

migrate: ## Run pending migrations
	docker compose exec api php artisan migrate

seed: ## Seed all databases (API + web)
	docker compose exec api php artisan db:seed
	docker compose exec web npx tsx scripts/seed.ts

bootstrap: up migrate seed ## First-time setup: start, migrate, seed

fresh: ## Wipe all data and re-seed (API DB + web SQLite)
	docker compose down -v
	docker compose exec web rm -f reports.db
	$(MAKE) up migrate seed

test: ## Run the API test suite
	docker compose exec api php artisan test

logs: ## Tail logs from every service
	docker compose logs -f

flush-flags: ## Invalidate the flag cache (use after editing flags directly in the DB)
	docker compose exec api php artisan cache:forget flags:index:v2
