FORTE_ENV ?= production
FORTE_CONFIG_PATH ?= api-demo-php-harness/config/config.local.php
PYTHON ?= python3
COVERAGE ?= coverage
PHPUNIT ?= vendor/bin/phpunit

export FORTE_ENV
export FORTE_CONFIG_PATH

.PHONY: help setup config sanitize sanitize-check dashboards soap-properties credential-groups rest-groups swp-groups agi-groups soap-groups test test-php test-integration test-python run hooks

help:
	@echo "Targets:"
	@echo "  setup       Install PHP dependencies (composer)"
	@echo "  config      Create local config from example if missing"
	@echo "  sanitize    Redact credentials/placeholders in demo artifacts"
	@echo "  sanitize-check Validate no redactions are needed"
	@echo "  dashboards  Regenerate HTML dashboards"
	@echo "  soap-properties Export SoapUI local.properties from config.local.php"
	@echo "  credential-groups Build grouped credential metadata (local-only)"
	@echo "  rest-groups Run REST credential checks (writes local results)"
	@echo "  swp-groups  Run SWP credential checks (writes local results)"
	@echo "  agi-groups  Run AGI credential checks (writes local results)"
	@echo "  soap-groups Run SOAP credential checks (writes local results)"
	@echo "  test        Run all tests (PHP + Python)"
	@echo "  test-php    Run PHPUnit with coverage"
	@echo "  test-integration Run sandbox integration tests (no mocks)"
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
	$(PYTHON) tools/sanitize_placeholders.py --check --tracked-only api-demo-php-harness soap-projects

dashboards:
	$(PYTHON) tools/generate_dashboards.py

soap-properties:
	$(PYTHON) tools/generate_soap_properties.py

credential-groups:
	$(PYTHON) tools/build_credential_groups.py \
		--review api-demo-php-harness/config/config.review.local.json \
		--sandbox api-demo-php-harness/config/config.review.sandbox.tested.json \
		--production api-demo-php-harness/config/config.review.production.tested.json \
		--sandbox-extra api-demo-php-harness/config/config.review.sandbox.300382-173185.tested.json \
		--swp api-demo-php-harness/config/config.review.swp.tested.json \
		--agi api-demo-php-harness/config/config.review.agi.tested.json \
		--soap api-demo-php-harness/config/config.review.soap.tested.json \
		--default-env $(FORTE_ENV) \
		--output api-demo-php-harness/config/credential-groups.local.json

rest-groups:
	$(PYTHON) tools/test_rest_groups.py \
		--input api-demo-php-harness/config/config.review.local.json \
		--output api-demo-php-harness/config/config.review.$(FORTE_ENV).tested.json \
		--env $(FORTE_ENV)

swp-groups:
	$(PYTHON) tools/test_swp_groups.py \
		--input api-demo-php-harness/config/config.review.local.json \
		--output api-demo-php-harness/config/config.review.swp.tested.json \
		--env $(FORTE_ENV)

agi-groups:
	$(PYTHON) tools/test_agi_groups.py \
		--input api-demo-php-harness/config/config.review.local.json \
		--output api-demo-php-harness/config/config.review.agi.tested.json \
		--env $(FORTE_ENV)

soap-groups:
	$(PYTHON) tools/test_soap_groups.py \
		--input api-demo-php-harness/config/config.review.local.json \
		--output api-demo-php-harness/config/config.review.soap.tested.json \
		--env $(FORTE_ENV)

test: test-php test-python

test-php:
	@bash -lc 'ulimit -n 1024 >/dev/null 2>&1 || true; XDEBUG_MODE=coverage $(PHPUNIT) --exclude-group integration --coverage-text'

test-integration:
	@bash -lc 'ulimit -n 1024 >/dev/null 2>&1 || true; if [ -f .env ]; then set -a; . .env; set +a; fi; FORTE_ENV=sandbox FORTE_TEST_CONFIG_PATH=$(FORTE_CONFIG_PATH) $(PHPUNIT) --group integration'

test-python:
	$(COVERAGE) run --source tools -m unittest discover -s tools/tests
	$(COVERAGE) report --include tools/sanitize_placeholders.py -m

run:
	php -S localhost:8080 -t api-demo-php-harness

hooks:
	@git config core.hooksPath .githooks
