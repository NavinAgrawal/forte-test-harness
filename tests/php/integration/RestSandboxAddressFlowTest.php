<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxAddressFlowTest extends IntegrationTestCase
{
    public function testCreateAddress(): array
    {
        $customerToken = $this->createCustomer();
        $billing = $this->defaultBillingAddress();

        $payload = [
            'label' => 'Shipping',
            'first_name' => $billing['first_name'],
            'last_name' => $billing['last_name'],
            'email' => $billing['email'],
            'phone' => $billing['phone'],
            'physical_address' => $billing['physical_address'],
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken . '/addresses',
            $payload
        );

        $this->assertResponse2xx($response, 'create address');
        $addressToken = $response['data']['address_token'] ?? '';
        $this->assertNotSame('', $addressToken, 'Missing address_token from create address response.');

        return [
            'customer_token' => $customerToken,
            'address_token' => $addressToken,
        ];
    }

    /**
     * @depends testCreateAddress
     */
    public function testGetAddress(array $state): array
    {
        $response = $this->client->request(
            'GET',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/addresses/' . $state['address_token']
        );

        $this->assertResponse2xx($response, 'get address');
        return $state;
    }

    /**
     * @depends testCreateAddress
     */
    public function testUpdateAddress(array $state): array
    {
        $payload = [
            'label' => $this->buildOrderNumber('address-update'),
            'physical_address' => [
                'street_line1' => '5059 Updated Street',
                'locality' => 'Testville',
                'region' => 'TX',
                'postal_code' => '75013',
            ],
        ];

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId
            . '/customers/' . $state['customer_token'] . '/addresses/' . $state['address_token'],
            $payload
        );

        $this->assertResponse2xx($response, 'update address');
        return $state;
    }

    /**
     * @depends testUpdateAddress
     */
    public function testDeleteAddress(array $state): array
    {
        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/addresses/' . $state['address_token']
        );

        $this->assertResponse2xx($response, 'delete address');
        return $state;
    }

    /**
     * @depends testDeleteAddress
     */
    public function testDeleteCustomer(array $state): void
    {
        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $state['customer_token']
        );

        $this->assertResponse2xx($response, 'delete customer (address flow)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('address-customer');

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

        $this->assertResponse2xx($response, 'create customer (address flow)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }
}
