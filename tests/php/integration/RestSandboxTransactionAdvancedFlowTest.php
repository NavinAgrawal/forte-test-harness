<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxTransactionAdvancedFlowTest extends IntegrationTestCase
{
    private static ?array $authorize = null;
    private static ?array $saleForReverse = null;
    private static ?array $authorizeForVoidById = null;

    public function testCaptureAuthorizedTransaction(): void
    {
        $authorize = $this->getAuthorize();
        $authCode = $authorize['authorization_code'] ?? '';
        if ($authCode === '') {
            $this->markTestSkipped('Authorize transaction missing authorization_code; cannot capture.');
        }

        $payload = [
            'action' => 'capture',
            'transaction_id' => $authorize['transaction_id'],
            'authorization_code' => $authCode,
            'authorization_amount' => 0.01,
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'capture authorized transaction');
    }

    public function testReverseSaleTransaction(): void
    {
        $sale = $this->getSaleForReverse();
        $authCode = $sale['authorization_code'] ?? '';
        if ($authCode === '') {
            $this->markTestSkipped('Sale missing authorization_code; cannot reverse.');
        }

        $payload = [
            'action' => 'reverse',
            'original_transaction_id' => $sale['transaction_id'],
            'authorization_code' => $authCode,
            'authorization_amount' => 0.01,
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions/force',
            $payload
        );

        $this->assertResponse2xx($response, 'reverse sale transaction');
    }

    public function testVoidAuthorizationById(): void
    {
        $authorization = $this->getAuthorizeForVoidById();
        $authCode = $authorization['authorization_code'] ?? '';
        if ($authCode === '') {
            $this->markTestSkipped('Authorization missing authorization_code; cannot void by id.');
        }

        $payload = [
            'action' => 'void',
            'authorization_code' => $authCode,
            'authorization_amount' => 0.01,
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions/' . $authorization['transaction_id'],
            $payload
        );

        $this->assertResponse2xx($response, 'void authorization by id');
    }

    public function testForceTransactionPayload(): void
    {
        $payload = $this->optionalJsonPayload('FORTE_TEST_FORCE_TRANSACTION_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_FORCE_TRANSACTION_PAYLOAD not set.');
        }

        if (!isset($payload['action'])) {
            $payload['action'] = 'force';
        }
        if (!isset($payload['authorization_amount'])) {
            $payload['authorization_amount'] = 0.01;
        }
        if (!isset($payload['order_number'])) {
            $payload['order_number'] = $this->buildOrderNumber('force');
        }
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'force transaction (payload)');
    }

    public function testOneTimeTokenTransactionPayload(): void
    {
        $payload = $this->optionalJsonPayload('FORTE_TEST_ONE_TIME_TOKEN_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_ONE_TIME_TOKEN_PAYLOAD not set.');
        }

        if (!isset($payload['action'])) {
            $payload['action'] = 'sale';
        }
        if (!isset($payload['authorization_amount'])) {
            $payload['authorization_amount'] = 0.01;
        }
        if (!isset($payload['order_number'])) {
            $payload['order_number'] = $this->buildOrderNumber('one-time');
        }
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'one-time token transaction (payload)');
    }

    private function getAuthorize(): array
    {
        if (self::$authorize !== null) {
            return self::$authorize;
        }

        $payload = [
            'action' => 'authorize',
            'authorization_amount' => 0.01,
            'card' => $this->buildCardPayload(),
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('authorize'),
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'authorize transaction');
        self::$authorize = $this->extractTransactionTokens($response['data']);
        return self::$authorize;
    }

    private function getSaleForReverse(): array
    {
        if (self::$saleForReverse !== null) {
            return self::$saleForReverse;
        }

        $payload = [
            'action' => 'sale',
            'authorization_amount' => 0.01,
            'card' => $this->buildCardPayload(),
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('reverse'),
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'sale transaction (reverse)');
        self::$saleForReverse = $this->extractTransactionTokens($response['data']);
        return self::$saleForReverse;
    }

    private function getAuthorizeForVoidById(): array
    {
        if (self::$authorizeForVoidById !== null) {
            return self::$authorizeForVoidById;
        }

        $payload = [
            'action' => 'authorize',
            'authorization_amount' => 0.01,
            'card' => $this->buildCardPayload(),
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('void-auth'),
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'authorize transaction (void by id)');
        self::$authorizeForVoidById = $this->extractTransactionTokens($response['data']);
        return self::$authorizeForVoidById;
    }
}
