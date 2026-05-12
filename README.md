# Fixico Feature Flag Assignment

Laravel API in `api/`, Next.js client in `web/`, wired through Docker Compose. Common workflows are driven by `make`.

## Run Locally

**First time:**
```bash
make bootstrap
```
Starts the stack, runs migrations, seeds demo data.

**Day to day:**
```bash
make up      # start
make down    # stop
make fresh   # wipe volumes and rebuild from scratch
```

Run `make` (no args) to list all targets.

| Service | URL |
| --- | --- |
| Web client | http://localhost:3001 |
| Laravel API | http://localhost:8000 |
| Postgres | localhost:5433 |
| Redis | localhost:6379 |

All dependencies (Composer + npm) install at image build time, so the host checkout stays clean.

## Common Tasks

```bash
make test                                  # API test suite
docker compose exec api vendor/bin/pint --dirty --format agent
docker compose exec web npm run lint
docker compose exec web npm run build
```

## Project Structure

- `api/` — Laravel API (Pest, Pint, Boost).
- `web/` — Next.js app router client.
- `docker-compose.yml` — Postgres, Redis, API, web.
- `Makefile` — workflow entry points.
- `AGENTS.md` — shared instructions for coding agents.

## Assignment Scope

- Feature flag CRUD in Laravel (no off-the-shelf packages like Pennant).
- A client-facing API endpoint for flag statuses.
- A Next.js car damage report UI that conditionally renders based on flags.
- At least one advanced rollout mechanism beyond a simple boolean.
- Scheduled activation and expiration.
- Redis-backed caching for flag reads.
- Documented behavior for stale UI when a flag flips after a user has loaded a page.
