#!/usr/bin/env python3
"""Generate an HTML inventory of PHP scripts and surfaces."""
from __future__ import annotations

import html
import re
from collections import Counter
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
PHP_ROOT = ROOT / "api-demo-php-harness"
DOCS_DIR = ROOT / "docs"
OUTPUT_PATH = DOCS_DIR / "php-inventory.html"

RESOURCE_PATTERN = re.compile(
    r"/(organizations|locations|customers|transactions|paymethods|schedules|scheduleitems|documents|fundings|disputes|settlements|addresses|applications|vendors)(?:/[^\s<>;]+)*",
    re.IGNORECASE,
)

CATEGORY_PATTERNS = [
    ("REST API v3", [r"/v3/", r"api\.forte\.net/v3", r"sandbox\.forte\.net/api/v3", r"forte_base_url", r"Authorization:\s*Basic"]),
    ("Forte Checkout (FCO)", [r"forte-checkout", r"\bFCO\b", r"checkout\.js", r"fortecheckout"]),
    ("Forte.js / Tokenization", [r"forte\.js", r"forte-api-login-id", r"tokenization"]),
    ("SWP (Simple Web Payments)", [r"paymentsgateway\.net/swp", r"\bSWP\b", r"simple web payments"]),
    ("AGI / Payments Gateway", [r"postauth\.pl", r"posttest\.pl", r"pg_merchant_id", r"pg_password", r"paymentsgateway\.net/cgi-bin"]),
    ("SOAP", [r"SoapClient", r"\.wsdl", r"soap\."] ),
    ("Webhooks / Notifications", [r"webhook", r"send_notification"]),
    ("Freshdesk", [r"freshdesk"]),
    ("HTML2PDF Rocket", [r"html2pdf", r"html2pdfrocket"]),
    ("Importer/Exporter", [r"importer", r"data_exporter"]),
    ("Routing / Validation", [r"routing"]),
]

CONFIG_DRIVEN_PATTERN = re.compile(r"forte_(config|base_url|js_url|pg_action_url|swp_base_url)\s*\(")


def load_php_files() -> list[Path]:
    return sorted(PHP_ROOT.rglob("*.php"))


def extract_resources(content: str) -> list[str]:
    resources = set()
    for match in RESOURCE_PATTERN.finditer(content):
        resources.add(match.group(1).capitalize())
    return sorted(resources)


def detect_categories(content: str, rel_path: str) -> list[str]:
    categories: list[str] = []
    lower_path = rel_path.lower()

    if "internal-toolbox" in lower_path:
        categories.append("Internal Toolbox")

    for name, patterns in CATEGORY_PATTERNS:
        for pattern in patterns:
            if re.search(pattern, content, re.IGNORECASE) or re.search(pattern, rel_path, re.IGNORECASE):
                categories.append(name)
                break

    return sorted(set(categories))


def escape_cell(value: str) -> str:
    return html.escape(value, quote=True)


