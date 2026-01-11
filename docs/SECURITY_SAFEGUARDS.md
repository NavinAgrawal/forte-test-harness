<!--
File: docs/SECURITY_SAFEGUARDS.md
Description: Repo safeguards to prevent credential leaks
Author: Navin Balmukund Agrawal
Created: 2026-01-11
Confidentiality: Internal / Do Not Distribute
-->

# Security Safeguards

This repo contains demo/test harness scripts that historically included credentials. The following
safeguards ensure we do not leak secrets to Git.

## Hard rules

- **Never commit** `config.local.php` or `.env`.
- Never commit logs, exports, or raw payloads containing customer data.
- Keep all credentials in local-only files and env vars.

## Automated checks

- **Sanitizer**: `tools/sanitize_placeholders.py` detects and redacts sensitive patterns.
- **Git hook**: `.githooks/pre-push` runs sanitizer + tests before any push.

Enable hooks:

```bash
make hooks
```

## Safe workflow

1. Make changes.
2. Run `make verify` (sanitize-check + tests).
4. Push (hook runs steps 2–3 automatically).

## Redaction patterns

The sanitizer replaces:
- API keys, access IDs, secure keys
- Org/location identifiers
- Merchant IDs, passwords
- Known sandbox test card/ACH values

## Recovery

If a sensitive file accidentally becomes tracked:

1. Stop and remove it from git tracking:

```bash
git rm --cached path/to/file
```

2. Add it to `.gitignore`.
3. Sanitize history only if already pushed.

## Notes

When in doubt, treat any credential-like string as sensitive and keep it local only.
