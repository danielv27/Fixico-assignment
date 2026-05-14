# Fixico Feature Flag Assignment

Laravel admin/API at `api/`, Next.js client at `web/`, wired through Docker Compose.

## Run Locally

**First time:**
```bash
make bootstrap
```
Starts the stack, migrates, and seeds four demo flags + three damage reports.

**Day to day:**
```bash
make up      # start
make down    # stop
make fresh   # wipe volumes and rebuild from scratch
```

Run `make` (no args) to list all available targets.

| Service | URL |
|---|---|
| Next.js client | http://localhost:3001 |
| Laravel API / Admin | http://localhost:8000 |
| Postgres | localhost:5433 |
| Redis | localhost:6379 |

## Two Apps, One Stack

The assignment deliberately separates the admin environment (the back-end) from the client:

| App | Technology | Purpose |
|---|---|---|
| **Admin + API** | Laravel 13 | Flag management UI (Blade), JSON API |
| **Client** | Next.js 16 | Damage reports with conditional UI |

Open http://localhost:8000/admin/flags to manage flags.
Open http://localhost:3001 to use the damage reports client.
The client nav bar has an **Admin · Flags ↗** link.

## Feature Flags

### What's seeded

| Flag | Targeting | Rollout |
|---|---|---|
| `demo.banner` | all users | 100 % |
| `reports.bulk_actions` | `role = admin` | 100 % of admins |
| `report.new_form_layout` | `country = NL` | 50 % of NL users |
| `reports.photo_attachments` | all users | 25 % |

### Evaluation order

A flag evaluates to **true** only if all four steps pass:

1. **Master switch** — `enabled` must be true
2. **Schedule window** — current time must be inside `[starts_at, ends_at]` when set
3. **Attribute rules** — all `{attribute, values}` clauses must match the request context (AND logic; empty list = all subjects pass)
4. **Rollout percentage** — `crc32(subject + ":" + flagName) % 100 < rollout_percentage` (sticky per subject+flag pair, decorrelated across flags)

### Demo viewer switcher

The Next.js client has a **Demo viewer** control in the nav bar. It lets you flip `country` and `role` without building a full auth system — this stands in for an authenticated session and is explicitly called out here as a demo affordance.

### Stale-interaction handling

When a user sees a feature, the flag is disabled, and they try to interact:

1. The mutation endpoint re-checks the flag server-side.
2. If the flag is now off it returns `410 Gone` with `{"error": "feature_disabled", "flag": "..."}`.
3. The client surfaces a message: "This feature is no longer available."

**Try it:** Enable `reports.bulk_actions` for an admin viewer, see the bulk toolbar, then disable the flag in the admin, and click **Delete selected** — the 410 message appears without a page reload.

### Caching

Flags are cached in Redis under a single key (`flags:index:v2`) with a configurable TTL (default 300 s, override via `FEATURE_FLAGS_CACHE_TTL`). The key is invalidated immediately on any Eloquent write (via `FlagObserver`) so admin changes reflect within the next request.

> **Note:** direct SQL edits (e.g., via a DB GUI) bypass Eloquent events. Run `make flush-flags` after a raw SQL edit or wait for the TTL.

## Development Commands

```bash
make test                                                # API test suite (51 tests)
docker compose exec api vendor/bin/pint --dirty         # PHP code style
docker compose exec web npm run lint                     # Next.js lint
docker compose exec web npm run build                    # Next.js prod build
```
