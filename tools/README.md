# Tools

- `sanitize_placeholders.py`: Redacts credentials/IDs in demo artifacts before commit.
  - Usage: `python3 tools/sanitize_placeholders.py api-demo-php-harness soap-projects`
  - Safety check (tracked only): `python3 tools/sanitize_placeholders.py --check --tracked-only api-demo-php-harness soap-projects`
- `generate_dashboards.py`: Regenerates HTML dashboards in `docs/`.
  - Usage: `python3 tools/generate_dashboards.py` or `make dashboards`
- `generate_soap_properties.py`: Exports SoapUI `local.properties` from `config.local.php` (gitignored).
  - Usage: `python3 tools/generate_soap_properties.py`
- `test_rest_groups.py`: Runs lightweight REST list checks for credential groups (no SSL verify by default).
  - Usage: `python3 tools/test_rest_groups.py --input ... --output ... --env sandbox`
- `test_swp_groups.py`: Runs SWP hosted page checks for credential groups.
  - Usage: `python3 tools/test_swp_groups.py --input ... --output ... --env sandbox`
- `test_agi_groups.py`: Runs AGI (posttest/postauth) checks for credential groups (credit + EFT + immediate void).
  - Usage: `python3 tools/test_agi_groups.py --input ... --output ... --env sandbox`
  - Requires env vars: `FORTE_TEST_CARD_*` and `FORTE_TEST_ACH_*` (see `api-demo-php-harness/config/README.md`)
  - Optional env-specific overrides: `*_SANDBOX` / `*_PRODUCTION`
- `test_soap_groups.py`: Sends a SOAP request per group to validate credentials.
  - Usage: `python3 tools/test_soap_groups.py --input ... --output ... --env sandbox`
- `build_credential_groups.py`: Builds local-only grouped credentials by surface/status.
  - Usage: `make credential-groups`
- `secret_*_paths*.txt`: Local scan outputs for candidate secret locations (generated). These are ignored by git.

Keep this folder for maintenance utilities and local scan artifacts.
