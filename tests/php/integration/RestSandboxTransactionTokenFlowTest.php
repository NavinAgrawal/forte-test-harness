<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxTransactionTokenFlowTest extends IntegrationTestCase
{
    public function testCreateCustomerAndPaymethods(): array
    {
        $customerToken = $this->createCustomer();
        $cardPaymethod = $this->createCardPaymethod($customerToken);
        $echeckPaymethod = $this->createEcheckPaymethod($customerToken);

        return [
            'customer_token' => $customerToken,
            'card_paymethod' => $cardPaymethod,
            'echeck_paymethod' => $echeckPaymethod,
        ];
    }

    /**
     * @depends testCreateCustomerAndPaymethods
     */
    public function testCardTokenSale(array $state): array
    {
        $payload = [
            'action' => 'sale',
            'authorization_amount' => 0.01,
            'customer_token' => $state['customer_token'],
            'paymethod_token' => $state['card_paymethod'],
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('token-card'),
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'card token sale');
        $tokens = $this->extractTransactionTokens($response['data']);
        $state['card_transaction_id'] = $tokens['transaction_id'] ?? '';
        $state['card_authorization_code'] = $tokens['authorization_code'] ?? '';

        return $state;
    }

    /**
     * @depends testCardTokenSale
     */
    public function testVoidCardTokenSale(array $state): array
    {
        if (empty($state['card_transaction_id']) || empty($state['card_authorization_code'])) {
            $this->markTestSkipped('Card token sale missing transaction_id or authorization_code.');
        }

        $payload = [
            'action' => 'void',
            'transaction_id' => $state['card_transaction_id'],
            'authorization_code' => $state['card_authorization_code'],
            'authorization_amount' => 0.01,
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'void card token sale');
        return $state;
    }

    /**
     * @depends testCreateCustomerAndPaymethods
     */
    public function testEcheckTokenSale(array $state): array
    {
        $payload = [
            'action' => 'sale',
            'authorization_amount' => 0.02,
            'customer_token' => $state['customer_token'],
            'paymethod_token' => $state['echeck_paymethod'],
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('token-echeck'),
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'echeck token sale');
        $tokens = $this->extractTransactionTokens($response['data']);
        $state['echeck_transaction_id'] = $tokens['transaction_id'] ?? '';
        $state['echeck_authorization_code'] = $tokens['authorization_code'] ?? '';

        return $state;
    }

    /**
     * @depends testEcheckTokenSale
     */
    public function testVoidEcheckTokenSale(array $state): array
    {
        if (empty($state['echeck_transaction_id']) || empty($state['echeck_authorization_code'])) {
            $this->markTestSkipped('eCheck token sale missing transaction_id or authorization_code.');
        }

        $payload = [
            'action' => 'void',
            'transaction_id' => $state['echeck_transaction_id'],
            'authorization_code' => $state['echeck_authorization_code'],
            'authorization_amount' => 0.02,
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'void eCheck token sale');
        return $state;
    }

    /**
     * @depends testVoidEcheckTokenSale
     */
    public function testCleanupTokens(array $state): void
    {
        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $state['card_paymethod']
        );
        $this->assertResponse2xx($response, 'delete card paymethod (token flow)');

        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $state['echeck_paymethod']
        );
        $this->assertResponse2xx($response, 'delete echeck paymethod (token flow)');

        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $state['customer_token']
        );
        $this->assertResponse2xx($response, 'delete customer (token flow)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('token-customer');

        $payload = [
            'first_name' => $billing['first_name'],
            'last_name' => $billing['last_name'],
            'company_name' => 'Forte Harness',
            'customer_id' => $label,
            'addresses' => [
                [
                    'label' => $label,
                    'first_name' => $billing['first_name'],
                    'last_name' => $billing['last_name'],
                    'email' => $billing['email'],
                    'phone' => $billing['phone'],
                    'address_type' => 'default_billing',
                    'physical_address' => $billing['physical_address'],
                ],
            ],
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers',
            $payload
        );

        $this->assertResponse2xx($response, 'create customer (token flow)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }

    private function createCardPaymethod(string $customerToken): string
    {
        $payload = [
            'label' => $this->buildOrderNumber('token-card-paymethod'),
            'customer_token' => $customerToken,
            'location_id' => $this->locationId,
            'card' => $this->buildCardPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create card paymethod (token flow)');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return $paymethodToken;
    }

    private function createEcheckPaymethod(string $customerToken): string
    {
        $payload = [
            'label' => $this->buildOrderNumber('token-echeck-paymethod'),
            'customer_token' => $customerToken,
            'location_id' => $this->locationId,
            'echeck' => $this->buildEcheckPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create echeck paymethod (token flow)');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return $paymethodToken;
    }
}
