# Fixico Feature Flag Service

- Feature flag admin panel and evaluation API built in Laravel.
- Client app that demonstrates conditional flag rendering in Next.js.

## Running locally

```bash
make up    # Start docker containers
make down  # Stop docker containers
make test  # Test
make fresh # Reset DBs (Restarts and wipes volumes)
```

| URL | What |
|---|---|
| http://localhost:8000/admin | Admin Panel |
| http://localhost:3001 | Next.js client |


## How it's structured
The project is a mono repo with the folloing components:
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

The same control shows the user's rollout bucket (`0`–`99`). The frontend mirrors the API's `crc32(user_id) % 100` calculation so the demo can show why a user does or does not land in percentage-based flags. Re-rolling creates a new demo user id and therefore a new bucket.

## Seeded flags

| Flag | Who sees it | Notes |
|---|---|---|
| `demo.banner` | Everyone | Simple on/off |
| `reports.bulk_actions` | `role = admin` | Admin-only feature |
| `form.description_first` | `country = NL`, 50% | Gradual rollout to NL |
| `reports.photo_attachments` | Everyone, 25% | Beta feature |
| `reports.repair_timeline` | Everyone, 20% | Scheduled — starts next week |
| `promo.winter_2024` | Everyone | Expired — Dec 2024 campaign |

## Conditionally rendered components

| Flag | Component | Condition | Effect |
|---|---|---|---|
| `demo.banner` | `DemoBanner` | On for everyone | Dismissable banner linking to the flag admin |
| `reports.bulk_actions` | `BulkActionsToolbar` | `role = admin` | Bulk-delete toolbar on the reports list |
| `form.description_first` | `ReportFormView` | `country = NL`, 50% rollout | Reorders the new and edit report forms to show damage description first |
| `reports.photo_attachments` | `PhotoAttachments` | Everyone, 25% rollout | Photo documentation section on the report detail page |

## Running linters

```bash
docker compose exec api vendor/bin/pint --dirty  # PHP style
docker compose exec web npm run lint             # ESLint
```
