<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxAddressAlternateFlowTest extends IntegrationTestCase
{
    public function testAlternateAddressFlow(): void
    {
        if (!$this->optionalEnvFlag('FORTE_TEST_ENABLE_ADDRESS_ALT_URI')) {
            $this->markTestSkipped('Alternate address URI flow disabled (FORTE_TEST_ENABLE_ADDRESS_ALT_URI).');
        }

        $customerToken = $this->createCustomer();
        $billing = $this->defaultBillingAddress();

        $payload = [
            'label' => 'AltShipping',
            'first_name' => $billing['first_name'],
            'last_name' => $billing['last_name'],
            'email' => $billing['email'],
            'phone' => $billing['phone'],
            'physical_address' => $billing['physical_address'],
        ];

        $createResponse = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/customers/' . $customerToken . '/addresses',
            $payload
        );

        $this->assertResponse2xx($createResponse, 'create address (alternate URI)');
        $addressToken = $createResponse['data']['address_token'] ?? '';
        $this->assertNotSame('', $addressToken, 'Missing address_token from alternate address create.');

        $updatePayload = [
            'label' => $this->buildOrderNumber('address-alt-update'),
            'physical_address' => [
                'street_line1' => '5060 Alternate Street',
                'locality' => 'Testville',
                'region' => 'TX',
                'postal_code' => '75013',
            ],
        ];

        $updateResponse = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/customers/' . $customerToken . '/addresses/' . $addressToken,
            $updatePayload
        );

        $this->assertResponse2xx($updateResponse, 'update address (alternate URI)');

        $deleteResponse = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/addresses/' . $addressToken
        );

        $this->assertResponse2xx($deleteResponse, 'delete address (alternate URI)');

        $deleteCustomer = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );

        $this->assertResponse2xx($deleteCustomer, 'delete customer (alternate address flow)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('alt-address-customer');

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

        $this->assertResponse2xx($response, 'create customer (alternate address flow)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }
}
