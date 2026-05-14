# Fixico Feature Flag Service

A take-home assignment: Laravel admin + API in `api/`, Next.js client in `web/`, wired through Docker Compose.

## Run

```bash
make bootstrap   # first time
make up          # subsequent starts
make down        # stop
make fresh       # wipe volumes and rebuild
```

| URL | What |
|---|---|
| http://localhost:3001 | Next.js damage-report client |
| http://localhost:8000/admin | Laravel Blade admin |
| http://localhost:8000/api | JSON API |

## Architecture

| App | Stack | Responsibility |
|---|---|---|
| Laravel 13 | Blade + JSON API | Flag management UI, evaluation endpoint, reports API |
| Next.js 16 | React 19 RSC | Damage reports UI with flag-driven conditional components |

Admin lives in Laravel (the assignment says "admin environment (the back-end)"). The Next.js app is purely the client.

## Flag evaluation

A flag is **on** only if all four steps pass, in order:

1. `enabled` master switch
2. inside `[starts_at, ends_at]` (schedule)
3. `attribute_rules` match the request context (AND of `{attribute, values}` clauses)
4. subject's bucket `< rollout_percentage`

### Rollout strategy — `abs(crc32(subject)) % 100`

A subject is whatever identifies a user — in the demo, a per-browser UUID stored as a cookie. CRC32 of that string maps deterministically to a bucket 0–99. If the bucket falls below the percentage threshold, the subject is in the rollout.

**Why CRC32**: deterministic, fast, uniform — same subject always yields the same bucket, no storage needed. Not a cryptographic hash, which is fine because flags aren't a security mechanism.

**Subject-only, not subject+flag**: the hash deliberately does **not** include the flag name. A user in bucket 17 sees every flag whose threshold is `> 17` — they're part of a stable cohort. This is right for product rollouts (gradual exposure to a beta cohort) but would be wrong for rigorous A/B testing where you need statistical independence between treatments. The simulator on the edit page makes this visual: drag the slider and you see exactly which subjects are in.

**In production**: subjects would be v4 UUIDs (122 bits of randomness) which distribute uniformly across the 100 buckets naturally. The synthetic 200-user grid in the admin is purely illustrative.

## Caching

Single Redis key (`flags:index`) holds every flag. `FlagObserver` busts it on any Eloquent write so admin changes reflect on the next request. TTL (default 300s, override via `FEATURE_FLAGS_CACHE_TTL`) is a fallback for writes that bypass Eloquent.

The cache stores plain arrays, not Eloquent models — `unserialize()` doesn't trigger the autoloader for unknown classes, which would produce `__PHP_Incomplete_Class` on the second hit. Arrays round-trip cleanly; the cache hydrates non-persisted `FeatureFlag` instances on read.

## Stale-interaction handling

When a user sees a feature, the flag flips off mid-session, and they try to act — the mutation endpoint re-evaluates the flag and returns **`410 Gone`** with `{"error": "feature_disabled", "flag": "..."}`. The client surfaces an inline message. 410 (not 403) because the resource existed and is no longer available.

Two endpoints are gated this way via a `feature_flag:` route middleware:

- `DELETE /api/reports/bulk` — gated by `reports.bulk_actions`
- `POST /api/reports/{id}/photos` — gated by `reports.photo_attachments`

## Demo viewer

The Next.js client has no authentication; a viewer pill in the nav (`Viewing as NL · customer`) stands in for an authenticated session. Switching country or role re-evaluates flags on the next request. In production the evaluation context would come from the JWT/session.

## Seeded flags

| Flag | Targeting | Rollout |
|---|---|---|
| `demo.banner` | all users | 100% |
| `reports.bulk_actions` | `role = admin` | 100% of admins |
| `report.new_form_layout` | `country = NL` | 50% of NL users |
| `reports.photo_attachments` | all users | 25% |

## Tests

```bash
make test                                            # 75 Pest tests
docker compose exec api vendor/bin/pint --dirty      # PHP style
docker compose exec web npm run lint                 # ESLint
```

Coverage: evaluator unit tests (all four pipeline steps, edge cases, percentage distribution), FlagCache (read-through, observer-triggered invalidation), admin Blade web flows (CRUD, validation, cache flush, redirect, nested-form regression), JSON API admin CRUD, evaluation endpoint, 410 enforcement.
