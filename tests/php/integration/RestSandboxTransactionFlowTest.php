<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxTransactionFlowTest extends IntegrationTestCase
{
    private static ?array $cardSale = null;
    private static ?array $echeckSale = null;

    public function testSandboxEnvironment(): void
    {
        $this->assertSame('sandbox', forte_env_name(), 'FORTE_ENV must be sandbox for integration tests.');
        $this->assertStringContainsString('sandbox', $this->baseUrl, 'Base URL should point to sandbox.');
    }

    public function testCreateCardSaleTransaction(): void
    {
        $sale = $this->getCardSale();
        $this->assertNotSame('', $sale['transaction_id'] ?? '', 'Missing transaction_id from card sale.');
    }

    public function testSaleEndpoint(): void
    {
        $payload = $this->buildCardTransactionPayload(0.01);
        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions/sale',
            $payload
        );

        $this->assertResponse2xx($response, 'sale endpoint');
    }

    public function testAuthorizeEndpoint(): void
    {
        $payload = $this->buildCardTransactionPayload(0.01);
        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions/authorize',
            $payload
        );

        $this->assertResponse2xx($response, 'authorize endpoint');
    }

    public function testCreditEndpoint(): void
    {
        $payload = $this->buildCardTransactionPayload(0.01);
        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions/credit',
            $payload
        );

        $this->assertResponse2xx($response, 'credit endpoint');
    }

    public function testVerifyEndpoint(): void
    {
        $payload = [
            'authorization_amount' => 0.01,
            'order_number' => $this->buildOrderNumber('verify'),
            'echeck' => [
                'routing_number' => $this->requireEnvValue('FORTE_TEST_ACH_ROUTING'),
                'account_number' => $this->requireEnvValue('FORTE_TEST_ACH_ACCOUNT'),
            ],
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions/verify',
            $payload
        );

        $this->assertResponse2xx($response, 'verify endpoint');
    }

    public function testAuthenticateEndpoint(): void
    {
        $payload = [
            'action' => 'authenticate',
            'authorization_amount' => 0.01,
            'billing_address' => [
                'first_name' => 'Forte',
                'last_name' => 'Harness',
            ],
            'echeck' => [
                'account_number' => $this->requireEnvValue('FORTE_TEST_ACH_ACCOUNT'),
                'routing_number' => $this->requireEnvValue('FORTE_TEST_ACH_ROUTING'),
                'account_type' => 'checking',
            ],
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions/authenticate',
            $payload
        );

        $this->assertResponse2xx($response, 'authenticate endpoint');
    }

    public function testVoidCardSaleTransaction(): void
    {
        $sale = $this->getCardSale();
        $authCode = $sale['authorization_code'] ?? '';
        if ($authCode === '') {
            $this->markTestSkipped('Card sale missing authorization_code; cannot void.');
        }

        $payload = [
            'action' => 'void',
            'transaction_id' => $sale['transaction_id'],
            'authorization_code' => $authCode,
            'entered_by' => 'forte-harness',
        ];

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'void card transaction');
    }

    public function testCreateEcheckSaleTransaction(): void
    {
        $sale = $this->getEcheckSale();
        $this->assertNotSame('', $sale['transaction_id'] ?? '', 'Missing transaction_id from eCheck sale.');
    }

    public function testVoidEcheckSaleTransaction(): void
    {
        $sale = $this->getEcheckSale();
        $authCode = $sale['authorization_code'] ?? '';
        if ($authCode === '') {
            $this->markTestSkipped('eCheck sale missing authorization_code; cannot void.');
        }

        $payload = [
            'action' => 'void',
            'transaction_id' => $sale['transaction_id'],
            'authorization_code' => $authCode,
            'entered_by' => 'forte-harness',
        ];

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'void eCheck transaction');
    }

    public function testGetCustomerFromCardSale(): void
    {
        $sale = $this->getCardSale();
        $customerToken = $sale['customer_token'] ?? '';
        if ($customerToken === '') {
            $this->markTestSkipped('Card sale missing customer_token; cannot fetch customer.');
        }

        $response = $this->client->request(
            'GET',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );

        $this->assertResponse2xx($response, 'get customer from card sale');
    }

    public function testGetPaymethodFromCardSale(): void
    {
        $sale = $this->getCardSale();
        $paymethodToken = $sale['paymethod_token'] ?? '';
        if ($paymethodToken === '') {
            $this->markTestSkipped('Card sale missing paymethod_token; cannot fetch paymethod.');
        }

        $response = $this->client->request(
            'GET',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
        );

        $this->assertResponse2xx($response, 'get paymethod from card sale');
    }

    private function getCardSale(): array
    {
        if (self::$cardSale !== null) {
            return self::$cardSale;
        }

        $card = $this->buildCardPayload();
        $payload = [
            'action' => 'sale',
            'authorization_amount' => 0.01,
            'save_token' => 'customer',
            'card' => $card,
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('card'),
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'card sale transaction');

        self::$cardSale = $this->extractTransactionTokens($response['data']);
        return self::$cardSale;
    }

    private function getEcheckSale(): array
    {
        if (self::$echeckSale !== null) {
            return self::$echeckSale;
        }

        $echeck = $this->buildEcheckPayload();
        $payload = [
            'action' => 'sale',
            'authorization_amount' => 0.02,
            'save_token' => 'customer',
            'echeck' => $echeck,
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('echeck'),
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'eCheck sale transaction');

        self::$echeckSale = $this->extractTransactionTokens($response['data']);
        return self::$echeckSale;
    }

    private function buildCardTransactionPayload(float $amount): array
    {
        $payload = [
            'authorization_amount' => $amount,
            'billing_address' => [
                'first_name' => 'Forte',
                'last_name' => 'Harness',
            ],
            'card' => $this->buildCardPayload(),
        ];
        return $this->withServiceFee($payload);
    }
}
