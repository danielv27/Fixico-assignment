# AGENTS.md

## Project Context

This repository is for the Fixico senior fullstack take-home assignment: a simple feature flag service with a Laravel admin/API in `api/` and a Next.js client in `web/`.

The assignment expects:

- Laravel for the admin environment and API.
- Next.js for the client application.
- A rudimentary car damage report UI.
- Feature flag CRUD and a client-facing flag status endpoint.
- At least one advanced rollout mechanism beyond a global boolean.
- Scheduled activation/expiration and a caching strategy.
- Clear local run instructions.

Do not use ready-made feature flag packages such as Laravel Pennant; the implementation should show the project-specific feature flag logic.

## Repository Rules

- Keep `docker compose up --build` as the one-command local setup path.
- Keep root documentation accurate whenever setup commands, ports, or services change.
- Do not commit local dependencies, build output, generated framework caches, `.env` files, or the assignment PDF.
- Prefer simple, explicit domain code over generic abstractions until duplication or complexity justifies more structure.
- Keep API and client contracts small and documented in code/tests.

## Stack Layout

- `api/`: Laravel API/admin app, Pest tests, Pint formatting.
- `web/`: Next.js app router client.
- `docker-compose.yml`: local Postgres, Redis, API, and web services.

## Common Commands

From the repository root:

```bash
docker compose up --build
docker compose exec api php artisan test --compact
docker compose exec api vendor/bin/pint --dirty --format agent
docker compose exec web npm run lint
docker compose exec web npm run build
```

If you work outside Docker, install dependencies inside the relevant subproject first and keep the same test/lint/build checks.

## Backend Notes

- Use migrations for schema changes.
- Use Redis-backed cache for flag evaluation results where appropriate.
- Use feature tests for API/admin behavior and focused unit tests for flag evaluation rules.
- Run Pint after changing PHP files.

## Frontend Notes

<!-- BEGIN:nextjs-agent-rules -->
 
### Next.js: ALWAYS read docs before coding
 
Before any Next.js work, find and read the relevant doc in `node_modules/next/dist/docs/`. Your training data is outdated — the docs are the source of truth.
 
<!-- END:nextjs-agent-rules -->

- The web app should consume the API through `NEXT_PUBLIC_API_BASE_URL`.
- Use conditionally rendered pages/components to demonstrate flag behavior.
- Keep the UI practical and workflow-focused for car damage report management.
