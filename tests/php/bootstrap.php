<?php

$override_path = getenv('FORTE_TEST_CONFIG_PATH');
if ($override_path) {
    putenv('FORTE_CONFIG_PATH=' . $override_path);
} elseif (!getenv('FORTE_CONFIG_PATH')) {
    putenv('FORTE_CONFIG_PATH=' . __DIR__ . '/fixtures/config.test.php');
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../api-demo-php-harness/config/bootstrap.php';
require_once __DIR__ . '/support/RestClient.php';
require_once __DIR__ . '/support/FormClient.php';
require_once __DIR__ . '/support/SoapClient.php';
require_once __DIR__ . '/support/IntegrationTestCase.php';
