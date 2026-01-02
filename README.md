# Forte Test Harness

Internal test harness for Forte demos and SoapUI SOAP projects.

## Contents

- `api-demo-php-harness/` — PHP demo pages (Forte Checkout, REST v3, SWP, utilities)
  - `internal-toolbox/` — legacy internal tooling (renamed from `toolbox1`)
- `soap-projects/` — SoapUI XML projects
  - `sandbox/`, `production/`, `templates/`
- `docs/` — project guidance
- `tools/` — maintenance scripts

## Quick start (PHP demos)

```bash
php -S localhost:8080 -t api-demo-php-harness
```

Open `http://localhost:8080/` in a browser.

## Security

This repo is sanitized. Replace placeholders locally only and never commit credentials or IDs.
