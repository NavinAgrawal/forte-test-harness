<!--
File: docs/PROJECT_STRUCTURE.md
Description: High-level project structure and file responsibilities
Author: Navin Balmukund Agrawal
Created: 2026-01-11
Confidentiality: Internal / Do Not Distribute
-->

# Project Structure

This document explains the purpose of each top-level folder and the most important files.

## Top-level folders

- `api-demo-php-harness/`
  - PHP demo scripts for REST v3, SWP, AGI, Forte Checkout, Forte.js, and legacy utilities.
  - `internal-toolbox/` is the legacy toolbox (imports/exports, data exporter, customer tools).
  - `config/` contains the central configuration and templates.

- `soap-projects/`
  - SoapUI XML projects split by environment.
  - `sandbox/` for sandbox projects, `production/` for production templates.
  - `local.properties` is generated locally from `config.local.php` and is gitignored.

- `tests/`
  - PHPUnit integration tests (REST + non-REST smoke tests).
  - `tests/php/integration` contains REST case definitions and flow cases.

- `tools/`
  - Maintenance scripts for dashboards, credential grouping, SoapUI properties, and sanitization.
  - This folder exists to keep the test harness maintainable and safe for Git.

- `docs/`
  - Project documentation and dashboards (`coverage-dashboard.html`, `test-dashboard.html`, `php-inventory.html`).

## Key files

- `api-demo-php-harness/config/config.example.php`
  - Placeholder config for safe tracking in Git.
- `api-demo-php-harness/config/config.local.php`
  - Local-only secrets and credentials (gitignored).
- `.githooks/pre-push`
  - Prevents unsafe pushes (sanitization + tests).
- `Makefile`
  - Standard targets to run tests, dashboards, and utilities.

## Notes

- All scripts should use `forte_config()` from `api-demo-php-harness/config/bootstrap.php`.
- Keep credentials only in `config.local.php` or `.env` (never tracked).
- Use `make dashboards` after any test or coverage update.
