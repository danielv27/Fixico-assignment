# Fixico Web

Next.js client application for the feature flag service.

Use the root `README.md` for the Docker-first setup. Useful frontend commands:

```bash
docker compose exec web npm run lint
docker compose exec web npm run build
```

The client should read the API base URL from `API_URL`.
