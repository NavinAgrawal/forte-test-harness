<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxPaymethodBillingAddressFlowTest extends IntegrationTestCase
{
    public function testUpdatePaymethodBillingAddress(): void
    {
        $customerToken = $this->createCustomer();
        $addressToken = $this->createAddress($customerToken);
        $paymethodToken = $this->createPaymethod($customerToken);

        $update = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken,
            [
                'billing_address_token' => $addressToken,
            ]
        );
        $this->assertResponse2xx($update, 'update paymethod billing address');

        $deletePaymethod = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
        );
        $this->assertResponse2xx($deletePaymethod, 'delete paymethod (billing address)');

        $deleteAddress = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/addresses/' . $addressToken
        );
        $this->assertResponse2xx($deleteAddress, 'delete address (billing address)');

        $deleteCustomer = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );
        $this->assertResponse2xx($deleteCustomer, 'delete customer (billing address)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('billing-address-customer');

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

        $this->assertResponse2xx($response, 'create customer (billing address)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }

    private function createAddress(string $customerToken): string
    {
        $billing = $this->defaultBillingAddress();

        $payload = [
            'label' => 'Billing',
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

        $this->assertResponse2xx($response, 'create address (billing address)');
        $addressToken = $response['data']['address_token'] ?? '';
        $this->assertNotSame('', $addressToken, 'Missing address_token from create address response.');

        return $addressToken;
    }

    private function createPaymethod(string $customerToken): string
    {
        $payload = [
            'label' => $this->buildOrderNumber('billing-address-paymethod'),
            'customer_token' => $customerToken,
            'location_id' => $this->locationId,
            'card' => $this->buildCardPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create paymethod (billing address)');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return $paymethodToken;
    }
}
