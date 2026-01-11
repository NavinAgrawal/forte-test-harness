#!/usr/bin/env python3
"""Test SOAP credential groups by sending a sample SOAP request."""
from __future__ import annotations

import argparse
import hashlib
import hmac
import json
import re
import ssl
import time
from pathlib import Path
from urllib import error, request


ERROR_PATTERNS = [
    "invalid login",
    "invalid api",
    "invalid apiloginid",
    "invalid tshash",
    "authentication",
    "unauthorized",
    "security",
]


REQUEST_RE = re.compile(r"<con:request><!\\[CDATA\\[(.*?)\\]\\]></con:request>", re.S)
ENDPOINT_RE = re.compile(r"<con:endpoint>([^<]+)</con:endpoint>")
ACTION_RE = re.compile(r"<con:operation[^>]*?action=\"([^\"]+)\"[^>]*>", re.S)


def detect_env(file_path: str, fallback: str) -> str:
    lower = file_path.lower()
    if "sandbox" in lower:
        return "sandbox"
    if "production" in lower:
        return "production"
    return fallback


def utc_ticks() -> str:
    return str(int((time.time() * 10000) + 621355968000000000))


def compute_tshash(login_id: str, utc: str, key: str) -> str:
    data = f"{login_id}|{utc}"
    return hmac.new(key.encode("utf-8"), data.encode("utf-8"), hashlib.md5).hexdigest()


def extract_hash_pairs(path: Path) -> dict[str, dict]:
    if not path.is_file():
        return {}
    text = path.read_text(encoding="utf-8", errors="ignore")
    pattern = re.compile(
        r"\\$merchantID\\s*=\\s*([0-9]+).*?\\$APILoginID\\s*=\\s*'([^']+)'.*?\\$SecureTransKey\\s*=\\s*'([^']+)'",
        re.S,
    )
    mapping = {}
    for match in pattern.finditer(text):
        merchant_id, login_id, key = match.group(1), match.group(2), match.group(3)
        mapping[login_id] = {"merchant_id": merchant_id, "secure_transaction_key": key}
    return mapping


def is_real_value(value: str | None) -> bool:
    if not value:
        return False
    if any(token in value for token in ("<?php", "?>", "YOUR_", "?")):
        return False
    if set(value) == {"*"}:
        return False
    return True


def extract_soap_values(path: str) -> dict:
    file_path = Path(path) if path else None
    if not file_path or not file_path.is_file():
        return {}
    text = file_path.read_text(encoding="utf-8", errors="ignore")
    values = {}
    for pattern in (
        r"<v1:APILoginID>([^<]+)</v1:APILoginID>",
        r"APILoginID[^>]*>([^<]+)</",
    ):
        match = re.search(pattern, text, re.IGNORECASE)
        if match and is_real_value(match.group(1)):
            values["api_login_id"] = match.group(1)
            break
    for pattern in (
        r"<v1:MerchantID>([^<]+)</v1:MerchantID>",
        r"<v1:MerchantIDs>([^<]+)</v1:MerchantIDs>",
    ):
        match = re.search(pattern, text, re.IGNORECASE)
        if match and is_real_value(match.group(1)):
            values["pg_merchant_id"] = match.group(1)
            break
    return values


def choose_request(xml_text: str) -> tuple[str | None, str | None, str | None]:
    endpoints = ENDPOINT_RE.findall(xml_text)
    endpoint = endpoints[0] if endpoints else None
    action = None
    actions = ACTION_RE.findall(xml_text)
    if actions:
        action = actions[0]

    candidates = []
    for match in REQUEST_RE.finditer(xml_text):
        request_xml = match.group(1)
        if "APILoginID" not in request_xml:
            continue
        if "MerchantID" not in request_xml and "MerchantIDs" not in request_xml:
            continue
        score = request_xml.count("?>") + request_xml.count(">?</")
        candidates.append((score, request_xml))

    if candidates:
        candidates.sort(key=lambda item: item[0])
        return endpoint, action, candidates[0][1]

    # Fallback: first request
    match = REQUEST_RE.search(xml_text)
    if match:
        return endpoint, action, match.group(1)
    return endpoint, action, None


def replace_placeholders(xml: str, login_id: str, merchant_id: str, ts_hash: str, utc: str) -> str:
    out = xml
    out = re.sub(r"<v1:APILoginID>[^<]*</v1:APILoginID>", f"<v1:APILoginID>{login_id}</v1:APILoginID>", out)
    out = re.sub(r"<v1:TSHash>[^<]*</v1:TSHash>", f"<v1:TSHash>{ts_hash}</v1:TSHash>", out)
    out = re.sub(r"<v1:UTCTime>[^<]*</v1:UTCTime>", f"<v1:UTCTime>{utc}</v1:UTCTime>", out)
    out = re.sub(r"<v1:MerchantID>[^<]*</v1:MerchantID>", f"<v1:MerchantID>{merchant_id}</v1:MerchantID>", out)
    out = re.sub(r"<v1:MerchantIDs>[^<]*</v1:MerchantIDs>", f"<v1:MerchantIDs>{merchant_id}</v1:MerchantIDs>", out)
    out = out.replace("YOUR_API_LOGIN_ID", login_id)

    def fill_tag(match):
        tag = match.group(1)
        if tag in ("StartDate", "EndDate", "Day"):
            return f"<v1:{tag}>2020-01-01</v1:{tag}>"
        if tag == "PageIndex":
            return "<v1:PageIndex>0</v1:PageIndex>"
        if tag == "PageSize":
            return "<v1:PageSize>50</v1:PageSize>"
        return f"<v1:{tag}>0</v1:{tag}>"

    out = re.sub(r"<v1:([A-Za-z0-9_]+)>\\?</v1:\\1>", fill_tag, out)
    return out


