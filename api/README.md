# Fixico API

Laravel admin/API application for the feature flag service.

Use the root `README.md` for the Docker-first setup. Useful backend commands:

```bash
docker compose exec api php artisan test --compact
docker compose exec api vendor/bin/pint --dirty --format agent
docker compose exec api php artisan route:list --except-vendor
```
