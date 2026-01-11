<!--
File: docs/PHP_SCRIPT_GUIDE.md
Description: How to use the PHP demo scripts and what surfaces they touch
Author: Navin Balmukund Agrawal
Created: 2026-01-11
Confidentiality: Internal / Do Not Distribute
-->

# PHP Script Guide

This harness includes many PHP demo scripts. They cover more than REST endpoints—there are scripts for
Forte Checkout, Forte.js tokenization, SWP, AGI (Payments Gateway), and utility workflows.

## Quick start

1. Configure credentials in `api-demo-php-harness/config/config.local.php`.
2. Start the local PHP server:

```bash
make run
```

3. Open the main index:

```
http://localhost:8080/
```

## What the scripts cover

- **REST v3 APIs**: CRUD flows for transactions, customers, paymethods, schedules, etc.
- **Forte Checkout (FCO)**: Hosted checkout form examples and signature generation.
- **Forte.js / Tokenization**: On‑page tokenization demos.
- **SWP (Simple Web Payments)**: Redirect/embedded SWP flows.
- **AGI (Payments Gateway)**: Postauth/posttest flows using `pg_merchant_id` and `pg_password`.
- **SOAP Helpers**: Local SOAP examples and SoapUI related helpers.
- **Internal Toolbox**: Legacy internal utilities (importer/exporter, reporting helpers).

See `docs/php-inventory.html` for a script‑by‑script dashboard including surfaces and REST resources.

## Central configuration

All scripts should load `api-demo-php-harness/config/bootstrap.php` and read credentials via:

```php
forte_config('api_access_id');
```

Never hardcode credentials directly into scripts. Use `config.local.php` or `.env` only.

## Notes

- The scripts are intentionally simple and optimized for demo/testing, not production.
- Always use the **lowest amounts** in tests (0.01 / 0.02) and reverse/void immediately.
- For SOAP projects, use `make soap-properties` to export `local.properties` from config.
