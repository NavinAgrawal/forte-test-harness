<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxCustomerPaymethodFlowTest extends IntegrationTestCase
{
    public function testUpdateCustomerWithPaymethod(): void
    {
        $customerToken = $this->createCustomer();
        $payload = [
            'label' => $this->buildOrderNumber('customer-paymethod'),
            'card' => $this->buildCardPayload(),
        ];

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'update customer with paymethod');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';

        if ($paymethodToken !== '') {
            $deletePaymethod = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
            );
            $this->assertResponse2xx($deletePaymethod, 'delete paymethod (customer paymethod flow)');
        }

        $deleteCustomer = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );

        $this->assertResponse2xx($deleteCustomer, 'delete customer (customer paymethod flow)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('customer-paymethod');

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

        $this->assertResponse2xx($response, 'create customer (customer paymethod flow)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }
}
