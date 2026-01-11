<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxPaymethodFlowTest extends IntegrationTestCase
{
    public function testCreatePaymethod(): array
    {
        $customerToken = $this->createCustomer();
        $card = $this->buildCardPayload();
        $label = $this->buildOrderNumber('paymethod');

        $payload = [
            'label' => $label,
            'customer_token' => $customerToken,
            'location_id' => $this->locationId,
            'card' => $card,
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create paymethod');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return [
            'customer_token' => $customerToken,
            'paymethod_token' => $paymethodToken,
        ];
    }

    #[Depends('testCreatePaymethod')]
    public function testGetPaymethod(array $state): array
    {
        $response = $this->client->request(
            'GET',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $state['paymethod_token']
        );

        $this->assertResponse2xx($response, 'get paymethod');
        return $state;
    }

    #[Depends('testCreatePaymethod')]
    public function testUpdatePaymethod(array $state): array
    {
        $payload = [
            'label' => $this->buildOrderNumber('paymethod-update'),
            'notes' => 'Updated by forte-harness',
        ];

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $state['paymethod_token'],
            $payload
        );

        $this->assertResponse2xx($response, 'update paymethod');
        return $state;
    }

    #[Depends('testCreatePaymethod')]
    public function testDeletePaymethod(array $state): array
    {
        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $state['paymethod_token']
        );

        $this->assertResponse2xx($response, 'delete paymethod');
        return $state;
    }

    #[Depends('testCreatePaymethod')]
    public function testDeleteCustomer(array $state): void
    {
        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $state['customer_token']
        );

        $this->assertResponse2xx($response, 'delete customer (paymethod flow)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('paymethod-customer');

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

        $this->assertResponse2xx($response, 'create customer (paymethod flow)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }
}