def post_soap(url: str, action: str | None, body: str, verify_ssl: bool) -> tuple[int | str, str]:
    ctx = ssl.create_default_context() if verify_ssl else ssl._create_unverified_context()
    data = body.encode("utf-8")
    req = request.Request(url, data=data, method="POST")
    req.add_header("Content-Type", "text/xml; charset=utf-8")
    if action:
        req.add_header("SOAPAction", action)
    try:
        with request.urlopen(req, context=ctx, timeout=30) as resp:
            return resp.status, resp.read().decode("utf-8", errors="ignore")
    except error.HTTPError as exc:
        body = exc.read().decode("utf-8", errors="ignore") if exc.fp else ""
        return exc.code, body
    except Exception as exc:
        return f"error:{exc.__class__.__name__}", ""


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
    if "<fault" in lower:
        return "partial"
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
    parser.add_argument("--soap-hash", default="/Users/nba/Library/CloudStorage/OneDrive-CSGSystemsInc/Tools/DevTools/htdocs/SOAP-hash.php")
    args = parser.parse_args()

    review = json.loads(Path(args.input).read_text())
    groups = [g for g in review.get("groups", []) if g.get("surface") == "SOAP"]

    if args.ids:
        id_set = set(args.ids)
        groups = [g for g in groups if g.get("id") in id_set]
    if args.limit:
        groups = groups[: args.limit]

    hash_pairs = extract_hash_pairs(Path(args.soap_hash))

    results = {
        "generated_from": args.input,
        "environment": args.env,
        "verify_ssl": args.verify_ssl,
        "results": [],
    }

    for group in groups:
        file_path_lower = (group.get("file") or "").lower()
        if args.ignore_file_env:
            if args.env == "sandbox" and "sandbox" not in file_path_lower:
                continue
            if args.env == "production" and "production" not in file_path_lower:
                continue
        values = group.get("values", {}) or {}
        login_id = values.get("api_login_id")
        merchant_id = values.get("pg_merchant_id")
        if not (is_real_value(login_id) and is_real_value(merchant_id)):
            extracted = extract_soap_values(group.get("file", ""))
            login_id = login_id if is_real_value(login_id) else extracted.get("api_login_id")
            merchant_id = merchant_id if is_real_value(merchant_id) else extracted.get("pg_merchant_id")

        error_note = None
        if not is_real_value(login_id):
            error_note = "missing api_login_id"

        secure_key = values.get("secure_transaction_key")
        if not secure_key and login_id in hash_pairs:
            secure_key = hash_pairs[login_id].get("secure_transaction_key")
            if not merchant_id:
                merchant_id = hash_pairs[login_id].get("merchant_id")

        if not secure_key:
            error_note = "missing secure_transaction_key"
        if not is_real_value(merchant_id):
            error_note = "missing merchant_id"

        env = args.env if args.ignore_file_env else detect_env(group.get("file", ""), args.env)
        file_path = group.get("file")
        endpoint = None
        action = None
        request_xml = None
        if file_path and Path(file_path).is_file():
            xml_text = Path(file_path).read_text(encoding="utf-8", errors="ignore")
            endpoint, action, request_xml = choose_request(xml_text)

        if not endpoint or not request_xml:
            error_note = error_note or "missing soap request template"
        if args.ignore_file_env and endpoint:
            if env == "sandbox" and "sandbox" not in endpoint.lower():
                error_note = "endpoint not sandbox"
            if env == "production" and "sandbox" in endpoint.lower():
                error_note = "endpoint not production"

        utc = utc_ticks()
        ts_hash = compute_tshash(login_id or "", utc, secure_key or "") if not error_note else ""
        request_body = replace_placeholders(request_xml, login_id or "", merchant_id or "", ts_hash, utc) if request_xml else ""

        status, body = post_soap(endpoint, action, request_body, args.verify_ssl) if not error_note else ("error:missing_data", "")
        result_status = classify(status, body, error_note)

        results["results"].append({
            "id": group.get("id"),
            "surface": "SOAP",
            "file": group.get("file"),
            "environment": env,
            "endpoint": endpoint,
            "status": result_status,
            "http_status": status,
            "error": error_note,
        })

    Path(args.output).write_text(json.dumps(results, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
