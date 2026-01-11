<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxScheduleAlternateFlowTest extends IntegrationTestCase
{
    public function testCustomerScheduleAlternateUri(): void
    {
        if (!$this->optionalEnvFlag('FORTE_TEST_ENABLE_SCHEDULE_ALT_URI')) {
            $this->markTestSkipped('Alternate schedule URI flow disabled (FORTE_TEST_ENABLE_SCHEDULE_ALT_URI).');
        }

        $customerToken = $this->createCustomer();
        $paymethodToken = $this->createPaymethod($customerToken);

        $payload = [
            'customer_token' => $customerToken,
            'paymethod_token' => $paymethodToken,
            'action' => 'sale',
            'schedule_quantity' => 1,
            'schedule_frequency' => 'monthly',
            'schedule_amount' => 0.01,
            'schedule_start_date' => date('m/d/Y', strtotime('+30 days')),
            'order_number' => $this->buildOrderNumber('schedule-alt'),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken . '/schedules',
            $payload
        );

        $this->assertResponse2xx($response, 'create schedule (alternate URI)');
        $scheduleId = $response['data']['schedule_id'] ?? '';
        $this->assertNotSame('', $scheduleId, 'Missing schedule_id from alternate schedule create.');

        $deleteSchedule = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/schedules/' . $scheduleId
        );
        $this->assertResponse2xx($deleteSchedule, 'delete schedule (alternate URI)');

        $deletePaymethod = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
        );
        $this->assertResponse2xx($deletePaymethod, 'delete paymethod (alternate schedule flow)');

        $deleteCustomer = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );
        $this->assertResponse2xx($deleteCustomer, 'delete customer (alternate schedule flow)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('schedule-alt-customer');

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

        $this->assertResponse2xx($response, 'create customer (alternate schedule flow)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }

    private function createPaymethod(string $customerToken): string
    {
        $payload = [
            'label' => $this->buildOrderNumber('schedule-alt-paymethod'),
            'customer_token' => $customerToken,
            'location_id' => $this->locationId,
            'card' => $this->buildCardPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create paymethod (alternate schedule flow)');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return $paymethodToken;
    }
}
