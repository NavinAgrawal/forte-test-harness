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

Bootstrap usage:

```php
require_once __DIR__ . '/config/bootstrap.php';
```

The bootstrap exports common globals (`$base_url`, `$organization_id`, `$location_id`,
`$api_access_id`, `$api_secure_key`, `$api_login_id`, `$pg_password`, etc.) and the helper:

```php
forte_config('api_access_id');
```
