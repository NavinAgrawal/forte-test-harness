#!/usr/bin/env python3
"""Test AGI credential groups by posting to the Payments Gateway endpoint."""
from __future__ import annotations

import argparse
import json
import os
import re
import ssl
from pathlib import Path
from urllib import error, parse, request


ERROR_PATTERNS = [
    "invalid merchant",
    "invalid password",
    "authentication",
    "unauthorized",
    "denied",
]


def pick_env_value(base: str, env: str) -> str:
    env_key = f"{base}_{env.upper()}"
    value = os.environ.get(env_key)
    if value:
        return value
    return os.environ.get(base, "")


def is_real_value(value: str | None) -> bool:
    if not value:
        return False
    if any(token in value for token in ("<?php", "?>", "YOUR_", "?")):
        return False
    if set(value) == {"*"}:
        return False
    return True


def extract_agi_values(path: str) -> dict:
    file_path = Path(path) if path else None
    if not file_path or not file_path.is_file():
        return {}
    text = file_path.read_text(encoding="utf-8", errors="ignore")
    values = {}
    for pattern in (
        r"pg_merchant_id\\s*=\\s*'([^']+)'",
        r"name=['\\\"]pg_merchant_id['\\\"]\\s+value=['\\\"]([^'\\\"]+)['\\\"]",
        r"<[^>]*pg_merchant_id[^>]*>([^<]+)</",
    ):
        match = re.search(pattern, text, re.IGNORECASE)
        if match and is_real_value(match.group(1)):
            values["pg_merchant_id"] = match.group(1)
            break
    for pattern in (
        r"pg_password\\s*=\\s*'([^']+)'",
        r"name=['\\\"]pg_password['\\\"]\\s+value=['\\\"]([^'\\\"]+)['\\\"]",
        r"<[^>]*pg_password[^>]*>([^<]+)</",
    ):
        match = re.search(pattern, text, re.IGNORECASE)
        if match and is_real_value(match.group(1)):
            values["pg_password"] = match.group(1)
            break
    return values


def detect_env(file_path: str, fallback: str) -> str:
    lower = file_path.lower()
    if "sandbox" in lower:
        return "sandbox"
    if "production" in lower:
        return "production"
    return fallback


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


def parse_response_fields(body: str) -> dict:
    if not body:
        return {}
    normalized = body.replace("\r", "").replace("\n", "&")
    parsed = parse.parse_qs(normalized, keep_blank_values=True)
    fields = {k: v[0] if v else "" for k, v in parsed.items()}
    if fields:
        return fields
    fallback = {}
    for line in body.splitlines():
        if "=" not in line:
            continue
        key, value = line.split("=", 1)
        fallback[key.strip()] = value.strip()
    return fallback


def response_ok(status: int | str, fields: dict, body: str) -> bool:
    if status != 200:
        return False
    response_type = (fields.get("pg_response_type") or "").upper()
    if response_type:
        return response_type == "A"
    lower = body.lower()
    if any(pat in lower for pat in ERROR_PATTERNS):
        return False
    return "approved" in lower


