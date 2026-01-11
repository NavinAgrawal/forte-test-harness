<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxPaymethodCustomerEcheckFlowTest extends IntegrationTestCase
{
    public function testCustomerEcheckPaymethodFlow(): void
    {
        $customerToken = $this->createCustomer();

        $payload = [
            'notes' => 'Customer echeck',
            'echeck' => $this->buildEcheckPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create customer echeck paymethod');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from customer echeck paymethod.');

        $updatePayload = [
            'notes' => 'Updated echeck paymethod',
            'echeck' => [
                'account_holder' => 'Forte Harness',
                'routing_number' => $this->requireEnvValue('FORTE_TEST_ACH_ROUTING'),
                'account_type' => 'checking',
            ],
        ];

        $update = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken,
            $updatePayload
        );
        $this->assertResponse2xx($update, 'update customer echeck paymethod');

        $deletePaymethod = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
        );
        $this->assertResponse2xx($deletePaymethod, 'delete customer echeck paymethod');

        $deleteCustomer = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );
        $this->assertResponse2xx($deleteCustomer, 'delete customer (echeck paymethod flow)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('echeck-paymethod-customer');

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

        $this->assertResponse2xx($response, 'create customer (echeck paymethod flow)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }
}
