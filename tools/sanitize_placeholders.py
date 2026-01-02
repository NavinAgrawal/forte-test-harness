#!/usr/bin/env python3
"""Redact credentials/IDs in demo artifacts before committing.

Replaces known credential fields and tokens with placeholders.
"""
import argparse
import os
import re

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
        new_text = pattern.sub(repl, new_text)
    return new_text


def process_file(path: str) -> bool:
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
        with open(path, "w", encoding=encoding) as f:
            f.write(new_text)
        return True
    return False


def walk_and_sanitize(root: str) -> int:
    changed = 0
    for dirpath, _, filenames in os.walk(root):
        for name in filenames:
            path = os.path.join(dirpath, name)
            if process_file(path):
                changed += 1
    return changed


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("roots", nargs="+", help="Root folders to sanitize")
    args = parser.parse_args()

    total_changed = 0
    for root in args.roots:
        if not os.path.isdir(root):
            print(f"[WARN] Not a directory: {root}")
            continue
        changed = walk_and_sanitize(root)
        print(f"[INFO] {root}: updated {changed} files")
        total_changed += changed

    print(f"[DONE] total updated files: {total_changed}")


if __name__ == "__main__":
    main()
