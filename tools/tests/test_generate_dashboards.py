import importlib.util
import json
import os
import tempfile
import unittest
from pathlib import Path

MODULE_PATH = Path(__file__).resolve().parents[1] / "generate_dashboards.py"

spec = importlib.util.spec_from_file_location("generate_dashboards", MODULE_PATH)
mod = importlib.util.module_from_spec(spec)
spec.loader.exec_module(mod)


class GenerateDashboardsTests(unittest.TestCase):
    def setUp(self):
        self.tmpdir = tempfile.TemporaryDirectory()
        self.root = Path(self.tmpdir.name)
        (self.root / "api-demo-php-harness").mkdir(parents=True)
        (self.root / "docs").mkdir(parents=True)
        (self.root / "tests/php/integration").mkdir(parents=True)

        self.collection_path = self.root / "collection.json"
        self.collection_path.write_text(
            json.dumps(
                {
                    "item": [
                        {
                            "name": "Customers",
                            "item": [
                                {
                                    "name": "List Customers",
                                    "request": {
                                        "method": "GET",
                                        "url": {"path": ["organizations", "{{org}}", "locations", "{{loc}}", "customers"]},
                                    },
                                },
                                {
                                    "name": "Create Customer",
                                    "request": {
                                        "method": "POST",
                                        "url": {"path": ["organizations", "{{org}}", "locations", "{{loc}}", "customers"]},
                                    },
                                },
                            ],
                        }
                    ]
                }
            ),
            encoding="utf-8",
        )

        self.cases_path = self.root / "tests/php/integration/rest_sandbox_cases.json"
        self.cases_path.write_text(
            json.dumps(
                {
                    "environment": "sandbox",
                    "rest": [
                        {
                            "group": "Customers",
                            "method": "GET",
                            "path": "/organizations/{org}/locations/{loc}/customers",
                        }
                    ],
                    "non_rest": [
                        {"category": "Forte Checkout (FCO)"},
                    ],
                }
            ),
            encoding="utf-8",
        )

        self.flow_cases_path = self.root / "tests/php/integration/rest_flow_cases.json"
        self.flow_cases_path.write_text(
            json.dumps({"rest": [], "non_rest": []}),
            encoding="utf-8",
        )

        php_with_config = (
            "<?php\nrequire_once __DIR__ . '/config/bootstrap.php';\n"
            "// FCO\n"
            "// /organizations/org_123/locations/loc_456/customers\n"
            "$path = '/organizations/' . $organization_id . '/locations/' . $location_id . '/customers';\n"
        )
        (self.root / "api-demo-php-harness" / "rest.php").write_text(php_with_config, encoding="utf-8")

        php_without_config = "<?php\n// /transactions/trn_123\n$path = '/transactions/trn_123';\n"
        (self.root / "api-demo-php-harness" / "plain.php").write_text(php_without_config, encoding="utf-8")
        (self.root / "api-demo-php-harness" / "fco_raw.php").write_text(
            "<?php\n// FCO\n", encoding="utf-8"
        )
        (self.root / "api-demo-php-harness" / "soap_config.php").write_text(
            "<?php\nrequire_once __DIR__ . '/config/bootstrap.php';\n// SOAP\n", encoding="utf-8"
        )
        (self.root / "api-demo-php-harness" / "routing_raw.php").write_text(
            "<?php\n// routing\n", encoding="utf-8"
        )

        self._orig = {
            "ROOT": mod.ROOT,
            "PHP_ROOT": mod.PHP_ROOT,
            "DOCS_DIR": mod.DOCS_DIR,
            "INTEGRATION_CASES_PATH": mod.INTEGRATION_CASES_PATH,
            "INTEGRATION_FLOW_CASES_PATH": mod.INTEGRATION_FLOW_CASES_PATH,
            "DEFAULT_COLLECTION_CANDIDATES": list(mod.DEFAULT_COLLECTION_CANDIDATES),
        }
        mod.ROOT = self.root
        mod.PHP_ROOT = self.root / "api-demo-php-harness"
        mod.DOCS_DIR = self.root / "docs"
        mod.INTEGRATION_CASES_PATH = self.cases_path
        mod.INTEGRATION_FLOW_CASES_PATH = self.flow_cases_path
        mod.DEFAULT_COLLECTION_CANDIDATES = []

    def tearDown(self):
        mod.ROOT = self._orig["ROOT"]
        mod.PHP_ROOT = self._orig["PHP_ROOT"]
        mod.DOCS_DIR = self._orig["DOCS_DIR"]
        mod.INTEGRATION_CASES_PATH = self._orig["INTEGRATION_CASES_PATH"]
        mod.INTEGRATION_FLOW_CASES_PATH = self._orig["INTEGRATION_FLOW_CASES_PATH"]
        mod.DEFAULT_COLLECTION_CANDIDATES = self._orig["DEFAULT_COLLECTION_CANDIDATES"]
        self.tmpdir.cleanup()

    def test_normalize_paths(self):
        self.assertEqual(mod.normalize_path("/v3/organizations/{{org}}/locations/{{loc}}/customers?x=1"),
                         "/organizations/VAR/locations/VAR/customers")
        self.assertEqual(mod.normalize_case_path("/v3/organizations/{org}/locations/{loc}/customers"),
                         "/organizations/org_VAR/locations/loc_VAR/customers")
        self.assertEqual(mod.normalize_php_path("/transactions/trn_123"), "/transactions/trn_VAR")

    def test_normalize_path_edge_cases(self):
        self.assertEqual(mod.normalize_path(""), "")
        self.assertEqual(mod.normalize_path("{{baseURI}}v3/customers"), "/customers")
        self.assertEqual(mod.normalize_path("organizations/loc_123/customers"), "/organizations/loc_123/customers")

    def test_normalize_case_path_edge_cases(self):
        self.assertEqual(mod.normalize_case_path(""), "")
        self.assertEqual(mod.normalize_case_path("{{baseURI}}v3/customers"), "/customers")
        self.assertEqual(mod.normalize_case_path("organizations/{org}/locations/{loc}"),
                         "/organizations/org_VAR/locations/loc_VAR")
        self.assertEqual(mod.normalize_case_path("/customers?x=1"), "/customers")

    def test_extract_php_paths(self):
        text = "$organization_id $location_id '/organizations/' . $organization_id . '/locations/' . $location_id"
        paths = mod.extract_php_paths(text)
        self.assertTrue(paths)

    def test_iter_items(self):
        items = [
            {
                "name": "Group",
                "item": [
                    {"name": "Req", "request": {"method": "GET", "url": {"path": ["a", "b"]}}}
                ],
            }
        ]
        rows = list(mod.iter_items(items))
        self.assertEqual(rows[0]["group"], "Group")
        self.assertEqual(rows[0]["path"], "/a/b")

    def test_iter_items_without_path(self):
        items = [
            {
                "name": "Solo",
                "request": {"method": "GET", "url": "https://example.test"},
            }
        ]
        rows = list(mod.iter_items(items))
        self.assertIsNone(rows[0]["path"])

    def test_build_coverage_with_integration_cases(self):
        collection = json.loads(self.collection_path.read_text(encoding="utf-8"))
        endpoints, summary, non_rest = mod.build_coverage(collection)
        self.assertEqual(len(endpoints), 2)
        self.assertTrue(summary)
        self.assertTrue(non_rest)

    def test_build_coverage_without_integration_cases(self):
        mod.INTEGRATION_CASES_PATH = self.root / "missing.json"
        collection = json.loads(self.collection_path.read_text(encoding="utf-8"))
        _, summary, _ = mod.build_coverage(collection)
        self.assertTrue(summary)

    def test_build_coverage_config_status_variants(self):
        mod.INTEGRATION_CASES_PATH = self.root / "missing.json"

        extra_file = (
            "<?php\n"
            "// /organizations/org_999/locations/loc_999/customers\n"
            "$path = '/organizations/org_999/locations/loc_999/customers';\n"
        )
        (self.root / "api-demo-php-harness" / "customer_raw.php").write_text(extra_file, encoding="utf-8")
        (self.root / "api-demo-php-harness" / "orgs.php").write_text(
            "<?php\nrequire_once __DIR__ . '/config/bootstrap.php';\n// /organizations/org_VAR\n$path = '/organizations/org_VAR';\n",
            encoding="utf-8",
        )

        collection = {
            "item": [
                {
                    "name": "Customers",
                    "item": [
                        {
                            "name": "List Customers",
                            "request": {"method": "GET", "url": {"path": ["organizations", "org_VAR", "locations", "loc_VAR", "customers"]}},
                        },
                        {
                            "name": "Create Customer",
                            "request": {"method": "POST", "url": {"path": ["organizations", "org_VAR", "locations", "loc_VAR", "customers"]}},
                        },
                    ],
                },
                {
                    "name": "Organizations",
                    "item": [
                        {
                            "name": "Get Org",
                            "request": {"method": "GET", "url": {"path": ["organizations", "org_VAR"]}},
                        }
                    ],
                },
                {
                    "name": "Transactions",
                    "item": [
                        {
                            "name": "Get Transaction",
                            "request": {"method": "GET", "url": {"path": ["transactions", "trn_VAR"]}},
                        }
                    ],
                },
                {
                    "name": "Applications",
                    "item": [
                        {
                            "name": "List Applications",
                            "request": {"method": "GET", "url": {"path": ["applications"]}},
                        }
                    ],
                },
            ]
        }

        _, summary, _ = mod.build_coverage(collection)
        by_group = {row["group"]: row for row in summary}
        self.assertEqual(by_group["Customers"]["config"], "Partial")
        self.assertEqual(by_group["Transactions"]["config"], "No")
        self.assertEqual(by_group["Applications"]["centralizable"], "Yes (new coverage)")
        self.assertEqual(by_group["Organizations"]["config"], "Yes")

        _, _, non_rest = mod.build_coverage(collection)
        by_category = {row["category"]: row for row in non_rest}
        self.assertEqual(by_category["Forte Checkout (FCO)"]["config"], "Partial")
        self.assertEqual(by_category["SOAP Helpers"]["config"], "Yes")
        self.assertEqual(by_category["Routing / Validation Utilities"]["config"], "No")

    def test_build_coverage_integration_set_partial(self):
        self.cases_path.write_text(
            json.dumps(
                {
                    "environment": "sandbox",
                    "rest": [
                        {
                            "group": "Customers",
                            "method": "GET",
                            "path": "/organizations/VAR/locations/VAR/customers",
                        }
                    ],
                    "non_rest": [],
                }
            ),
            encoding="utf-8",
        )
        collection = json.loads(self.collection_path.read_text(encoding="utf-8"))
        _, summary, _ = mod.build_coverage(collection)
        by_group = {row["group"]: row for row in summary}
        self.assertEqual(by_group["Customers"]["config"], "Partial")

    def test_render_dashboards(self):
        html = mod.render_coverage_dashboard(
            [
                {
                    "group": "Customers",
                    "total": 2,
                    "covered": 1,
                    "not_covered": 1,
                    "coverage_pct": 50,
                    "config": "Partial",
                    "centralizable": "Yes (needs refactor)",
                    "example_files": [],
                },
                {
                    "group": "Transactions",
                    "total": 2,
                    "covered": 2,
                    "not_covered": 0,
                    "coverage_pct": 90,
                    "config": "Yes",
                    "centralizable": "Already",
                    "example_files": ["rest.php"],
                },
            ],
            [
                {
                    "category": "Forte Checkout (FCO)",
                    "files_count": 1,
                    "config": "Yes",
                    "centralizable": "Already",
                    "example_files": ["rest.php"],
                }
            ],
        )
        self.assertIn("Forte Test Harness Coverage Dashboard", html)
        rest_cases = [{"group": "Addresses", "method": "GET", "path": "/addresses"} for _ in range(10)]
        rest_cases.append({"method": "GET", "path": "/missing"})
        test_html = mod.render_test_dashboard(
            {"environment": "sandbox", "rest": rest_cases, "non_rest": [{"category": "Forte Checkout (FCO)"}, {}]}
        )
        self.assertIn("Integration Test Dashboard", test_html)

    def test_is_config_driven(self):
        self.assertTrue(mod.is_config_driven("require config/bootstrap.php"))
        self.assertTrue(mod.is_config_driven("forte_base_url("))
        self.assertFalse(mod.is_config_driven("no config"))

    def test_load_integration_cases_variants(self):
        mod.INTEGRATION_CASES_PATH = self.root / "tests/php/integration/invalid.json"
        mod.INTEGRATION_CASES_PATH.write_text("{", encoding="utf-8")
        data = mod.load_integration_cases()
        self.assertEqual(data["rest"], [])

        mod.INTEGRATION_CASES_PATH.write_text(json.dumps(["not", "dict"]), encoding="utf-8")
        data = mod.load_integration_cases()
        self.assertEqual(data["rest"], [])

        mod.INTEGRATION_CASES_PATH.write_text(
            json.dumps({"environment": "sandbox", "rest": [], "non_rest": []}), encoding="utf-8"
        )
        mod.INTEGRATION_FLOW_CASES_PATH = self.root / "tests/php/integration/flow.json"
        mod.INTEGRATION_FLOW_CASES_PATH.write_text(
            json.dumps({"rest": [{"group": "Customers", "method": "GET", "path": "/customers"}], "non_rest": []}),
            encoding="utf-8",
        )
        data = mod.load_integration_cases()
        self.assertEqual(len(data["rest"]), 1)

        mod.INTEGRATION_FLOW_CASES_PATH.write_text("{", encoding="utf-8")
        data = mod.load_integration_cases()
        self.assertIsInstance(data, dict)

        mod.INTEGRATION_FLOW_CASES_PATH.write_text(json.dumps(["bad"]), encoding="utf-8")
        data = mod.load_integration_cases()
        self.assertIsInstance(data, dict)

    def test_load_integration_cases_missing_flow(self):
        mod.INTEGRATION_CASES_PATH = self.cases_path
        mod.INTEGRATION_FLOW_CASES_PATH = self.root / "tests/php/integration/missing_flow.json"
        data = mod.load_integration_cases()
        self.assertIn("rest", data)

    def test_load_collection_skips_empty_candidates(self):
        mod.DEFAULT_COLLECTION_CANDIDATES = [None, "", str(self.collection_path)]
        loaded = mod.load_collection()
        self.assertIn("item", loaded)

    def test_build_coverage_read_error_is_ignored(self):
        bad_path = self.root / "api-demo-php-harness" / "bad.php"
        bad_path.write_text("<?php // /customers\n", encoding="utf-8")

        original_read_text = Path.read_text
        raised = {"value": False}

        def patched_read_text(self, *args, **kwargs):
            if self == bad_path and not raised["value"]:
                raised["value"] = True
                raise OSError("boom")
            return original_read_text(self, *args, **kwargs)

        with unittest.mock.patch.object(Path, "read_text", patched_read_text):
            collection = json.loads(self.collection_path.read_text(encoding="utf-8"))
            mod.build_coverage(collection)

    def test_load_collection_env(self):
        os.environ["FORTE_POSTMAN_COLLECTION"] = str(self.collection_path)
        loaded = mod.load_collection()
        self.assertIn("item", loaded)
        os.environ.pop("FORTE_POSTMAN_COLLECTION", None)

    def test_load_collection_missing_raises(self):
        os.environ["FORTE_POSTMAN_COLLECTION"] = str(self.root / "missing.json")
        with self.assertRaises(FileNotFoundError):
            mod.load_collection()
        os.environ.pop("FORTE_POSTMAN_COLLECTION", None)

    def test_main_writes_dashboards(self):
        os.environ["FORTE_POSTMAN_COLLECTION"] = str(self.collection_path)
        mod.main()
        self.assertTrue((self.root / "docs/coverage-dashboard.html").is_file())
        self.assertTrue((self.root / "docs/test-dashboard.html").is_file())
        os.environ.pop("FORTE_POSTMAN_COLLECTION", None)
