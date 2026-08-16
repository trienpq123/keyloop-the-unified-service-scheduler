# Keyloop Unified Service Scheduler

A Laravel and PostgreSQL backend for booking dealership service appointments.
An appointment is confirmed only when a qualified technician and a service bay
are available for the entire service duration.

## What is included

- Guest appointment booking with customer and vehicle resolution.
- Availability checks, deterministic allocation, PostgreSQL locking, and overlap prevention.
- Idempotent `POST` requests, business hours, cancellation, structured JSON logs, and request correlation.
- OpenAPI 3.0 documentation, executable cURL examples, SQLite feature tests, and a PostgreSQL concurrency harness.

## Requirements

- Docker Desktop with Docker Compose v2.

No host PHP, Composer, Node, or PostgreSQL installation is required.

## Build and run

```bash
cp .env.example .env
docker compose run --rm app composer install --no-interaction --prefer-dist
docker compose up -d
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --seed --force
docker compose exec app php artisan l5-swagger:generate
```

The API is then available at `http://localhost:8000` and Swagger UI at
`http://localhost:8000/api/documentation`.

Useful commands:

```bash
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
docker compose exec app php artisan l5-swagger:generate
docker compose logs -f app
docker compose down
```

It demonstrates advisory availability, a successful booking, idempotency
replay, changed-payload conflict, and validation failure. The date in the
harness is deliberately explicit; replace it with an open Monday–Friday slot
when running after August 2026.

The public endpoints are:

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | `/api/v1/dealerships` | List dealerships |
| `GET` | `/api/v1/dealerships/{dealership}/availability` | Advisory availability check |
| `POST` | `/api/v1/appointments` | Create/replay an idempotent appointment |
| `GET` | `/api/v1/appointments/{appointment}` | Retrieve an appointment |
| `PATCH` | `/api/v1/appointments/{appointment}/cancel` | Cancel an appointment and release resources |

All API responses use a stable `success`/`data` or `success`/`error` envelope
and include a request identifier in `meta.request_id`.

## Testing and verification

```bash
docker compose exec app php artisan test
```


## Design and AI collaboration

- [System design](../docs/system_design.md)
- [AI collaboration narrative](docs/AI_COLLABORATION_NARRATIVE.md)

## Troubleshooting

- **Port is already in use:** change `APP_PORT` or `FORWARD_DB_PORT` in `.env`.
- **Database not ready:** wait for `docker compose ps` to report PostgreSQL as healthy, then rerun the migrate command.
- **Vendor directory missing:** run the `docker compose run --rm app composer install ...` command above.
- **Swagger is stale:** rerun `docker compose exec app php artisan l5-swagger:generate`.
- **Start over locally:** `docker compose down -v` removes this project’s database volume.
