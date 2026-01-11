<!--
File: docs/CREDENTIAL_GROUPS.md
Description: Credential grouping rules by surface and environment
Author: Navin Balmukund Agrawal
Created: 2026-01-11
Confidentiality: Internal / Do Not Distribute
-->

# Credential Groups

Credential groups keep related IDs and keys together so tests can select a valid set by surface.
Groups are local-only and live in `config.local.php` or `config/credential-groups.local.json`.

## Surfaces

- **REST**: `api_access_id`, `api_secure_key`, `organization_id`, `location_id`
- **SWP**: `api_login_id`, `secure_transaction_key`
- **SOAP**: `api_access_id`, `api_secure_key`, `organization_id`, `location_id`
- **AGI**: `pg_merchant_id`, `pg_password`

Some surfaces reuse the same keys (REST/SOAP) but are tracked separately to avoid cross‑surface confusion.

## Status buckets

Each surface uses three buckets:

- `working`: fully validated with live tests
- `partial`: validated for some endpoints only
- `not_working`: present in source but failed validation

## Naming convention

`<env>-<org>-<loc>-<surface>-<context>`

Examples:
- `sandbox-333251-191620-rest-onetime-to-transaction`
- `sandbox-349419-205845-rest-fco-rolando`
- `prod-300382-173185-rest-main`

AGI/SWP groups may omit org/loc if not applicable, but keep env + surface + context.

## Where to update

- Source review output: `api-demo-php-harness/config/config.review.local.json`
- Tested results (local-only): `config.review.*.tested.json`
- Generated groups: `api-demo-php-harness/config/credential-groups.local.json`

## Next validation steps

1. Identify candidate groups from source files.
2. Test them with `make rest-groups`, `make swp-groups`, `make agi-groups`, `make soap-groups`.
3. Move passing groups into `working`, partial into `partial`, failures into `not_working`.
4. Update `docs/TODO.md` and dashboards after each validation pass.
