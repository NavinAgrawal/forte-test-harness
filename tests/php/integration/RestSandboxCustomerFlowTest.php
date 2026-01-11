<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxCustomerFlowTest extends IntegrationTestCase
{
    public function testCreateCustomer(): array
    {
        $payload = $this->buildCustomerPayload('create');
        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers',
            $payload
        );

        $this->assertResponse2xx($response, 'create customer');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return ['customer_token' => $customerToken];
    }

    /**
     * @depends testCreateCustomer
     */
    public function testGetCustomer(array $state): array
    {
        $response = $this->client->request(
            'GET',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $state['customer_token']
        );

        $this->assertResponse2xx($response, 'get customer');
        return $state;
    }

    /**
     * @depends testCreateCustomer
     */
    public function testUpdateCustomer(array $state): array
    {
        $payload = $this->buildCustomerPayload('update');
        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $state['customer_token'],
            $payload
        );

        $this->assertResponse2xx($response, 'update customer');
        return $state;
    }

    /**
     * @depends testCreateCustomer
     */
    public function testUpdateCustomerAlternateUri(array $state): array
    {
        $payload = $this->buildCustomerPayload('update-alt');
        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/customers/' . $state['customer_token'],
            $payload
        );

        $this->assertResponse2xx($response, 'update customer (alternate URI)');
        return $state;
    }

    /**
     * @depends testCreateCustomer
     */
    public function testDeleteCustomer(array $state): void
    {
        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $state['customer_token']
        );

        $this->assertResponse2xx($response, 'delete customer');
    }

    private function buildCustomerPayload(string $suffix): array
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('customer-' . $suffix);

        return [
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
    }
}
