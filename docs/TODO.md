# High-Priority TODO (Top 20)

Status keys: [ ] not started, [~] in progress, [x] done

1. [x] Define test data policy (idempotent create/delete rules, cleanup windows, naming conventions).
2. [x] Decide integration test runner (PHPUnit integration group) and standardize it.
3. [x] Build REST client helper (auth headers, base URL selection, retries, logging) for integration tests.
4. [x] Implement integration test framework scaffolding (env loading, test discovery, reporting, CI targets).
5. [x] Add integration tests for Transactions endpoints (all POST/GET/PUT/DELETE in v3 collection).
6. [x] Add integration tests for Customers endpoints (create/read/update/delete and list/filter).
7. [x] Add integration tests for Paymethods endpoints (tokenized card/ACH flows).
8. [x] Add integration tests for Schedules endpoints (create/list/update/delete).
9. [x] Add integration tests for Scheduleitems endpoints.
10. [x] Add integration tests for Applications endpoints.
11. [x] Add integration tests for Settlements endpoints.
12. [x] Add integration tests for Documents endpoints (upload/read/delete).
13. [x] Add integration tests for Addresses endpoints.
14. [x] Add integration tests for Locations endpoints.
15. [x] Add integration tests for Organizations endpoints.
16. [x] Add integration tests for Fundings endpoints.
17. [x] Add integration tests for Disputes endpoints.
18. [x] Add integration tests for Vendors endpoints.
19. [x] Build non-REST integration tests (AGI/SWP/SOAP/FCO/Forte.js/Risk/Routing/Webhooks/Freshdesk/HTML2PDF/Importer).
20. [ ] Collect sandbox credentials + test org/location IDs for full integration testing. (needs input)

Notes:
- SWP/AGI/SOAP credential check scripts exist; next step is running them with real test data + wiring results into integration tests.

Notes:
- All integration tests must use real config, infra, and data; no mocks or fake data.
- Update dashboards after each milestone: `make dashboards`.

## Blocked / Needs Input (move to bottom)

- [ ] Non-REST inputs missing (webhook URL, importer CSV path, risk tag key) and API keys (Freshdesk, HTML2PDF).
- [ ] Non-REST creds missing in `api-demo-php-harness/config/config.local.php` (pg_merchant_id, pg_password, secure_transaction_key/api_login_id). Needed to run AGI/SWP/SOAP tests.
- [ ] AGI production auth failing with `E10 INVALID MERCH OR PASSWD` for `pg_merchant_id=173185` (TEST1234). Need correct prod password or confirm merchant setup.
- [ ] AGI sandbox auth failing with `E10 INVALID MERCH OR PASSWD` for `pg_merchant_id=173185` (TEST1234). If correct, confirm whether convenience fee is required or provide sandbox creds.
