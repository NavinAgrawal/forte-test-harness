<!--
File: docs/TEST_DATA_POLICY.md
Description: Test data policy for live integration tests
Author: Navin Balmukund Agrawal
Created: 2026-01-06
Confidentiality: Internal / Do Not Distribute
-->

# Test Data Policy

This harness runs live integration tests against real Forte environments. All test data
must be safe, minimal, and easy to clean up.

## Core rules

- Use **real credentials** and **real endpoints** only.
- Do **not** use mocks or fake infrastructure.
- Keep transaction amounts at the **lowest denominations** (0.01 / 0.02).
- Use **idempotent naming** and **consistent tags** so data can be found and cleaned.
- Prefer **GET list + GET item** flows when no create/delete permission exists.

## Naming conventions

Use predictable prefixes for created objects:

- `forte-harness-<env>-<timestamp>-<resource>`
- Example: `forte-harness-sandbox-20260106-transaction`

Recommended labels/metadata:

- `developer_note`, `reference_id`, or `description` fields (where supported)
  should include the same prefix.

## Create / Update / Delete guidelines

- Create only what is required for the test.
- Update only fields that are safe and revertible.
- Delete created resources when possible.
- If delete is not allowed, record IDs in a local artifact file (gitignored).

## Cleanup windows

- Sandbox: clean up daily or after each test run.
- Production: clean up immediately or within 24 hours.

## Sensitive data

- Never store real PAN/CVV in the repo.
- Only use local `.env` / `config.local.php`.
- Do not commit logs with raw payloads.

## Tracking

- Update `docs/test-dashboard.html` after each integration milestone.
- Keep `docs/TEST_DATA_SOURCES.md` current with local data sources.
