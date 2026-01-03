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

## SoapUI projects

Import the XMLs into SoapUI:

- `soap-projects/sandbox/` — sandbox targets
- `soap-projects/production/` — production targets (placeholders only)
- `soap-projects/templates/` — reusable templates

Before running, replace placeholders (e.g., `YOUR_API_ACCESS_ID`, `YOUR_PG_PASSWORD`, `org_xxxxx`, `loc_xxxxx`).

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

## Data hygiene

- Do not add raw exports, logs, or uploads containing customer data.
- Keep internal URLs and credentials out of git.
