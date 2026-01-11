<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\FormClient;
use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 * @group non-rest
 * @group agi
 */
class AgiSandboxFlowTest extends IntegrationTestCase
{
    private FormClient $formClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formClient = new FormClient($this->sslVerifyEnabled());
    }

    public function testSandboxEnvironment(): void
    {
        $this->assertSame('sandbox', forte_env_name(), 'FORTE_ENV must be sandbox for AGI integration tests.');
    }

    public function testCardSaleAndVoid(): void
    {
        $merchantId = (string)forte_config('pg_merchant_id');
        $password = (string)forte_config('pg_password');
        if ($this->isPlaceholder($merchantId) || $this->isPlaceholder($password)) {
            $this->markTestSkipped('AGI credentials not configured (pg_merchant_id/pg_password).');
        }
        $this->assertConfigValue('pg_merchant_id', $merchantId);
        $this->assertConfigValue('pg_password', $password);

        $payload = [
            'pg_merchant_id' => $merchantId,
            'pg_password' => $password,
            'pg_transaction_type' => '10',
            'pg_total_amount' => '0.01',
            'ecom_billto_postal_name_first' => 'Forte',
            'ecom_billto_postal_name_last' => 'Harness',
            'ecom_billto_postal_postalcode' => '75013',
            'ecom_payment_card_type' => $this->agiCardType(),
            'ecom_payment_card_name' => $this->optionalEnvValue('FORTE_TEST_CARD_NAME', 'Forte Test'),
            'ecom_payment_card_number' => $this->requireEnvValue('FORTE_TEST_CARD_NUMBER'),
            'ecom_payment_card_expdate_month' => $this->requireEnvValue('FORTE_TEST_CARD_EXP_MONTH'),
            'ecom_payment_card_expdate_year' => $this->requireEnvValue('FORTE_TEST_CARD_EXP_YEAR'),
            'ecom_payment_card_verification' => $this->requireEnvValue('FORTE_TEST_CARD_CVV'),
        ];

        $sale = $this->postGateway($payload);
        $saleFields = $this->parseGatewayFields($sale['body']);
        $this->assertGatewayApproved($sale, $saleFields, 'AGI card sale');

        $trace = $saleFields['pg_trace_number'] ?? '';
        $auth = $saleFields['pg_authorization_code'] ?? '';
        $this->assertNotSame('', $trace, 'Missing pg_trace_number from AGI sale response.');
        $this->assertNotSame('', $auth, 'Missing pg_authorization_code from AGI sale response.');

        $voidPayload = [
            'pg_merchant_id' => $merchantId,
            'pg_password' => $password,
            'pg_transaction_type' => '14',
            'pg_original_trace_number' => $trace,
            'pg_original_authorization_code' => $auth,
            'pg_total_amount' => '0.01',
        ];

        $void = $this->postGateway($voidPayload);
        $voidFields = $this->parseGatewayFields($void['body']);
        $this->assertGatewayApproved($void, $voidFields, 'AGI card void');
    }

    public function testAchSaleAndVoid(): void
    {
        $merchantId = (string)forte_config('pg_merchant_id');
        $password = (string)forte_config('pg_password');
        if ($this->isPlaceholder($merchantId) || $this->isPlaceholder($password)) {
            $this->markTestSkipped('AGI credentials not configured (pg_merchant_id/pg_password).');
        }
        $this->assertConfigValue('pg_merchant_id', $merchantId);
        $this->assertConfigValue('pg_password', $password);

        $routing = $this->requireEnvValue('FORTE_TEST_ACH_ROUTING');
        $account = $this->requireEnvValue('FORTE_TEST_ACH_ACCOUNT');
        $accountType = strtoupper($this->requireEnvValue('FORTE_TEST_ACH_ACCOUNT_TYPE'));
        if (!in_array($accountType, ['C', 'S'], true)) {
            $this->fail('FORTE_TEST_ACH_ACCOUNT_TYPE must be C or S.');
        }

        $payload = [
            'pg_merchant_id' => $merchantId,
            'pg_password' => $password,
            'pg_transaction_type' => '20',
            'pg_total_amount' => '0.02',
            'ecom_billto_postal_name_first' => 'Forte',
            'ecom_billto_postal_name_last' => 'Harness',
            'ecom_billto_postal_postalcode' => '75013',
            'ecom_payment_check_trn' => $routing,
            'ecom_payment_check_account' => $account,
            'ecom_payment_check_account_type' => $accountType,
        ];

        $entryClass = $this->optionalEnvValue('FORTE_TEST_ACH_ENTRY_CLASS', '');
        if ($entryClass !== '') {
            $payload['pg_entry_class_code'] = $entryClass;
        }

        $sale = $this->postGateway($payload);
        $saleFields = $this->parseGatewayFields($sale['body']);
        $this->assertGatewayApproved($sale, $saleFields, 'AGI ACH sale');

        $trace = $saleFields['pg_trace_number'] ?? '';
        $auth = $saleFields['pg_authorization_code'] ?? '';
        $this->assertNotSame('', $trace, 'Missing pg_trace_number from AGI ACH sale response.');
        $this->assertNotSame('', $auth, 'Missing pg_authorization_code from AGI ACH sale response.');

        $voidPayload = [
            'pg_merchant_id' => $merchantId,
            'pg_password' => $password,
            'pg_transaction_type' => '24',
            'pg_original_trace_number' => $trace,
            'pg_original_authorization_code' => $auth,
            'pg_total_amount' => '0.02',
        ];

        $void = $this->postGateway($voidPayload);
        $voidFields = $this->parseGatewayFields($void['body']);
        $this->assertGatewayApproved($void, $voidFields, 'AGI ACH void');
    }

    private function postGateway(array $payload): array
    {
        $url = forte_pg_action_url();
        return $this->formClient->request('POST', $url, $payload);
    }

    private function parseGatewayFields(string $body): array
    {
        $body = trim($body);
        if ($body === '') {
            return [];
        }

        $normalized = str_replace(["\r\n", "\r", "\n"], '&', $body);
        if (strpos($normalized, '|') !== false && strpos($normalized, '=') !== false) {
            $normalized = str_replace('|', '&', $normalized);
        }

        $fields = [];
        parse_str($normalized, $fields);
        if (!empty($fields)) {
            return $fields;
        }

        $fallback = [];
        foreach (preg_split('/\r\n|\r|\n/', $body) as $line) {
            if (strpos($line, '=') === false) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $fallback[trim($key)] = trim($value);
        }
        return $fallback;
    }

    private function assertGatewayApproved(array $response, array $fields, string $label): void
    {
        $this->assertSame(200, $response['status'], sprintf('%s returned %s', $label, $response['status']));

        $responseType = strtoupper((string)($fields['pg_response_type'] ?? ''));
        if ($responseType !== '') {
            $this->assertSame('A', $responseType, sprintf('%s not approved: %s', $label, $responseType));
            return;
        }

        $body = strtolower((string)($response['body'] ?? ''));
        foreach (['invalid', 'unauthorized', 'denied', 'error', 'declined'] as $token) {
            $this->assertStringNotContainsString($token, $body, sprintf('%s error detected: %s', $label, $token));
        }
        $this->assertTrue(strpos($body, 'approved') !== false || !empty($fields), sprintf('%s approval not confirmed.', $label));
    }

    private function agiCardType(): string
    {
        $raw = strtoupper($this->optionalEnvValue('FORTE_TEST_CARD_TYPE', 'VISA'));
        $map = [
            'VISA' => 'VISA',
            'VIS' => 'VISA',
            'MAST' => 'MAST',
            'MASTER' => 'MAST',
            'MASTERCARD' => 'MAST',
            'MC' => 'MAST',
            'AMEX' => 'AMER',
            'AMERICANEXPRESS' => 'AMER',
            'AMER' => 'AMER',
            'DISC' => 'DISC',
            'DISCOVER' => 'DISC',
            'DINE' => 'DINE',
            'DINERS' => 'DINE',
            'JCB' => 'JCB',
        ];
        return $map[$raw] ?? $raw;
    }

    private function isPlaceholder(string $value): bool
    {
        return $value === '' || strpos($value, 'YOUR_') !== false;
    }
}