def classify(status: int | str, body: str, error_note: str | None) -> str:
    if error_note:
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
    groups = [g for g in review.get("groups", []) if g.get("surface") == "PAYMENTS_GATEWAY_AGI"]
    if not groups:
        groups = [g for g in review.get("groups", []) if g.get("surface") == "PAYMENTS_GATEWAY_SWP" and "agi" in (g.get("file", "").lower())]

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

    card_type = os.environ.get("FORTE_TEST_CARD_TYPE", "visa")
    card_name = os.environ.get("FORTE_TEST_CARD_NAME", "Forte Test")

    for group in groups:
        values = group.get("values", {}) or {}
        merchant_id = values.get("pg_merchant_id")
        password = values.get("pg_password")
        if not (is_real_value(merchant_id) and is_real_value(password)):
            extracted = extract_agi_values(group.get("file", ""))
            merchant_id = merchant_id if is_real_value(merchant_id) else extracted.get("pg_merchant_id")
            password = password if is_real_value(password) else extracted.get("pg_password")
        env = args.env if args.ignore_file_env else detect_env(group.get("file", ""), args.env)
        url = "https://www.paymentsgateway.net/cgi-bin/posttest.pl" if env == "sandbox" else "https://www.paymentsgateway.net/cgi-bin/postauth.pl"

        error_note = None
        if not merchant_id or not password:
            error_note = "missing required agi credentials"
        errors = []
        tests = []
        attempted = 0
        ok_count = 0
        hard_fail = 0

        card_number = pick_env_value("FORTE_TEST_CARD_NUMBER", env)
        card_exp_month = pick_env_value("FORTE_TEST_CARD_EXP_MONTH", env)
        card_exp_year = pick_env_value("FORTE_TEST_CARD_EXP_YEAR", env)
        card_cvv = pick_env_value("FORTE_TEST_CARD_CVV", env)

        ach_routing = pick_env_value("FORTE_TEST_ACH_ROUTING", env)
        ach_account = pick_env_value("FORTE_TEST_ACH_ACCOUNT", env)
        ach_account_type = pick_env_value("FORTE_TEST_ACH_ACCOUNT_TYPE", env)
        ach_entry_class = pick_env_value("FORTE_TEST_ACH_ENTRY_CLASS", env)

        if ach_account_type:
            ach_account_type = ach_account_type.upper()

        def run_flow(flow: str, sale_type: str, void_type: str, payload_extra: dict, amount: str) -> None:
            nonlocal attempted, ok_count, hard_fail
            sale_payload = {
                "pg_merchant_id": merchant_id,
                "pg_password": password,
                "pg_transaction_type": sale_type,
                "pg_total_amount": amount,
                "ecom_billto_postal_name_first": "Forte",
                "ecom_billto_postal_name_last": "Tester",
                "ecom_billto_postal_postalcode": "75013",
            }
            sale_payload.update(payload_extra)

            attempted += 1
            sale_status, sale_body = post_form(url, sale_payload, args.verify_ssl)
            sale_fields = parse_response_fields(sale_body)
            sale_ok = response_ok(sale_status, sale_fields, sale_body)
            sale_error = sale_fields.get("pg_response_description") or None
            void_ok = False
            void_status = None
            void_error = None

            if sale_ok:
                trace = sale_fields.get("pg_trace_number", "")
                auth = sale_fields.get("pg_authorization_code", "")
                if not trace or not auth:
                    void_error = "missing trace/authorization for void"
                else:
                    void_payload = {
                        "pg_merchant_id": merchant_id,
                        "pg_password": password,
                        "pg_transaction_type": void_type,
                        "pg_original_trace_number": trace,
                        "pg_original_authorization_code": auth,
                    }
                    void_status, void_body = post_form(url, void_payload, args.verify_ssl)
                    void_fields = parse_response_fields(void_body)
                    void_ok = response_ok(void_status, void_fields, void_body)
                    void_error = void_fields.get("pg_response_description") or None
            else:
                void_error = "void skipped (sale failed)"

            if sale_ok and void_ok:
                ok_count += 1
            else:
                lower = (sale_body or "").lower()
                if sale_status in (401, 403) or any(pat in lower for pat in ERROR_PATTERNS):
                    hard_fail += 1

            tests.append({
                "flow": flow,
                "sale_type": sale_type,
                "void_type": void_type,
                "sale_ok": sale_ok,
                "sale_http_status": sale_status,
                "sale_response_type": sale_fields.get("pg_response_type"),
                "sale_response_code": sale_fields.get("pg_response_code"),
                "sale_response_description": sale_error,
                "void_ok": void_ok,
                "void_http_status": void_status,
                "void_response_description": void_error,
            })

        if not error_note:
            if card_number and card_exp_month and card_exp_year and card_cvv:
                run_flow(
                    "credit",
                    "10",
                    "14",
                    {
                        "ecom_payment_card_type": card_type,
                        "ecom_payment_card_name": card_name,
                        "ecom_payment_card_number": card_number,
                        "ecom_payment_card_expdate_month": card_exp_month,
                        "ecom_payment_card_expdate_year": card_exp_year,
                        "ecom_payment_card_verification": card_cvv,
                    },
                    "0.01",
                )
            else:
                errors.append("missing test card data (set FORTE_TEST_CARD_* env vars)")

            if ach_routing and ach_account and ach_account_type in {"C", "S"}:
                extra = {
                    "ecom_payment_check_trn": ach_routing,
                    "ecom_payment_check_account": ach_account,
                    "ecom_payment_check_account_type": ach_account_type,
                }
                if ach_entry_class:
                    extra["pg_entry_class_code"] = ach_entry_class
                run_flow("eft", "20", "24", extra, "0.02")
            else:
                errors.append("missing ACH data (set FORTE_TEST_ACH_* env vars)")

        if error_note:
            result_status = classify("error:missing_data", "", error_note)
        elif attempted == 0:
            result_status = "partial"
        elif ok_count == attempted and not errors:
            result_status = "working"
        elif hard_fail == attempted and ok_count == 0:
            result_status = "not_working"
        else:
            result_status = "partial"

        results["results"].append({
            "id": group.get("id"),
            "surface": "AGI",
            "file": group.get("file"),
            "environment": env,
            "pg_merchant_id": merchant_id,
            "status": result_status,
            "error": error_note,
            "notes": errors,
            "tests": tests,
        })

    Path(args.output).write_text(json.dumps(results, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
