# Day to day:    make up | make down
# Reset:         make fresh

.PHONY: help up down fresh test

help: ## Show available targets
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-12s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up:  ## Start the stack
	docker compose up -d

down: ## Stop the stack
	docker compose down

fresh: ## Rebuild from scratch with empty volumes
	docker compose down -v
	docker compose up --build -d

test: ## Run the API test suite
	docker compose exec api php artisan test
