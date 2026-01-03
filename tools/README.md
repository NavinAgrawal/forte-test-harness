# Tools

- `sanitize_placeholders.py`: Redacts credentials/IDs in demo artifacts before commit.
  - Usage: `python3 tools/sanitize_placeholders.py api-demo-php-harness soap-projects`
- `secret_*_paths*.txt`: Local scan outputs for candidate secret locations (generated). These are ignored by git.

Keep this folder for maintenance utilities and local scan artifacts.
