# Config

- `config.example.php` is tracked and uses placeholders.
- Copy to `config.local.php` and replace with real values for local use.
- `config.local.php` is gitignored and must never be committed.
- You can also override any config with environment variables.

Common env vars:

- `FORTE_ENV` (production or sandbox)
- `FORTE_CONFIG_PATH` (absolute or repo-relative path to a config file)
- `FORTE_BASE_URL`, `FORTE_BASE_URL_PRODUCTION`, `FORTE_BASE_URL_SANDBOX`
- `FORTE_JS_URL`, `FORTE_JS_URL_PRODUCTION`, `FORTE_JS_URL_SANDBOX`
- `FORTE_PG_ACTION_URL`, `FORTE_PG_ACTION_URL_PRODUCTION`, `FORTE_PG_ACTION_URL_SANDBOX`
- `FORTE_SWP_BASE_URL`, `FORTE_SWP_BASE_URL_PRODUCTION`, `FORTE_SWP_BASE_URL_SANDBOX`
- `FORTE_API_ACCESS_ID`, `FORTE_API_SECURE_KEY`, `FORTE_API_LOGIN_ID`, `FORTE_SECURE_TRANSACTION_KEY`
- `FORTE_ORGANIZATION_ID`, `FORTE_LOCATION_ID`
- `FORTE_PG_PASSWORD`, `FORTE_PG_MERCHANT_ID`, `FORTE_PG_PAYMENT_TOKEN`, `FORTE_PG_CUSTOMER_TOKEN`
- `FRESHDESK_API_KEY`, `FRESHDESK_DOMAIN`, `FRESHDESK_PASSWORD`
- `HTML2PDF_API_KEY`
- `FORTE_TEST_CARD_NUMBER`, `FORTE_TEST_CARD_EXP_MONTH`, `FORTE_TEST_CARD_EXP_YEAR`, `FORTE_TEST_CARD_CVV`
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_CARD_TYPE`, `FORTE_TEST_CARD_NAME` (optional metadata for AGI/SWP flows)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_ACH_ROUTING`, `FORTE_TEST_ACH_ACCOUNT`, `FORTE_TEST_ACH_ACCOUNT_TYPE` (`C` or `S`), `FORTE_TEST_ACH_ENTRY_CLASS`
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_SERVICE_FEE_AMOUNT` (optional per‑transaction service fee)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_FORCE_TRANSACTION_PAYLOAD` (optional JSON string or file path for force transactions)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_ONE_TIME_TOKEN_PAYLOAD` (optional JSON string or file path for one-time token transactions)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_LOCATION_UPDATE_PAYLOAD` (optional JSON string or file path for location address updates)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_LOCATION_LIMITS_PAYLOAD` (optional JSON string or file path for processing limit updates)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_APPLICATION_PAYLOAD` (optional JSON string or file path for application create)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_PAYPAL_BILLING_TOKEN` (optional PayPal billing agreement token for customer/paymethod flows)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_PAYMETHOD_ONE_TIME_CARD_PAYLOAD` (optional JSON string or file path for one-time card paymethod)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_PAYMETHOD_ONE_TIME_ECHECK_PAYLOAD` (optional JSON string or file path for one-time echeck paymethod)
  - You can also set env-specific overrides with `_SANDBOX` / `_PRODUCTION` suffixes.
- `FORTE_TEST_TRANSACTION_*_PAYLOAD` (optional JSON string or file path for transaction variants)
  - Examples: `FORTE_TEST_TRANSACTION_PAYPAL_PAYLOAD`, `FORTE_TEST_TRANSACTION_DIGITAL_WALLET_INITIAL_PAYLOAD`,
    `FORTE_TEST_TRANSACTION_SWIPED_PAYLOAD`, `FORTE_TEST_TRANSACTION_EMV_EDYNAMO_PAYLOAD`,
    `FORTE_TEST_TRANSACTION_SURCHARGE_PAYLOAD` (all support `_SANDBOX` / `_PRODUCTION` suffixes).
- `FORTE_TEST_VENDOR_*` (optional JSON string or file path for PayPal vendor flows)
  - Examples: `FORTE_TEST_VENDOR_ACCOUNT_ID`, `FORTE_TEST_VENDOR_AGREEMENT_PAYLOAD`,
    `FORTE_TEST_VENDOR_ORDER_PAYLOAD`, `FORTE_TEST_VENDOR_ORDER_UPDATE_PAYLOAD` (all support `_SANDBOX` / `_PRODUCTION` suffixes).
- `FORTE_TEST_PAYLOADS_PATH` (optional JSON file with payload templates)
  - See `api-demo-php-harness/config/payload-templates.example.json`.
- `FORTE_WEBHOOK_URL` (optional webhook receiver URL for smoke tests)
- `FORTE_IMPORTER_SAMPLE_CSV` (optional CSV path for importer smoke tests)
- `FORTE_RISK_TAG_KEY` (optional risk tag key for `img3.forte.net` scripts)

Bootstrap usage:

```php
require_once __DIR__ . '/config/bootstrap.php';
```

The bootstrap exports common globals (`$base_url`, `$organization_id`, `$location_id`,
`$api_access_id`, `$api_secure_key`, `$api_login_id`, `$pg_password`, etc.) and the helper:

```php
forte_config('api_access_id');
```

For scripts that need distinct secure transaction keys or merchant IDs, use arrays:

```php
$secure_keys = (array)forte_config('secure_transaction_keys', []);
$secure_key = $secure_keys['fco_james'] ?? forte_config('secure_transaction_key');

$merchant_ids = (array)forte_config('merchant_ids', []);
$merchant_id = $merchant_ids['soap_hash'] ?? forte_config('pg_merchant_id');
```

## Credential groups (by surface)

Local-only grouping of credentials by surface (REST, SWP, SOAP, AGI) lives in:

- `api-demo-php-harness/config/credential-groups.local.json`

This file is generated by:

```bash
python3 tools/build_credential_groups.py \
  --review api-demo-php-harness/config/config.review.local.json \
  --sandbox api-demo-php-harness/config/config.review.sandbox.tested.json \
  --production api-demo-php-harness/config/config.review.production.tested.json \
  --sandbox-extra api-demo-php-harness/config/config.review.sandbox.300382-173185.tested.json \
  --swp api-demo-php-harness/config/config.review.swp.tested.json \
  --agi api-demo-php-harness/config/config.review.agi.tested.json \
  --soap api-demo-php-harness/config/config.review.soap.tested.json \
  --output api-demo-php-harness/config/credential-groups.local.json
```

`config.local.php` will load this JSON automatically and expose it under:

```
forte_config('credential_groups')
```

Groups are categorized as `working`, `partial`, or `not_working`. Names include
environment + org + location (e.g., `prod-300382-173185-rest-v3`).
