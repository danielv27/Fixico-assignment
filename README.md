# Fixico Feature Flag Assignment

Laravel admin/API in `api/`, Next.js client in `web/`, all wired through Docker Compose.

## Run Locally

```bash
docker compose up --build
```

| Service | URL |
| --- | --- |
| Web client | http://localhost:3001 |
| Laravel API | http://localhost:8000 |
| Postgres | localhost:5433 |
| Redis | localhost:6379 |

All dependencies (Composer + npm) are installed at image build time, so the host checkout stays clean. After changing application code, rebuild the affected service:

```bash
docker compose up --build api    # or web
```

The API container generates an `APP_KEY` at build, runs migrations on boot, then serves Laravel on `:8000`.

## Common Commands

```bash
docker compose exec api php artisan test --compact
docker compose exec api vendor/bin/pint --dirty --format agent
docker compose exec web npm run lint
docker compose exec web npm run build
```

## Project Structure

- `api/` — Laravel API (Pest, Pint, Boost).
- `web/` — Next.js app router client.
- `docker-compose.yml` — Postgres, Redis, API, web.
- `AGENTS.md` — shared instructions for coding agents.

## Assignment Scope

- Feature flag CRUD in Laravel (no off-the-shelf packages like Pennant).
- A client-facing API endpoint for flag statuses.
- A Next.js car damage report UI that conditionally renders based on flags.
- At least one advanced rollout mechanism beyond a simple boolean.
- Scheduled activation and expiration.
- Redis-backed caching for flag reads.
- Documented behavior for stale UI when a flag flips after a user has loaded a page.
