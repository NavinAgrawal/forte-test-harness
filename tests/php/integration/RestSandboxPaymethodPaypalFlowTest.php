<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxPaymethodPaypalFlowTest extends IntegrationTestCase
{
    public function testCustomerPaypalPaymethodFlow(): void
    {
        $paypalToken = $this->optionalEnvValue('FORTE_TEST_PAYPAL_BILLING_TOKEN', '');
        if ($paypalToken === '') {
            $this->markTestSkipped('FORTE_TEST_PAYPAL_BILLING_TOKEN not set.');
        }

        $customerToken = $this->createCustomer();

        $payload = [
            'notes' => 'PayPal paymethod',
            'vendor' => [
                'vendor_type' => 'paypal',
                'vendor_billing_agreement_token' => $paypalToken,
            ],
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create customer paypal paymethod');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';

        if ($paymethodToken !== '') {
            $deletePaymethod = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
            );
            $this->assertResponse2xx($deletePaymethod, 'delete paypal paymethod');
        }

        $deleteCustomer = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );

        $this->assertResponse2xx($deleteCustomer, 'delete customer (paypal paymethod)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('paypal-paymethod-customer');

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

        $this->assertResponse2xx($response, 'create customer (paypal paymethod)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }
}
