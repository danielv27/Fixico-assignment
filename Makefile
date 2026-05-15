# Common workflows for the fixico-assignment stack.
#
# First clone:   make bootstrap
# Day to day:    make up | make down
# Reset:         make fresh

.PHONY: help up down migrate seed bootstrap fresh test

help: ## Show available targets
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-12s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up:  ## Start the stack
	docker compose up -d

down: ## Stop the stack
	docker compose down

migrate: ## Run pending migrations and seed API data
	docker compose exec api php artisan migrate --seed

seed: ## Seed databases (FeatureFlag API + Web app damage reports)
	docker compose exec api php artisan db:seed
	docker compose exec web npm run seed

bootstrap: ## Build and start the stack; containers migrate and seed themselves
	docker compose up --build -d
	$(MAKE) seed

fresh: ## Rebuild from scratch with empty volumes
	docker compose down -v
	docker compose up --build -d

test: ## Run the API test suite
	docker compose exec api php artisan test
