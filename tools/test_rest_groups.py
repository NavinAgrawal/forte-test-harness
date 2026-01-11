#!/usr/bin/env python3
"""Test REST credential groups against sandbox/production list endpoints.

This script reads local review files generated from DevTools sources and runs
lightweight GET requests to list endpoints to verify credential usability.
Secrets are never written to output files.
"""
from __future__ import annotations

import argparse
import base64
import json
import ssl
import sys
from pathlib import Path
from typing import Dict, List
from urllib import request, error

ROOT = Path(__file__).resolve().parents[1]

DEFAULT_ENDPOINTS = [
    ("Organizations", "GET", "/organizations"),
    ("Organizations", "GET", "/organizations/{org}"),
    ("Locations", "GET", "/organizations/{org}/locations"),
    ("Locations", "GET", "/organizations/{org}/locations/{loc}"),
    ("Transactions", "GET", "/organizations/{org}/locations/{loc}/transactions"),
    ("Customers", "GET", "/organizations/{org}/locations/{loc}/customers"),
    ("Paymethods", "GET", "/organizations/{org}/locations/{loc}/paymethods"),
    ("Schedules", "GET", "/organizations/{org}/locations/{loc}/schedules"),
    ("Scheduleitems", "GET", "/organizations/{org}/locations/{loc}/scheduleitems"),
    ("Settlements", "GET", "/organizations/{org}/settlements"),
]


def normalize_id(value: str, prefix: str) -> str:
    if not value:
        return ""
    if value.startswith(prefix):
        return value
    if value.isdigit():
        return f"{prefix}{value}"
    return value


def select_base_url(group: dict, env: str, ignore_group_base: bool) -> str:
    base_url = (group.get("values", {}) or {}).get("base_url") or ""
    if base_url and not ignore_group_base:
        return base_url.rstrip("/")
    if env == "sandbox":
        return "https://sandbox.forte.net/api/v3"
    return "https://api.forte.net/v3"


def request_status(url: str, token: str, org_id: str, verify_ssl: bool) -> int | str:
    ctx = ssl.create_default_context() if verify_ssl else ssl._create_unverified_context()
    req = request.Request(url, method="GET")
    req.add_header("Authorization", f"Basic {token}")
    req.add_header("X-Forte-Auth-Organization-Id", org_id)
    req.add_header("Accept", "application/json")
    try:
        with request.urlopen(req, context=ctx, timeout=20) as resp:
            return resp.status
    except error.HTTPError as exc:
        return exc.code
    except Exception as exc:  # pragma: no cover - network/runtime errors
        return f"error:{exc.__class__.__name__}"


def test_group(group: dict, env: str, verify_ssl: bool, ignore_group_base: bool) -> dict:
    values = group.get("values", {}) or {}
    api_access_id = values.get("api_access_id")
    api_secure_key = values.get("api_secure_key")
    org_id = normalize_id(values.get("organization_id") or "", "org_")
    loc_id = normalize_id(values.get("location_id") or "", "loc_")

    result = {
        "id": group.get("id"),
        "surface": group.get("surface"),
        "file": group.get("file"),
        "environment": env,
        "organization_id": org_id,
        "location_id": loc_id,
        "base_url": select_base_url(group, env, ignore_group_base),
        "statuses": [],
    }

    if not api_access_id or not api_secure_key or not org_id:
        result["error"] = "missing required REST credentials"
        return result

    token = base64.b64encode(f"{api_access_id}:{api_secure_key}".encode("utf-8")).decode("ascii")

    for category, _method, path in DEFAULT_ENDPOINTS:
        url = result["base_url"] + path.format(org=org_id, loc=loc_id)
        status = request_status(url, token, org_id, verify_ssl)
        result["statuses"].append({
            "category": category,
            "path": path,
            "status": status,
        })

    return result


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", required=True, help="Path to grouped review JSON")
    parser.add_argument("--output", required=True, help="Path to write results JSON")
    parser.add_argument("--env", choices=["sandbox", "production"], required=True)
    parser.add_argument("--verify-ssl", action="store_true", help="Enable SSL verification")
    parser.add_argument("--ignore-base-url", action="store_true", help="Ignore group base_url and use env default")
    parser.add_argument("--limit", type=int, default=0, help="Limit number of groups tested")
    parser.add_argument("--ids", nargs="*", default=[], help="Specific group IDs to test")
    args = parser.parse_args()

    data = json.loads(Path(args.input).read_text())
    groups = data.get("groups", [])
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
        if group.get("surface") != "REST_API_V3":
            continue
        results["results"].append(test_group(group, args.env, args.verify_ssl, args.ignore_base_url))

    Path(args.output).write_text(json.dumps(results, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