def main() -> None:
    files = load_php_files()
    rows = []
    category_counter = Counter()
    config_driven_count = 0

    for path in files:
        rel = path.relative_to(ROOT)
        try:
            content = path.read_text(encoding="utf-8", errors="ignore")
        except Exception:
            content = ""

        resources = extract_resources(content)
        categories = detect_categories(content, str(rel))
        if resources and "REST API v3" not in categories:
            categories.append("REST API v3")
            categories = sorted(set(categories))

        config_driven = bool(CONFIG_DRIVEN_PATTERN.search(content))
        if config_driven:
            config_driven_count += 1

        centralizable = "Already" if config_driven else "Needs refactor"
        if not categories:
            categories = ["Uncategorized"]

        for cat in categories:
            category_counter[cat] += 1

        rows.append(
            {
                "file": str(rel),
                "categories": ", ".join(categories),
                "resources": ", ".join(resources) if resources else "—",
                "config": "Yes" if config_driven else "No",
                "centralizable": centralizable,
            }
        )

    rows.sort(key=lambda row: (row["categories"], row["file"]))

    total_files = len(files)
    non_config = total_files - config_driven_count
    top_categories = category_counter.most_common(8)

    category_rows = "".join(
        f"<tr><td><strong>{escape_cell(name)}</strong></td><td>{count}</td></tr>" for name, count in top_categories
    )

    table_rows = "".join(
        "<tr>"
        f"<td><code>{escape_cell(row['file'])}</code></td>"
        f"<td>{escape_cell(row['categories'])}</td>"
        f"<td>{escape_cell(row['resources'])}</td>"
        f"<td><span class='badge {'good' if row['config']=='Yes' else 'bad'}'>{row['config']}</span></td>"
        f"<td><span class='badge {'good' if row['centralizable']=='Already' else 'warn'}'>{row['centralizable']}</span></td>"
        "</tr>"
        for row in rows
    )

    html_output = f"""<!doctype html>
<html lang=\"en\">
<head>
<meta charset=\"utf-8\"/>
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"/>
<title>Forte Test Harness PHP Inventory</title>
<style>
:root {{
  --bg: #0f172a;
  --panel: #111827;
  --muted: #94a3b8;
  --text: #e5e7eb;
  --accent: #22c55e;
  --warn: #f59e0b;
  --bad: #ef4444;
  --good: #22c55e;
  --blue: #38bdf8;
  --border: #1f2937;
}}
* {{ box-sizing: border-box; }}
body {{
  margin: 0;
  font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Arial, sans-serif;
  background: radial-gradient(1200px 600px at 20% -10%, #1f2937 0%, #0f172a 55%);
  color: var(--text);
}}
header {{ padding: 28px 32px 8px; }}
header h1 {{ margin: 0 0 6px; font-size: 28px; letter-spacing: 0.2px; }}
header p {{ margin: 0; color: var(--muted); }}
main {{ padding: 16px 32px 40px; display: grid; gap: 18px; }}
.grid {{ display: grid; gap: 14px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }}
.card {{
  background: linear-gradient(180deg, #111827 0%, #0b1220 100%);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 16px;
}}
.card h3 {{ margin: 0 0 6px; font-size: 14px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.8px; }}
.card .value {{ font-size: 28px; font-weight: 700; }}
.card .sub {{ color: var(--muted); font-size: 12px; }}
.table-wrap {{ overflow: auto; border-radius: 12px; border: 1px solid var(--border); }}
.table {{ width: 100%; border-collapse: collapse; min-width: 980px; }}
.table th, .table td {{ padding: 10px 12px; border-bottom: 1px solid var(--border); text-align: left; }}
.table th {{ position: sticky; top: 0; background: #0b1220; font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.6px; }}
.badge {{ padding: 4px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; display: inline-block; }}
.badge.good {{ background: rgba(34,197,94,0.15); color: var(--good); }}
.badge.warn {{ background: rgba(245,158,11,0.15); color: var(--warn); }}
.badge.bad {{ background: rgba(239,68,68,0.15); color: var(--bad); }}
.list {{ margin: 0; padding-left: 18px; }}
.list li {{ margin: 4px 0; color: var(--muted); }}
.section-title {{ font-size: 16px; margin: 0 0 8px; text-transform: uppercase; letter-spacing: 0.8px; color: var(--muted); }}
@media (max-width: 900px) {{
  main {{ padding: 16px; }}
  header {{ padding: 20px 16px 8px; }}
}}
</style>
</head>
<body>
<header>
  <h1>Forte Test Harness PHP Inventory</h1>
  <p>PHP script surfaces, REST resource references, and config-driven status.</p>
</header>
<main>
  <div class=\"grid\">
    <div class=\"card\">
      <h3>Total PHP Files</h3>
      <div class=\"value\">{total_files}</div>
      <div class=\"sub\">api-demo-php-harness/**/*.php</div>
    </div>
    <div class=\"card\">
      <h3>Config-driven</h3>
      <div class=\"value\">{config_driven_count}</div>
      <div class=\"sub\">Uses forte_config/forte_base_url</div>
    </div>
    <div class=\"card\">
      <h3>Needs Refactor</h3>
      <div class=\"value\">{non_config}</div>
      <div class=\"sub\">Missing config helpers</div>
    </div>
  </div>

  <section class=\"card\">
    <h2 class=\"section-title\">Top Categories</h2>
    <div class=\"table-wrap\">
      <table class=\"table\">
        <thead>
          <tr>
            <th>Category</th>
            <th>Files</th>
          </tr>
        </thead>
        <tbody>
          {category_rows}
        </tbody>
      </table>
    </div>
  </section>

  <section class=\"card\">
    <h2 class=\"section-title\">PHP Script Inventory</h2>
    <div class=\"table-wrap\">
      <table class=\"table\">
        <thead>
          <tr>
            <th>File</th>
            <th>Surface(s)</th>
            <th>REST Resources</th>
            <th>Config-driven</th>
            <th>Centralizable</th>
          </tr>
        </thead>
        <tbody>
          {table_rows}
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>
"""

    OUTPUT_PATH.write_text(html_output, encoding="utf-8")


if __name__ == "__main__":
    main()
