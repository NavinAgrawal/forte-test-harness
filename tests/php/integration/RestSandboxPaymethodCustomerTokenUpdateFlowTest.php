<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxPaymethodCustomerTokenUpdateFlowTest extends IntegrationTestCase
{
    public function testUpdatePaymethodCustomerTokenFlow(): void
    {
        $customerA = $this->createCustomer('customer-a');
        $customerB = $this->createCustomer('customer-b');

        $paymethodToken = $this->createPaymethod($customerA);

        $update = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken,
            [
                'customer_token' => $customerB,
            ]
        );
        $this->assertResponse2xx($update, 'update paymethod customer token');

        $deletePaymethod = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
        );
        $this->assertResponse2xx($deletePaymethod, 'delete paymethod (customer token update)');

        $this->deleteCustomer($customerA);
        $this->deleteCustomer($customerB);
    }

    private function createCustomer(string $suffix): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('paymethod-' . $suffix);

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

        $this->assertResponse2xx($response, 'create customer (' . $suffix . ')');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }

    private function createPaymethod(string $customerToken): string
    {
        $payload = [
            'label' => $this->buildOrderNumber('paymethod-customer-update'),
            'customer_token' => $customerToken,
            'location_id' => $this->locationId,
            'card' => $this->buildCardPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create paymethod (customer token update)');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return $paymethodToken;
    }

    private function deleteCustomer(string $customerToken): void
    {
        $delete = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );
        $this->assertResponse2xx($delete, 'delete customer');
    }
}
