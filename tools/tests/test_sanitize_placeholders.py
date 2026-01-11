import importlib.util
import os
import runpy
import sys
import tempfile
import unittest
from contextlib import redirect_stdout
from io import StringIO
from pathlib import Path
from unittest import mock

MODULE_PATH = Path(__file__).resolve().parents[1] / "sanitize_placeholders.py"

spec = importlib.util.spec_from_file_location("sanitize_placeholders", MODULE_PATH)
sp = importlib.util.module_from_spec(spec)
spec.loader.exec_module(sp)


class FakeBytes:
    def __contains__(self, item):
        return False

    def decode(self, encoding):
        raise UnicodeDecodeError(encoding, b"", 0, 1, "boom")


class SanitizePlaceholdersTests(unittest.TestCase):
    def test_should_process(self):
        self.assertTrue(sp.should_process("file.php"))
        self.assertFalse(sp.should_process("file.bin"))

    def test_sanitize_text_replaces_known_fields(self):
        text = "api_access_id='abc' api_secure_key=\"def\" org_123 loc_456"
        cleaned = sp.sanitize_text(text)
        self.assertIn("YOUR_API_ACCESS_ID", cleaned)
        self.assertIn("YOUR_API_SECURE_KEY", cleaned)
        self.assertIn("org_xxxxx", cleaned)
        self.assertIn("loc_xxxxx", cleaned)

    def test_sanitize_text_skips_php_interpolation(self):
        text = "api_access_id=\"<?php echo $api_access_id; ?>\""
        cleaned = sp.sanitize_text(text)
        self.assertIn("<?php echo $api_access_id; ?>", cleaned)

    def test_process_file_skips_binary(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            path = Path(tmpdir) / "binary.php"
            path.write_bytes(b"\x00\x01\x02")
            self.assertFalse(sp.process_file(str(path)))

    def test_process_file_skips_non_text_extension(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            path = Path(tmpdir) / "image.jpg"
            path.write_text("api_access_id='abc';", encoding="utf-8")
            self.assertFalse(sp.process_file(str(path)))

    def test_process_file_missing_path(self):
        self.assertFalse(sp.process_file("/nonexistent/path.php"))

    def test_process_file_rewrites_text(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            path = Path(tmpdir) / "sample.php"
            path.write_text("$api_access_id='abc';", encoding="utf-8")
            changed = sp.process_file(str(path))
            self.assertTrue(changed)
            contents = path.read_text(encoding="utf-8")
            self.assertIn("YOUR_API_ACCESS_ID", contents)

    def test_process_file_no_changes(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            path = Path(tmpdir) / "sample.php"
            path.write_text("no secrets here", encoding="utf-8")
            self.assertFalse(sp.process_file(str(path)))

    def test_process_file_check_does_not_write(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            path = Path(tmpdir) / "sample.php"
            path.write_text("$api_access_id='abc';", encoding="utf-8")
            changed = sp.process_file(str(path), check=True)
            self.assertTrue(changed)
            contents = path.read_text(encoding="utf-8")
            self.assertIn("api_access_id", contents)

    def test_process_file_latin1_fallback(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            path = Path(tmpdir) / "latin.php"
            content = "api_access_id='abc';ñ"
            path.write_bytes(content.encode("latin-1"))
            self.assertTrue(sp.process_file(str(path)))

    def test_process_file_decode_error_returns_false(self):
        fake_bytes = FakeBytes()
        fake_file = mock.MagicMock()
        fake_file.__enter__.return_value = fake_file
        fake_file.read.return_value = fake_bytes

        with mock.patch.object(sp, "open", return_value=fake_file):
            self.assertFalse(sp.process_file("fake.php"))

    def test_walk_and_sanitize_counts_changes(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            root = Path(tmpdir)
            (root / "a.php").write_text("$api_access_id='abc';", encoding="utf-8")
            (root / "b.php").write_text("no secrets", encoding="utf-8")
            count = sp.walk_and_sanitize(str(root))
            self.assertEqual(count, 1)

    def test_walk_and_sanitize_check_records_paths(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            root = Path(tmpdir)
            (root / "a.php").write_text("$api_access_id='abc';", encoding="utf-8")
            changed_paths = []
            count = sp.walk_and_sanitize(str(root), check=True, changed_paths=changed_paths)
            self.assertEqual(count, 1)
            self.assertEqual(len(changed_paths), 1)

    def test_walk_and_sanitize_respects_allowed_paths(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            root = Path(tmpdir)
            allowed = root / "a.php"
            (allowed).write_text("$api_access_id='abc';", encoding="utf-8")
            (root / "b.php").write_text("$api_access_id='abc';", encoding="utf-8")
            count = sp.walk_and_sanitize(str(root), allowed_paths={str(allowed)})
            self.assertEqual(count, 1)

    def test_git_tracked_files_returns_paths(self):
        tracked = sp.git_tracked_files()
        self.assertIsInstance(tracked, set)
        self.assertIn(str(MODULE_PATH), tracked)

    def test_git_tracked_files_handles_errors(self):
        with mock.patch.object(sp.subprocess, "check_output", side_effect=Exception("boom")):
            self.assertEqual(sp.git_tracked_files(), set())

    def test_main_outputs_summary(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            root = Path(tmpdir)
            (root / "a.php").write_text("$api_access_id='abc';", encoding="utf-8")

            stdout = StringIO()
            old_argv = sys.argv
            try:
                sys.argv = ["sanitize_placeholders.py", str(root), "not_a_dir"]
                with redirect_stdout(stdout):
                    sp.main()
            finally:
                sys.argv = old_argv

            output = stdout.getvalue()
            self.assertIn("[INFO]", output)
            self.assertIn("[WARN] Not a directory", output)
            self.assertIn("[DONE]", output)

    def test_main_tracked_only_runs(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            root = Path(tmpdir)
            (root / "a.php").write_text("$api_access_id='abc';", encoding="utf-8")

            stdout = StringIO()
            old_argv = sys.argv
            try:
                sys.argv = ["sanitize_placeholders.py", "--tracked-only", str(root)]
                with redirect_stdout(stdout):
                    sp.main()
            finally:
                sys.argv = old_argv

            output = stdout.getvalue()
            self.assertIn("[DONE]", output)

    def test_main_check_exits_nonzero(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            root = Path(tmpdir)
            (root / "a.php").write_text("$api_access_id='abc';", encoding="utf-8")

            stdout = StringIO()
            old_argv = sys.argv
            try:
                sys.argv = ["sanitize_placeholders.py", "--check", str(root)]
                with redirect_stdout(stdout):
                    with self.assertRaises(SystemExit) as ctx:
                        sp.main()
            finally:
                sys.argv = old_argv

            self.assertEqual(ctx.exception.code, 1)
            output = stdout.getvalue()
            self.assertIn("[CHECK] would update", output)

    def test_main_entrypoint_executes(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            root = Path(tmpdir)
            (root / "a.php").write_text("$api_access_id='abc';", encoding="utf-8")

            stdout = StringIO()
            old_argv = sys.argv
            try:
                sys.argv = ["sanitize_placeholders.py", str(root)]
                with redirect_stdout(stdout):
                    runpy.run_path(str(MODULE_PATH), run_name="__main__")
            finally:
                sys.argv = old_argv

            output = stdout.getvalue()
            self.assertIn("[DONE]", output)


if __name__ == "__main__":
    unittest.main()
