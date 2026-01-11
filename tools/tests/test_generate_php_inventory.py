import importlib.util
import os
import tempfile
import unittest
from pathlib import Path

MODULE_PATH = Path(__file__).resolve().parents[1] / "generate_php_inventory.py"

spec = importlib.util.spec_from_file_location("generate_php_inventory", MODULE_PATH)
inv = importlib.util.module_from_spec(spec)
spec.loader.exec_module(inv)


class GeneratePhpInventoryTests(unittest.TestCase):
    def setUp(self):
        self.tmpdir = tempfile.TemporaryDirectory()
        self.root = Path(self.tmpdir.name)
        (self.root / "api-demo-php-harness" / "internal-toolbox").mkdir(parents=True)
        (self.root / "docs").mkdir(parents=True)

        (self.root / "api-demo-php-harness" / "rest.php").write_text(
            "<?php\n$cfg = forte_config('api_access_id');\n$path = '/organizations/org_123';\n",
            encoding="utf-8",
        )
        (self.root / "api-demo-php-harness" / "internal-toolbox" / "agi.php").write_text(
            "postauth.pl pg_password",
            encoding="utf-8",
        )
        (self.root / "api-demo-php-harness" / "soap.php").write_text(
            "SoapClient /customers/cst_123",
            encoding="utf-8",
        )
        (self.root / "api-demo-php-harness" / "plain.php").write_text(
            "plain",
            encoding="utf-8",
        )
        (self.root / "api-demo-php-harness" / "v3.php").write_text(
            "/v3/transactions/trn_123",
            encoding="utf-8",
        )

        self.no_read = self.root / "api-demo-php-harness" / "no_read.php"
        self.no_read.write_text("no read", encoding="utf-8")
        os.chmod(self.no_read, 0)
        self.addCleanup(self._restore_permissions)
        self.addCleanup(self.tmpdir.cleanup)

    def _restore_permissions(self):
        try:
            os.chmod(self.no_read, 0o644)
        except FileNotFoundError:
            return

    def test_generates_inventory_html(self):
        inv.ROOT = self.root
        inv.PHP_ROOT = self.root / "api-demo-php-harness"
        inv.DOCS_DIR = self.root / "docs"
        inv.OUTPUT_PATH = inv.DOCS_DIR / "php-inventory.html"

        inv.main()

        self.assertTrue(inv.OUTPUT_PATH.is_file())
        html = inv.OUTPUT_PATH.read_text(encoding="utf-8")

        self.assertIn("PHP Script Inventory", html)
        self.assertIn("Internal Toolbox", html)
        self.assertIn("AGI / Payments Gateway", html)
        self.assertIn("SOAP", html)
        self.assertIn("REST API v3", html)
        self.assertIn("Uncategorized", html)
        self.assertIn("api-demo-php-harness/rest.php", html)
        self.assertIn("api-demo-php-harness/plain.php", html)
