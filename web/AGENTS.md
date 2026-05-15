# Next.js Client Notes

This directory contains the Next.js client application.

## Commands

Prefer running commands through Docker from the repository root:

```bash
docker compose exec web npm run lint
docker compose exec web npm run build
```

If working directly inside `web/`, use the same npm scripts after installing dependencies.

## Conventions

- Use the app router.
- Read `API_URL` for API calls.
- Keep flag-aware UI states explicit: loading, enabled, disabled, and stale interaction handling.
- Build the car damage report UI as the primary experience, not a landing page.
- If installed dependencies are present, check local Next.js docs before relying on memory for canary-specific APIs.
