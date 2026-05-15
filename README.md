# Fixico Feature Flag Service

A take-home assignment. Laravel handles the feature flag admin and evaluation API. Next.js is the client app — a car damage report tool that exercises the flags in practice.

## Running locally

```bash
docker compose up --build

# Optional convenience wrappers
make bootstrap   # build and start in the background
make up          # subsequent starts
make down        # stop
make fresh       # wipe all data and start clean
```

| URL | What |
|---|---|
| http://localhost:3001 | Next.js client |
| http://localhost:8000/admin | Flag admin |

## How it's structured

```
api/   Laravel — flag management UI, evaluation endpoint
web/   Next.js — damage reports client
```

The Laravel API owns feature flags entirely. The Next.js app owns damage reports in its own local SQLite database. The two apps are decoupled: the client calls one endpoint to evaluate which flags are on, then renders accordingly. The containers run migrations and seed data on startup, so `docker compose up --build` is enough for a fresh clone.

## Feature flags

A flag is **active** only when all of the following are true:

1. It is **enabled**
2. The current time is within its **schedule** window (`starts_at` → `ends_at`)
3. The request's **audience attributes** match its targeting rules (e.g. `country = NL`)
4. The user's **rollout bucket** is below the percentage threshold

Steps 3 and 4 are optional — a flag with no rules and no percentage cap is simply on for everyone.

### Rollout bucketing

Each user gets a stable UUID stored as a cookie. `abs(crc32(uuid)) % 100` maps that to a bucket 0–99. If the bucket is below the flag's percentage, the user is in. Same user always lands in the same bucket, no database needed.

This means a user at bucket 17 sees every flag with a threshold above 17 — they belong to a stable cohort. That's the right behaviour for a gradual rollout (everyone in the cohort gets the same experience) but not for statistically independent A/B tests. The simulator on the flag edit page makes this concrete.

### Caching

All flags are cached together in Redis under a single key. The cache is busted automatically whenever a flag is saved or deleted. TTL is a fallback for any writes that bypass the application layer.

### Expired flags

When a flag passes its `ends_at` date it stops being served to clients, but its `enabled` state is preserved. This keeps the history clean and lets you reopen a campaign simply by extending the expiry date. The admin shows a warning on expired flags and disables the toggle to avoid confusion.

## Demo User

The client has no real auth. A pill in the nav lets you switch country and role to simulate different users — this re-evaluates flags on the next request. In production these values would come from a session or JWT.

## Seeded flags

| Flag | Who sees it | Notes |
|---|---|---|
| `demo.banner` | Everyone | Simple on/off |
| `reports.bulk_actions` | `role = admin` | Admin-only feature |
| `form.description_first` | `country = NL`, 50% | Gradual rollout to NL |
| `reports.photo_attachments` | Everyone, 25% | Beta feature |
| `dashboard.v2` | Everyone, 20% | Scheduled — starts next week |
| `promo.winter_2024` | Everyone | Expired — Dec 2024 campaign |

## Conditionally rendered components

| Flag | Component | Condition | Effect |
|---|---|---|---|
| `demo.banner` | `DemoBanner` | On for everyone | Dismissable banner linking to the flag admin |
| `reports.bulk_actions` | `BulkActionsToolbar` | `role = admin` | Bulk-delete toolbar on the reports list |
| `form.description_first` | `ReportFormView` | `country = NL`, 50% rollout | Reorders the new and edit report forms to show damage description first |
| `reports.photo_attachments` | `PhotoAttachments` | Everyone, 25% rollout | Photo documentation section on the report detail page |

The live Next.js server actions re-check their feature flag immediately before mutating data, so a stale rendered control becomes a no-op with an inline error if the flag was disabled after the page loaded. Matching Laravel API endpoints are also feature-gated and return `410 Gone` when their flag is off:

| Flag | Endpoint |
|---|---|
| `reports.bulk_actions` | `DELETE /api/reports/bulk` |
| `reports.photo_attachments` | `POST /api/reports/{id}/photos` |

## Tests and linting

```bash
make test                                        # Pest test suite
docker compose exec api vendor/bin/pint --dirty  # PHP style
docker compose exec web npm run lint             # ESLint
docker compose exec web npm run build            # Next.js production build
```
