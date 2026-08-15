# AI Collaboration Narrative

## Strategy

I used GenAI as a design and implementation collaborator, not as an authority.
I first converted the challenge into explicit acceptance criteria, invariants,
and a work-breakdown sequence. This made each generated change reviewable
against one concrete outcome.

## Where AI helped

- Identifying assumptions absent from the brief: half-open intervals,
  technician qualification, guest identity matching, idempotency, and local
  business-hour validation.
- Comparing a layered modular monolith with repository-heavy and microservice
  alternatives.
- Generating Laravel scaffolding, OpenAPI attributes, migrations, fixtures,
  and edge-case test ideas.
- Reviewing error envelopes, lock ordering, and PII-safe logging fields.

## Verification and ownership

Generated proposals were accepted only after checking them against the brief
and system design. I verified syntax, formatting, unit/feature tests, real
PostgreSQL migrations, live API behavior, generated OpenAPI JSON/YAML, and
request-correlated JSON logs. During review, I explicitly corrected issues
including Stringable-to-Carbon parsing, incomplete Swagger metadata, response
envelope consistency, idempotency key races, logging before transaction commit,
and overly large action methods.

The final decisions remain mine: PostgreSQL row locking is the concurrency
mechanism; SQLite is used only for fast feature tests; PostgreSQL contention is
documented with a reproducible harness; metrics and OpenTelemetry are future
signals rather than falsely claimed implementations.
