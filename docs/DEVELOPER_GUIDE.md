<!--
File: docs/DEVELOPER_GUIDE.md
Description: Developer guide and maintenance playbook for Forte test harness
Author: Navin Balmukund Agrawal
Created: 2026-01-02
Confidentiality: Internal / Do Not Distribute
-->

# Developer Guide & Maintenance Playbook

## Local setup (macOS/Linux)

1. Ensure PHP is installed:
   - `php -v`
2. Create a local config:
   - `make config`
   - Edit `api-demo-php-harness/config/config.local.php` with your credentials.
3. Start a local server from repo root:
   - `make run`
4. Open examples:
   - Main demos: `http://localhost:8080/`
   - Internal toolbox: `http://localhost:8080/internal-toolbox/index.php`
   - See `docs/PHP_SCRIPT_GUIDE.md` for script coverage by surface.

## SoapUI projects

Import the XMLs into SoapUI:

- `soap-projects/sandbox/` — sandbox targets
- `soap-projects/production/` — production targets (placeholders only)
- `soap-projects/templates/` — reusable templates

Before running, replace placeholders (e.g., `YOUR_API_ACCESS_ID`, `YOUR_PG_PASSWORD`, `org_xxxxx`, `loc_xxxxx`).

SoapUI central config (recommended):

- The XMLs reference project properties for `pg_merchant_id` and `pg_password`.
- Generate `soap-projects/local.properties` from `config.local.php`:

```bash
make soap-properties
```

Then load `soap-projects/local.properties` in SoapUI project properties.

## Central config and placeholders

This repo uses placeholders and a central config. To run locally:

- Put real values in `api-demo-php-harness/config/config.local.php` (gitignored).
- You can also override values via environment variables (see `api-demo-php-harness/config/README.md`).
- SoapUI XMLs use placeholders; replace locally in SoapUI or via project properties.

## Sanitization

Before committing changes to demo artifacts, run:

```bash
python3 tools/sanitize_placeholders.py api-demo-php-harness soap-projects
```

## Dashboards (WIP, keep current)

The HTML dashboards in `docs/` are living status snapshots and must be updated whenever
coverage or tests change:

- `docs/coverage-dashboard.html`
- `docs/test-dashboard.html`
- `docs/php-inventory.html`

Regenerate with:

```bash
make dashboards
```

The generator uses `FORTE_POSTMAN_COLLECTION` if set; otherwise it looks for
`Original - Forte REST API v3-collection.json` in the repo root or the default DevTools path.

## Testing Policy (Production Quality)

- Integration tests are required for every REST and non‑REST endpoint.
- No mocks or fake data: tests must use real credentials, real endpoints, and real data.
- 100% pass rate is the definition of “ready”.
- For destructive operations, use dedicated sandbox accounts and idempotent setup/teardown.
- Follow `docs/TEST_DATA_POLICY.md` for naming, cleanup, and data safety rules.

This is tracked in `docs/test-dashboard.html` and must be kept current.

See `docs/TEST_RUNBOOK.md` for step-by-step execution and safety checks.
See `docs/CREDENTIAL_GROUPS.md` for naming and validation buckets.
See `docs/SECURITY_SAFEGUARDS.md` for repo safety guardrails.

### Running sandbox integration tests

Integration cases live in `tests/php/integration/rest_sandbox_cases.json`.

Run:

```bash
make test-integration
```

This sets `FORTE_ENV=sandbox` and uses `api-demo-php-harness/config/config.local.php` by default.

## Data hygiene

- Do not add raw exports, logs, or uploads containing customer data.
- Keep internal URLs and credentials out of git.
- See `docs/TEST_DATA_SOURCES.md` for local-only test data sources.

## Knowledge base references

Local reference roots (do not commit copies of these files into the repo):

- `/Users/nba/Library/CloudStorage/OneDrive-CSGSystemsInc/Documents/SolutionEngineering/Knowledge Base/`
- `/Users/nba/Library/CloudStorage/OneDrive-CSGSystemsInc/Documents/SolutionEngineering/ECOSYSTEM/Webtools`

See `docs/KB_RESOURCES.md` for curated Forte docs used by this project.
