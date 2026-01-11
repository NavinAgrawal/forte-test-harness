<!--
File: docs/TEST_RUNBOOK.md
Description: Step-by-step runbook for running tests and updating dashboards
Author: Navin Balmukund Agrawal
Created: 2026-01-11
Confidentiality: Internal / Do Not Distribute
-->

# Test Runbook

This runbook explains how to run the unit and integration tests, when to update dashboards, and
how to keep production usage safe.

## Prerequisites

- PHP + Composer dependencies installed (`make setup`).
- Local config in `api-demo-php-harness/config/config.local.php` (never committed).
- For integration tests, valid sandbox credentials and test data sources (see `docs/TEST_DATA_SOURCES.md`).

## Unit tests

Run fast unit coverage (no network):

```bash
make test-php
make test-python
```

Or run both:

```bash
make test
```

## Integration tests (sandbox)

Integration tests hit real endpoints and must use sandbox credentials.

```bash
make test-integration
```

Notes:
- Tests will skip if required credentials are missing.
- All transactions should use the lowest amounts (0.01 / 0.02) and be voided immediately.
- Keep cleanup rules in `docs/TEST_DATA_POLICY.md`.

## Integration tests (production)

Production testing should be deliberate and minimal. Use the lowest amounts and remove data quickly.
Set `FORTE_ENV=production` and ensure `config.local.php` has production credentials.

```bash
FORTE_ENV=production make test-integration
```

## Dashboards

After any test/coverage change, regenerate dashboards:

```bash
make dashboards
```

This updates:
- `docs/coverage-dashboard.html`
- `docs/test-dashboard.html`
- `docs/php-inventory.html`

## Failure triage

1. **Credential errors**: Move to the bottom of `docs/TODO.md` under Blocked.
2. **Endpoint errors**: Capture status + response, update the test case with notes.
3. **Cleanup errors**: Record IDs locally and mark for follow‑up cleanup.

## Required inputs checklist

- REST: `FORTE_API_ACCESS_ID`, `FORTE_API_SECURE_KEY`, `FORTE_ORGANIZATION_ID`, `FORTE_LOCATION_ID`.
- AGI: `FORTE_PG_MERCHANT_ID`, `FORTE_PG_PASSWORD`.
- SWP: `FORTE_API_LOGIN_ID`, `FORTE_SECURE_TRANSACTION_KEY`.
- SOAP: `FORTE_API_ACCESS_ID`, `FORTE_API_SECURE_KEY`, `FORTE_ORGANIZATION_ID`, `FORTE_LOCATION_ID`.
- Non‑REST extras: webhook URL, importer CSV, risk tag key, Freshdesk/HTML2PDF API keys.

## Safety reminders

- Do not commit credentials or logs containing PII.
- Always run `make sanitize-check` before pushing.
- Use git hooks: `make hooks`.
