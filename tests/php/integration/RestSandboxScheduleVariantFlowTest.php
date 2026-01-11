<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxScheduleVariantFlowTest extends IntegrationTestCase
{
    public function testScheduleVariantsFlow(): void
    {
        $customerToken = $this->createCustomer();
        $paymethodToken = $this->createPaymethod($customerToken);

        $startMonthly = date('m/d/Y', strtotime('+30 days'));
        $startWeekly = date('Y-m-d\\T00:00:00', strtotime('+14 days'));
        $startOneTime = date('m/d/Y', strtotime('+45 days'));

        $variants = [
            [
                'label' => '12-month schedule',
                'payload' => [
                    'customer_token' => $customerToken,
                    'paymethod_token' => $paymethodToken,
                    'action' => 'sale',
                    'schedule_quantity' => 12,
                    'schedule_frequency' => 'monthly',
                    'schedule_amount' => 0.01,
                    'schedule_start_date' => $startMonthly,
                    'order_number' => $this->buildOrderNumber('schedule-12'),
                ],
            ],
            [
                'label' => 'weekly schedule',
                'payload' => [
                    'customer_token' => $customerToken,
                    'paymethod_token' => $paymethodToken,
                    'action' => 'sale',
                    'schedule_quantity' => 4,
                    'schedule_frequency' => 'weekly',
                    'schedule_amount' => 0.01,
                    'schedule_start_date' => $startWeekly,
                    'order_number' => $this->buildOrderNumber('schedule-weekly'),
                ],
            ],
            [
                'label' => 'one-time future schedule',
                'payload' => [
                    'customer_token' => $customerToken,
                    'paymethod_token' => $paymethodToken,
                    'action' => 'sale',
                    'schedule_frequency' => 'one_time_future',
                    'schedule_amount' => 0.01,
                    'schedule_start_date' => $startOneTime,
                    'order_number' => $this->buildOrderNumber('schedule-onetime'),
                ],
            ],
            [
                'label' => 'continuous schedule',
                'payload' => [
                    'customer_token' => $customerToken,
                    'paymethod_token' => $paymethodToken,
                    'action' => 'sale',
                    'schedule_quantity' => 0,
                    'schedule_frequency' => 'monthly',
                    'schedule_amount' => 0.01,
                    'schedule_start_date' => $startMonthly,
                    'order_number' => $this->buildOrderNumber('schedule-continuous'),
                ],
            ],
        ];

        $scheduleIds = [];
        foreach ($variants as $variant) {
            $response = $this->client->request(
                'POST',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/schedules',
                $variant['payload']
            );

            $this->assertResponse2xx($response, 'create ' . $variant['label']);
            $scheduleId = $response['data']['schedule_id'] ?? '';
            $this->assertNotSame('', $scheduleId, 'Missing schedule_id for ' . $variant['label']);
            $scheduleIds[] = $scheduleId;
        }

        foreach ($scheduleIds as $scheduleId) {
            $delete = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/schedules/' . $scheduleId
            );
            $this->assertResponse2xx($delete, 'delete schedule variant');
        }

        $deletePaymethod = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
        );
        $this->assertResponse2xx($deletePaymethod, 'delete paymethod (schedule variants)');

        $deleteCustomer = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );
        $this->assertResponse2xx($deleteCustomer, 'delete customer (schedule variants)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('schedule-variant-customer');

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

        $this->assertResponse2xx($response, 'create customer (schedule variants)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }

    private function createPaymethod(string $customerToken): string
    {
        $payload = [
            'label' => $this->buildOrderNumber('schedule-variant-paymethod'),
            'customer_token' => $customerToken,
            'location_id' => $this->locationId,
            'card' => $this->buildCardPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create paymethod (schedule variants)');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return $paymethodToken;
    }
}
