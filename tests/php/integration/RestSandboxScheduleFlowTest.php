<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxScheduleFlowTest extends IntegrationTestCase
{
    public function testCreateSchedule(): array
    {
        $customerToken = $this->createCustomer();
        $paymethodToken = $this->createPaymethod($customerToken);

        $payload = [
            'customer_token' => $customerToken,
            'paymethod_token' => $paymethodToken,
            'action' => 'sale',
            'schedule_quantity' => 1,
            'schedule_frequency' => 'monthly',
            'schedule_amount' => 0.01,
            'schedule_start_date' => $this->futureDate(30),
            'order_number' => $this->buildOrderNumber('schedule'),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/schedules',
            $payload
        );

        $this->assertResponse2xx($response, 'create schedule');
        $scheduleId = $response['data']['schedule_id'] ?? '';
        $this->assertNotSame('', $scheduleId, 'Missing schedule_id from create schedule response.');

        return [
            'customer_token' => $customerToken,
            'paymethod_token' => $paymethodToken,
            'schedule_id' => $scheduleId,
        ];
    }

    #[Depends('testCreateSchedule')]
    public function testGetSchedule(array $state): array
    {
        $response = $this->client->request(
            'GET',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/schedules/' . $state['schedule_id']
        );

        $this->assertResponse2xx($response, 'get schedule');
        return $state;
    }

    #[Depends('testCreateSchedule')]
    public function testUpdateSchedule(array $state): array
    {
        $payload = [
            'schedule_amount' => 0.02,
            'order_number' => $this->buildOrderNumber('schedule-update'),
        ];

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/schedules/' . $state['schedule_id'],
            $payload
        );

        $this->assertResponse2xx($response, 'update schedule');
        return $state;
    }

    #[Depends('testCreateSchedule')]
    public function testCreateScheduleItem(array $state): array
    {
        $payload = [
            'schedule_id' => $state['schedule_id'],
            'customer_token' => $state['customer_token'],
            'paymethod_token' => $state['paymethod_token'],
            'schedule_item_amount' => 0.01,
            'schedule_item_status' => 'scheduled',
            'schedule_item_date' => $this->futureDate(35),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId
            . '/schedules/' . $state['schedule_id'] . '/scheduleitems',
            $payload
        );

        $this->assertResponse2xx($response, 'create schedule item');
        $scheduleItemId = $response['data']['scheduleitem_id'] ?? $response['data']['schedule_item_id'] ?? '';
        $this->assertNotSame('', $scheduleItemId, 'Missing scheduleitem_id from create schedule item response.');

        $state['scheduleitem_id'] = $scheduleItemId;
        return $state;
    }

    #[Depends('testCreateScheduleItem')]
    public function testGetScheduleItem(array $state): array
    {
        $response = $this->client->request(
            'GET',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/scheduleitems/' . $state['scheduleitem_id']
        );

        $this->assertResponse2xx($response, 'get schedule item');
        return $state;
    }

    #[Depends('testCreateScheduleItem')]
    public function testUpdateScheduleItem(array $state): array
    {
        $payload = [
            'schedule_item_amount' => 0.02,
            'schedule_item_status' => 'scheduled',
            'schedule_item_date' => $this->futureDate(40),
        ];

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/scheduleitems/' . $state['scheduleitem_id'],
            $payload
        );

        $this->assertResponse2xx($response, 'update schedule item');
        return $state;
    }

    #[Depends('testUpdateScheduleItem')]
    public function testDeleteScheduleItem(array $state): array
    {
        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/scheduleitems/' . $state['scheduleitem_id']
        );

        $this->assertResponse2xx($response, 'delete schedule item');
        return $state;
    }

    #[Depends('testDeleteScheduleItem')]
    public function testDeleteSchedule(array $state): array
    {
        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/schedules/' . $state['schedule_id']
        );

        $this->assertResponse2xx($response, 'delete schedule');
        return $state;
    }

    #[Depends('testDeleteSchedule')]
    public function testDeletePaymethodAndCustomer(array $state): void
    {
        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $state['paymethod_token']
        );
        $this->assertResponse2xx($response, 'delete paymethod (schedule flow)');

        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $state['customer_token']
        );
        $this->assertResponse2xx($response, 'delete customer (schedule flow)');
    }

    private function createCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $label = $this->buildOrderNumber('schedule-customer');

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

        $this->assertResponse2xx($response, 'create customer (schedule flow)');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from create customer response.');

        return $customerToken;
    }

    private function createPaymethod(string $customerToken): string
    {
        $card = $this->buildCardPayload();
        $label = $this->buildOrderNumber('schedule-paymethod');

        $payload = [
            'label' => $label,
            'customer_token' => $customerToken,
            'location_id' => $this->locationId,
            'card' => $card,
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create paymethod (schedule flow)');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return $paymethodToken;
    }

    private function futureDate(int $days): string
    {
        return date('m/d/Y', strtotime('+' . $days . ' days'));
    }
}
