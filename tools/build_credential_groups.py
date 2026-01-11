#!/usr/bin/env python3
"""Build categorized credential groups for REST/SWP/SOAP/AGI surfaces.

Consumes grouped review JSON and optional test results, then writes a compact
credential-groups.local.json for local use (gitignored).
Secrets remain local; no output is printed to stdout.
"""
from __future__ import annotations

import argparse
import json
import re
from collections import defaultdict
from pathlib import Path
from typing import Dict, Iterable

ROOT = Path(__file__).resolve().parents[1]


def is_real_value(value: str | None) -> bool:
    if not value:
        return False
    if any(token in value for token in ("<?php", "?>", "YOUR_", "?")):
        return False
    if set(value) == {"*"}:
        return False
    return True


def extract_values_from_file(path: str) -> dict:
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
    for pattern in (
        r"pg_merchant_id\\s*=\\s*'([^']+)'",
        r"name=['\\\"]pg_merchant_id['\\\"]\\s+value=['\\\"]([^'\\\"]+)['\\\"]",
        r"<[^>]*pg_merchant_id[^>]*>([^<]+)</",
        r"<v1:MerchantID>([^<]+)</v1:MerchantID>",
        r"<v1:MerchantIDs>([^<]+)</v1:MerchantIDs>",
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


def load_json(path: Path) -> dict:
    if not path.is_file():
        return {}
    return json.loads(path.read_text())


def normalize_id(value: str, prefix: str) -> str:
    if not value:
        return "unknown"
    value = str(value)
    if value.startswith(prefix):
        value = value[len(prefix):]
    return value if value else "unknown"


def slugify(text: str) -> str:
    text = text.lower()
    text = re.sub(r"[^a-z0-9]+", "-", text)
    return text.strip("-") or "unknown"


def context_from_file(file_path: str) -> str:
    stem = Path(file_path).stem
    context = slugify(stem)
    for prefix in ("rest-", "swp-", "soap-", "agi-"):
        if context.startswith(prefix):
            return context[len(prefix):] or context
    return context


def status_from_test(result: dict) -> str:
    status = result.get("status")
    if status in ("working", "partial", "not_working"):
        return status
    statuses = result.get("statuses", [])
    if not statuses:
        return "partial"
    ok = sum(1 for s in statuses if s.get("status") == 200)
    unauthorized = sum(1 for s in statuses if s.get("status") == 401)
    if ok == len(statuses):
        return "working"
    if ok == 0 and unauthorized == len(statuses):
        return "not_working"
    return "partial"


def normalize_surface(value: str) -> str:
    value = (value or "").lower()
    if value in ("rest", "rest_api_v3"):
        return "rest"
    if "swp" in value:
        return "swp"
    if "soap" in value:
        return "soap"
    if "agi" in value:
        return "agi"
    return value


def dedupe_groups(groups: Iterable[dict], key_fields: Iterable[str]) -> list[dict]:
    seen = set()
    unique = []
    for g in groups:
        values = g.get("values", {}) or {}
        key = tuple(values.get(k) or "" for k in key_fields)
        if key in seen:
            continue
        seen.add(key)
        unique.append(g)
    return unique


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--review", required=True, help="Path to config.review.local.json")
    parser.add_argument("--sandbox", help="Path to sandbox tested JSON (REST)")
    parser.add_argument("--production", help="Path to production tested JSON (REST)")
    parser.add_argument("--sandbox-extra", help="Path to sandbox tested JSON for extra groups (REST)")
    parser.add_argument("--swp", help="Path to SWP tested JSON")
    parser.add_argument("--agi", help="Path to AGI tested JSON")
    parser.add_argument("--soap", help="Path to SOAP tested JSON")
    parser.add_argument("--default-env", choices=["sandbox", "production", "unknown"], default="sandbox")
    parser.add_argument("--soap-hash", default="/Users/nba/Library/CloudStorage/OneDrive-CSGSystemsInc/Tools/DevTools/htdocs/SOAP-hash.php")
    parser.add_argument("--output", required=True, help="Path to write credential-groups.local.json")
    args = parser.parse_args()

    review = load_json(Path(args.review))
    soap_hash_map = extract_hash_pairs(Path(args.soap_hash))

    test_sources = []
    for path in (args.sandbox, args.production, args.sandbox_extra, args.swp, args.agi, args.soap):
        if path:
            test_sources.append(load_json(Path(path)))

    test_by_id_env: dict[tuple[str, str, str], dict] = {}
    for source in test_sources:
        env = source.get("environment") or "unknown"
        for result in source.get("results", []):
            gid = result.get("id")
            if not gid:
                continue
            surface = normalize_surface(result.get("surface"))
            result_env = result.get("environment") or env
            test_by_id_env[(gid, result_env, surface)] = result

    surfaces = {
        "rest": {"working": {}, "partial": {}, "not_working": {}, "untested_count": 0},
        "swp": {"working": {}, "partial": {}, "not_working": {}, "untested_count": 0},
        "soap": {"working": {}, "partial": {}, "not_working": {}, "untested_count": 0},
        "agi": {"working": {}, "partial": {}, "not_working": {}, "untested_count": 0},
    }

    # REST: use tested results where available
    rest_groups = [g for g in review.get("groups", []) if g.get("surface") == "REST_API_V3"]

    tested_ids = set()
    for (gid, env, surface_key), result in test_by_id_env.items():
        if surface_key != "rest":
            continue
        tested_ids.add(gid)
        source = next((g for g in rest_groups if g.get("id") == gid), None)
        if not source:
            continue
        values = source.get("values", {}) or {}
        org_id = normalize_id(values.get("organization_id", ""), "org_")
        loc_id = normalize_id(values.get("location_id", ""), "loc_")
        context = context_from_file(source.get("file", ""))
        name = f"{env}-{org_id}-{loc_id}-rest-{context}"
        status = status_from_test(result)
        entry = {
            "id": gid,
            "environment": env,
            "organization_id": values.get("organization_id"),
            "location_id": values.get("location_id"),
            "api_access_id": values.get("api_access_id"),
            "api_secure_key": values.get("api_secure_key"),
            "base_url": values.get("base_url"),
            "source": source.get("file"),
            "status": status,
        }
        surfaces["rest"][status][name] = entry

    untested_rest = [g for g in rest_groups if g.get("id") not in tested_ids]
    surfaces["rest"]["untested_count"] = len(untested_rest)

    # SWP / SOAP / AGI: dedupe and mark as partial (untested)
    swp_groups = [g for g in review.get("groups", []) if g.get("surface") == "PAYMENTS_GATEWAY_SWP"]
    soap_groups = [g for g in review.get("groups", []) if g.get("surface") == "SOAP"]
    agi_groups = [g for g in review.get("groups", []) if g.get("surface") == "PAYMENTS_GATEWAY_AGI"]

    # Reclassify any SWP groups that are actually AGI flows based on filename.
    reclassified = []
    for group in list(swp_groups):
        file_path = (group.get("file", "") or "").lower()
        if "agi" in file_path:
            reclassified.append(group)
            swp_groups.remove(group)
    if reclassified:
        agi_groups.extend(reclassified)

    def add_groups(surface_key: str, groups: list[dict], required_keys: list[str]) -> None:
        unique = dedupe_groups(groups, required_keys)
        tested = 0
        for g in unique:
            values = g.get("values", {}) or {}
            extracted = extract_values_from_file(g.get("file", ""))
            for key in ("api_login_id", "secure_transaction_key", "pg_merchant_id", "pg_password"):
                if not is_real_value(values.get(key)):
                    if extracted.get(key):
                        values[key] = extracted.get(key)
            if surface_key == "soap" and is_real_value(values.get("api_login_id")):
                if not is_real_value(values.get("secure_transaction_key")):
                    mapped = soap_hash_map.get(values.get("api_login_id"), {})
                    if mapped.get("secure_transaction_key"):
                        values["secure_transaction_key"] = mapped.get("secure_transaction_key")
                    if not is_real_value(values.get("pg_merchant_id")) and mapped.get("merchant_id"):
                        values["pg_merchant_id"] = mapped.get("merchant_id")
            file_path = g.get("file", "").lower()
            if "sandbox" in file_path:
                env = "sandbox"
            elif "production" in file_path:
                env = "production"
            else:
                env = args.default_env
            org_id = normalize_id(values.get("organization_id", ""), "org_")
            loc_id = normalize_id(values.get("location_id", ""), "loc_")
            if loc_id == "unknown":
                loc_id = normalize_id(values.get("pg_merchant_id", ""), "loc_")
            context = context_from_file(g.get("file", ""))
            name = f"{env}-{org_id}-{loc_id}-{surface_key}-{context}"
            status = "partial"
            result = test_by_id_env.get((g.get("id"), env, surface_key))
            if result:
                status = status_from_test(result)
                tested += 1
            entry = {
                "id": g.get("id"),
                "environment": env,
                "organization_id": values.get("organization_id"),
                "location_id": values.get("location_id"),
                "api_login_id": values.get("api_login_id"),
                "secure_transaction_key": values.get("secure_transaction_key"),
                "pg_merchant_id": values.get("pg_merchant_id"),
                "pg_password": values.get("pg_password"),
                "source": g.get("file"),
                "status": status,
                "note": "untested" if not result else None,
            }
            surfaces[surface_key][status][name] = entry
        surfaces[surface_key]["untested_count"] = len(unique) - tested

    add_groups("swp", swp_groups, ["api_login_id", "secure_transaction_key"])
    add_groups("soap", soap_groups, ["api_login_id", "pg_merchant_id", "pg_password"])
    add_groups("agi", agi_groups, ["pg_merchant_id", "pg_password"])

    output = {
        "generated_at": "local",
        "surfaces": surfaces,
    }

    Path(args.output).write_text(json.dumps(output, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
