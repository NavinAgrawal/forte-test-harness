#!/usr/bin/env python3
"""Generate SoapUI local.properties from config.local.php (gitignored)."""
from __future__ import annotations

import json
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CONFIG_LOCAL = ROOT / "api-demo-php-harness" / "config" / "config.local.php"
PROPERTIES_PATH = ROOT / "soap-projects" / "local.properties"


def load_config() -> dict:
    if not CONFIG_LOCAL.is_file():
        raise FileNotFoundError(f"Missing {CONFIG_LOCAL}")

    cmd = [
        "php",
        "-r",
        f"$cfg=require '{CONFIG_LOCAL.as_posix()}'; echo json_encode($cfg);",
    ]
    result = subprocess.run(cmd, capture_output=True, text=True, check=True)
    return json.loads(result.stdout or "{}")


def main() -> int:
    try:
        cfg = load_config()
    except FileNotFoundError as exc:
        print(f"[ERROR] {exc}")
        return 1
    except subprocess.CalledProcessError:
        print("[ERROR] PHP is required to read config.local.php")
        return 1

    pg_merchant_id = cfg.get("pg_merchant_id")
    pg_password = cfg.get("pg_password")

    lines = []
    if pg_merchant_id:
        lines.append(f"pg_merchant_id={pg_merchant_id}")
    if pg_password:
        lines.append(f"pg_password={pg_password}")

    if not lines:
        print("[WARN] No pg_merchant_id/pg_password found in config.local.php")
        return 1

    PROPERTIES_PATH.write_text("\n".join(lines) + "\n", encoding="utf-8")
    print(f"[OK] Wrote {PROPERTIES_PATH}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
