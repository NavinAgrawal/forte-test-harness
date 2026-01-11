#!/usr/bin/env python3
"""Test SWP credential groups by posting to the hosted payment page."""
from __future__ import annotations

import argparse
import hashlib
import hmac
import json
import re
import ssl
import time
from pathlib import Path
from urllib import parse, request, error


ERROR_PATTERNS = [
    "invalid",
    "unauthorized",
    "error",
    "failed",
    "denied",
    "hash",
]


def detect_env(file_path: str, fallback: str) -> str:
    lower = file_path.lower()
    if "sandbox" in lower:
        return "sandbox"
    if "production" in lower:
        return "production"
    return fallback


def utc_time() -> str:
    # mimic .NET ticks used in sample scripts
    ticks = int((time.time() * 10000) + 621355968000000000)
    return str(ticks)


def compute_hash(login_id: str, trans_type: str, version: str, amount: str, utc: str, order: str, key: str) -> str:
    data = f"{login_id}|{trans_type}|{version}|{amount}|{utc}|{order}"
    return hmac.new(key.encode("utf-8"), data.encode("utf-8"), hashlib.md5).hexdigest()


def is_real_value(value: str | None) -> bool:
    if not value:
        return False
    if any(token in value for token in ("<?php", "?>", "YOUR_", "?")):
        return False
    if set(value) == {"*"}:
        return False
    return True


def extract_swp_values(path: str) -> dict:
    file_path = Path(path) if path else None
    if not file_path or not file_path.is_file():
        return {}
    text = file_path.read_text(encoding="utf-8", errors="ignore")
    values = {}
    for pattern in (
        r"api_login_id\\s*=\\s*'([^']+)'",
        r"APILoginID[^>]*>([^<]+)</",
        r"pg_api_login_id[^>]*value=['\\\"]([^'\\\"]+)['\\\"]",
        r"api_login_id=['\\\"]([^'\\\"]+)['\\\"]",
    ):
        match = re.search(pattern, text, re.IGNORECASE)
        if match and is_real_value(match.group(1)):
            values["api_login_id"] = match.group(1)
            break
    for pattern in (
        r"secure_transaction_key\\s*=\\s*'([^']+)'",
        r"secure_trans_key\\s*=\\s*'([^']+)'",
        r"SecureTransKey\\s*=\\s*'([^']+)'",
    ):
        match = re.search(pattern, text, re.IGNORECASE)
        if match and is_real_value(match.group(1)):
            values["secure_transaction_key"] = match.group(1)
            break
    return values


def post_form(url: str, payload: dict, verify_ssl: bool) -> tuple[int | str, str]:
    ctx = ssl.create_default_context() if verify_ssl else ssl._create_unverified_context()
    data = parse.urlencode(payload).encode("utf-8")
    req = request.Request(url, data=data, method="POST")
    req.add_header("Content-Type", "application/x-www-form-urlencoded")
    try:
        with request.urlopen(req, context=ctx, timeout=30) as resp:
            body = resp.read().decode("utf-8", errors="ignore")
            return resp.status, body
    except error.HTTPError as exc:
        body = exc.read().decode("utf-8", errors="ignore") if exc.fp else ""
        return exc.code, body
    except Exception as exc:
        return f"error:{exc.__class__.__name__}", ""


def classify(status: int | str, body: str, error: str | None) -> str:
    if error:
        return "partial"
    if status in (401, 403):
        return "not_working"
    if isinstance(status, str) and status.startswith("error:"):
        return "partial"
    lower = body.lower()
    if any(pat in lower for pat in ERROR_PATTERNS):
        return "not_working"
    return "working"


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", required=True)
    parser.add_argument("--output", required=True)
    parser.add_argument("--env", choices=["sandbox", "production"], default="sandbox")
    parser.add_argument("--verify-ssl", action="store_true")
    parser.add_argument("--ignore-file-env", action="store_true", help="Ignore file path env hints")
    parser.add_argument("--ids", nargs="*", default=[])
    parser.add_argument("--limit", type=int, default=0)
    args = parser.parse_args()

    review = json.loads(Path(args.input).read_text())
    groups = [g for g in review.get("groups", []) if g.get("surface") == "PAYMENTS_GATEWAY_SWP"]

    if args.ids:
        id_set = set(args.ids)
        groups = [g for g in groups if g.get("id") in id_set]
    if args.limit:
        groups = groups[: args.limit]

    results = {
        "generated_from": args.input,
        "environment": args.env,
        "verify_ssl": args.verify_ssl,
        "results": [],
    }

    for group in groups:
        values = group.get("values", {}) or {}
        login_id = values.get("api_login_id")
        secure_key = values.get("secure_transaction_key")
        if not (is_real_value(login_id) and is_real_value(secure_key)):
            extracted = extract_swp_values(group.get("file", ""))
            login_id = login_id if is_real_value(login_id) else extracted.get("api_login_id")
            secure_key = secure_key if is_real_value(secure_key) else extracted.get("secure_transaction_key")
        error_note = None
        if not login_id or not secure_key:
            error_note = "missing required swp credentials"

        env = args.env if args.ignore_file_env else detect_env(group.get("file", ""), args.env)
        base = "https://sandbox.paymentsgateway.net/swp" if env == "sandbox" else "https://swp.paymentsgateway.net"
        url = f"{base}/default.aspx"

        trans_type = "10"
        version = "2.0"
        amount = "0.01"
        order = f"TEST-{int(time.time())}"
        utc = utc_time()
        ts_hash = compute_hash(login_id, trans_type, version, amount, utc, order, secure_key) if not error_note else ""

        payload = {
            "pg_api_login_id": login_id,
            "pg_transaction_type": trans_type,
            "pg_version_number": version,
            "pg_total_amount": amount,
            "pg_utc_time": utc,
            "pg_transaction_order_number": order,
            "pg_ts_hash": ts_hash,
            "pg_billto_postal_name_first": "Forte",
            "pg_billto_postal_name_last": "Tester",
            "pg_billto_postal_street_line1": "500 Bethany Dr",
            "pg_billto_postal_city": "Allen",
            "pg_billto_postal_state": "TX",
            "pg_billto_postal_postalcode": "75013",
            "pg_billto_online_email": "integration@forte.net",
        }

        status, body = post_form(url, payload, args.verify_ssl) if not error_note else ("error:missing_credentials", "")
        result_status = classify(status, body, error_note)

        results["results"].append({
            "id": group.get("id"),
            "surface": "SWP",
            "file": group.get("file"),
            "environment": env,
            "api_login_id": login_id,
            "pg_merchant_id": values.get("pg_merchant_id"),
            "status": result_status,
            "http_status": status,
            "error": error_note,
        })

    Path(args.output).write_text(json.dumps(results, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
