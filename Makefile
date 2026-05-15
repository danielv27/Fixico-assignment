# Common workflows for the fixico-assignment stack.
#
# First clone:   make bootstrap
# Day to day:    make up | make down
# Reset:         make fresh

.PHONY: help up down migrate seed web-seed web-db-reset bootstrap fresh test logs flush-flags

help: ## Show available targets
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-12s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up:  ## Start the stack
	docker compose up -d

down: ## Stop the stack
	docker compose down

migrate: ## Run pending migrations
	docker compose exec api php artisan migrate

seed: ## Run database seeders
	docker compose exec api php artisan db:seed

web-seed: ## Seed the Next.js SQLite database (skipped if reports already exist)
	docker compose exec web npx tsx scripts/seed.ts

web-db-reset: ## Wipe and re-seed the Next.js SQLite database
	docker compose exec web rm -f reports.db
	$(MAKE) web-seed

bootstrap: up migrate seed web-seed ## First-time setup: start, migrate, seed

fresh: ## Wipe volumes and start clean (rebuilds images)
	docker compose down -v
	$(MAKE) up
	$(MAKE) migrate seed web-seed

test: ## Run the API test suite
	docker compose exec api php artisan test

flush-flags: ## Invalidate the flag cache (use after editing flags directly in the DB)
	docker compose exec api php artisan cache:forget flags:index:v2

logs: ## Tail logs from every service
	docker compose logs -f
