#!/usr/bin/env python3
"""Redact credentials/IDs in demo artifacts before committing.

Replaces known credential fields and tokens with placeholders.
"""
import argparse
import os
import re
import subprocess


TEXT_EXTS = {
    ".php", ".html", ".htm", ".js", ".css", ".xml", ".txt", ".csv", ".md", ".json", ".wsa"
}

REPLACEMENTS = [
    (re.compile(r"(api_access_id\w*\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_API_ACCESS_ID\3"),
    (re.compile(r"(api_secure_key\w*\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_API_SECURE_KEY\3"),
    (re.compile(r"(api_login_id\w*\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_API_LOGIN_ID\3"),
    (re.compile(r"(APILoginID\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_API_LOGIN_ID\3"),
    (re.compile(r"(pg_api_login_id\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_API_LOGIN_ID\3"),
    (re.compile(r"(name=['\"]pg_api_login_id['\"]\s+value=['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_API_LOGIN_ID\3"),
    (re.compile(r"(forte-api-login-id=['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_API_LOGIN_ID\3"),
    (re.compile(r"(<[^>]*APILoginID[^>]*>)([^<]+)(</[^>]*APILoginID>)", re.IGNORECASE), r"\1YOUR_API_LOGIN_ID\3"),
    (re.compile(r"(name=['\"]ecom_merchant_api_id['\"]\s+value=['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_API_LOGIN_ID\3"),
    (re.compile(r"(APIKey\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_API_KEY\3"),
    (re.compile(r"(SecureTransactionKey\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_SECURE_TRANSACTION_KEY\3"),
    (re.compile(r"(secure_transaction_key\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_SECURE_TRANSACTION_KEY\3"),
    (re.compile(r"((?:organization_id|org_id)\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1org_xxxxx\3"),
    (re.compile(r"((?:location_id|loc_id)\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1loc_xxxxx\3"),
    (re.compile(r"(customer_token\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1cst_xxxxx\3"),
    (re.compile(r"(paymethod_token\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1mth_xxxxx\3"),
    (re.compile(r"(transaction_id\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1trn_xxxxx\3"),
    (re.compile(r"(pg_password=)([^|]+)", re.IGNORECASE), r"\1YOUR_PG_PASSWORD"),
    (re.compile(r"(<[^>]*pg_password[^>]*>)([^<]+)(</[^>]*pg_password>)", re.IGNORECASE), r"\1YOUR_PG_PASSWORD\3"),
    (re.compile(r"(name=['\"]pg_password['\"]\s+value=['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_PG_PASSWORD\3"),
    (re.compile(r"(pg_merchant_id=)([^|]+)", re.IGNORECASE), r"\1YOUR_PG_MERCHANT_ID"),
    (re.compile(r"(<[^>]*pg_merchant_id[^>]*>)([^<]+)(</[^>]*pg_merchant_id>)", re.IGNORECASE), r"\1YOUR_PG_MERCHANT_ID\3"),
    (re.compile(r"(name=['\"]pg_merchant_id['\"]\s+value=['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1YOUR_PG_MERCHANT_ID\3"),
    (re.compile(r"(pg_payment_token=)([^|]+)", re.IGNORECASE), r"\1YOUR_PG_PAYMENT_TOKEN"),
    (re.compile(r"(<[^>]*pg_payment_token[^>]*>)([^<]+)(</[^>]*pg_payment_token>)", re.IGNORECASE), r"\1YOUR_PG_PAYMENT_TOKEN\3"),
    (re.compile(r"(pg_customer_token=)([^|]+)", re.IGNORECASE), r"\1YOUR_PG_CUSTOMER_TOKEN"),
    (re.compile(r"(<[^>]*pg_customer_token[^>]*>)([^<]+)(</[^>]*pg_customer_token>)", re.IGNORECASE), r"\1YOUR_PG_CUSTOMER_TOKEN\3"),
    (re.compile(r"((?:routing_number|account_number|bank_account_number|ecom_payment_check_trn|ecom_payment_check_account)\s*[:=]\s*['\"])([^'\"]+)(['\"])", re.IGNORECASE), r"\1000000000\3"),
    (re.compile(r"(ecom_payment_check_trn=)(\d+)", re.IGNORECASE), r"\g<1>000000000"),
    (re.compile(r"(ecom_payment_check_account=)(\d+)", re.IGNORECASE), r"\g<1>0000000000"),
    (re.compile(r"(\borg_)[A-Za-z0-9]+\b"), r"org_xxxxx"),
    (re.compile(r"(\bloc_)[A-Za-z0-9]+\b"), r"loc_xxxxx"),
    (re.compile(r"(\bcst_)[A-Za-z0-9]+\b"), r"cst_xxxxx"),
    (re.compile(r"(\btrn_)[A-Za-z0-9-]+\b"), r"trn_xxxxx"),
    (re.compile(r"(\bmth_)[A-Za-z0-9]+\b"), r"mth_xxxxx"),
    (re.compile(r"\b[a-f0-9]{64}\b", re.IGNORECASE), "REDACTED_HASH"),
    (re.compile(r"\b[a-f0-9]{32}\b", re.IGNORECASE), "REDACTED_HASH"),
]


def should_process(path: str) -> bool:
    _, ext = os.path.splitext(path)
    return ext.lower() in TEXT_EXTS


def sanitize_text(text: str) -> str:
    new_text = text
    for pattern, repl in REPLACEMENTS:
        def _replace(match):
            if match.lastindex and match.lastindex >= 2:
                value = match.group(2)
                if value and ("<?" in value or "?>" in value or "$" in value):
                    return match.group(0)
            return match.expand(repl)

        new_text = pattern.sub(_replace, new_text)
    return new_text


def process_file(path: str, check: bool = False) -> bool:
    if not should_process(path):
        return False
    try:
        with open(path, "rb") as f:
            data = f.read()
    except Exception:
        return False
    if b"\x00" in data:
        return False
    try:
        text = data.decode("utf-8")
        encoding = "utf-8"
    except UnicodeDecodeError:
        try:
            text = data.decode("latin-1")
            encoding = "latin-1"
        except UnicodeDecodeError:
            return False
    new_text = sanitize_text(text)
    if new_text != text:
        if not check:
            with open(path, "w", encoding=encoding) as f:
                f.write(new_text)
        return True
    return False


def walk_and_sanitize(root: str, check: bool = False, changed_paths=None, allowed_paths=None) -> int:
    changed = 0
    for dirpath, _, filenames in os.walk(root):
        for name in filenames:
            path = os.path.join(dirpath, name)
            if allowed_paths is not None and os.path.normpath(path) not in allowed_paths:
                continue
            if process_file(path, check=check):
                changed += 1
                if changed_paths is not None:
                    changed_paths.append(path)
    return changed


def git_tracked_files() -> set[str]:
    try:
        repo_root = subprocess.check_output(
            ["git", "rev-parse", "--show-toplevel"], text=True, stderr=subprocess.DEVNULL
        ).strip()
        output = subprocess.check_output(
            ["git", "ls-files", "-z"], cwd=repo_root
        )
    except Exception:
        return set()

    tracked = set()
    for raw in output.split(b"\x00"):
        if not raw:
            continue
        rel = raw.decode("utf-8", errors="ignore")
        path = os.path.normpath(os.path.join(repo_root, rel))
        tracked.add(path)
    return tracked


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "--check",
        action="store_true",
        help="Detect files that would be updated without modifying them.",
    )
    parser.add_argument(
        "--tracked-only",
        action="store_true",
        help="Only scan files tracked by git (ignores gitignored local files).",
    )
    parser.add_argument("roots", nargs="+", help="Root folders to sanitize")
    args = parser.parse_args()

    total_changed = 0
    changed_paths = []
    tracked_paths = git_tracked_files() if args.tracked_only else None
    for root in args.roots:
        if not os.path.isdir(root):
            print(f"[WARN] Not a directory: {root}")
            continue
        allowed = None
        if tracked_paths is not None:
            root_abs = os.path.normpath(os.path.abspath(root))
            allowed = {p for p in tracked_paths if p.startswith(root_abs + os.sep) or p == root_abs}
        per_root_paths = [] if args.check else None
        changed = walk_and_sanitize(
            root,
            check=args.check,
            changed_paths=per_root_paths,
            allowed_paths=allowed,
        )
        print(f"[INFO] {root}: updated {changed} files")
        total_changed += changed
        if args.check and per_root_paths:
            changed_paths.extend(per_root_paths)

    print(f"[DONE] total updated files: {total_changed}")
    if args.check and total_changed > 0:
        for path in changed_paths:
            print(f"[CHECK] would update: {path}")
        raise SystemExit(1)


if __name__ == "__main__":
    main()
