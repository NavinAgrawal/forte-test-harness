<!--
File: SESSION_HISTORY.md
Description: Session log for Forte test harness
Author: Navin Balmukund Agrawal
Created: 2026-01-02
Confidentiality: Internal / Do Not Distribute
-->

# Session History

## 2026-01-02

- Initialized repo structure under `forte-test-harness/`.
- Copied PHP demo pages into `api-demo-php-harness/` and SoapUI XMLs into `soap-projects/`.
- Renamed `toolbox1` to `internal-toolbox` and updated all references.
- Removed secret-bearing files from git history and added `.gitignore` entries to keep local copies untracked.
- Removed internal-only files (uploads, working files, logs) from the repo.
- Added project scaffolding docs and agent rules.

## 2026-01-11

- Centralized REST integration coverage and updated dashboards to show 100% REST endpoint coverage.
- Added non-REST integration scaffolding (AGI/SWP/SOAP + smoke tests for FCO/Forte.js/Risk/Routing/Webhooks/Freshdesk/HTML2PDF/Importer).
- Added local payload templates and env-driven payload support for integration tests.
- Hardened git safety checks (sanitize tracked files only) and updated pre-push hook.
- Documented KB references, test data sources, and expanded config/env guide.
- Added PHP inventory dashboard generator and published `docs/php-inventory.html`.
- Ensured PHPUnit bootstrap loads Composer autoload to prevent php-parser coverage failures.
- Added unit tests and coverage reporting for the PHP inventory generator.
- Documented project structure and folder responsibilities.
- Added a PHP script usage guide for surfaces and local running.
