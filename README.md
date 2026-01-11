# Forte Test Harness

Internal test harness for Forte demos and SoapUI SOAP projects.

## Contents

- `api-demo-php-harness/` — PHP demo pages (Forte Checkout, REST v3, SWP, utilities)
  - `internal-toolbox/` — legacy internal tooling (renamed from `toolbox1`)
  - `config/` — central configuration (local values live in `config.local.php`)
- `soap-projects/` — SoapUI XML projects
  - `sandbox/`, `production/`, `templates/`
- `docs/` — project guidance
- `tools/` — maintenance scripts

## Quick start (PHP demos)

1. Copy the example config and edit locally (or run `make config`):

```bash
cp api-demo-php-harness/config/config.example.php api-demo-php-harness/config/config.local.php
```

2. Run a local PHP server:

```bash
php -S localhost:8080 -t api-demo-php-harness
```

Open `http://localhost:8080/` in a browser.

## Make targets

```bash
make setup
make config
make run
make test
make test-integration
```

## Integration tests (sandbox)

- Integration tests use real credentials and hit sandbox endpoints (no mocks).
- Ensure `api-demo-php-harness/config/config.local.php` is populated.
- Run:

```bash
make test-integration
```

## Security

- `config.local.php` is gitignored and must never be committed.
- Run `python3 tools/sanitize_placeholders.py api-demo-php-harness soap-projects` before committing demo artifacts.
- Enable the repo hook to block unsafe pushes: `git config core.hooksPath .githooks` (or `make hooks`).
- Do not commit credentials, IDs, or client data.

## Dashboards (WIP)

The HTML dashboards in `docs/` are the current status snapshots and should be kept up to date:

- `docs/coverage-dashboard.html` (REST + non-REST coverage)
- `docs/test-dashboard.html` (integration test status)
- `docs/php-inventory.html` (PHP script surface inventory)

Regenerate after changes with:

```bash
make dashboards
```

The generator reads the Postman collection from `FORTE_POSTMAN_COLLECTION` if set. If not set,
it will look for `Original - Forte REST API v3-collection.json` in the repo root or the default
DevTools path.
