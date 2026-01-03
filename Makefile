FORTE_ENV ?= production
FORTE_CONFIG_PATH ?= api-demo-php-harness/config/config.local.php
PYTHON ?= python3
COVERAGE ?= coverage
PHPUNIT ?= vendor/bin/phpunit

export FORTE_ENV
export FORTE_CONFIG_PATH

.PHONY: help setup config sanitize sanitize-check test test-php test-python run hooks

help:
	@echo "Targets:"
	@echo "  setup       Install PHP dependencies (composer)"
	@echo "  config      Create local config from example if missing"
	@echo "  sanitize    Redact credentials/placeholders in demo artifacts"
	@echo "  sanitize-check Validate no redactions are needed"
	@echo "  test        Run all tests (PHP + Python)"
	@echo "  test-php    Run PHPUnit with coverage"
	@echo "  test-python Run Python unit tests with coverage"
	@echo "  run         Start PHP built-in server"
	@echo "  hooks       Enable repo git hooks (.githooks)"

setup:
	@composer install

config:
	@test -f "$(FORTE_CONFIG_PATH)" || cp api-demo-php-harness/config/config.example.php "$(FORTE_CONFIG_PATH)"

sanitize:
	$(PYTHON) tools/sanitize_placeholders.py api-demo-php-harness soap-projects

sanitize-check:
	$(PYTHON) tools/sanitize_placeholders.py --check api-demo-php-harness soap-projects

test: test-php test-python

test-php:
	XDEBUG_MODE=coverage $(PHPUNIT) --coverage-text

test-python:
	$(COVERAGE) run -m unittest discover -s tools/tests
	$(COVERAGE) report -m

run:
	php -S localhost:8080 -t api-demo-php-harness

hooks:
	@git config core.hooksPath .githooks
