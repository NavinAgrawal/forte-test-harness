<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxPaymethodOptionalFlowTest extends IntegrationTestCase
{
    public function testClientlessPaypalPaymethod(): void
    {
        $paypalToken = $this->optionalEnvValue('FORTE_TEST_PAYPAL_BILLING_TOKEN', '');
        if ($paypalToken === '') {
            $this->markTestSkipped('FORTE_TEST_PAYPAL_BILLING_TOKEN not set.');
        }

        $payload = [
            'label' => $this->buildOrderNumber('clientless-paypal'),
            'notes' => 'Clientless PayPal',
            'vendor' => [
                'vendor_type' => 'paypal',
                'vendor_billing_agreement_token' => $paypalToken,
            ],
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create clientless paypal paymethod');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        if ($paymethodToken !== '') {
            $delete = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
            );
            $this->assertResponse2xx($delete, 'delete clientless paypal paymethod');
        }
    }

    public function testPermanentPaymethodFromOneTimeTokenCard(): void
    {
        $payload = $this->optionalJsonPayload('FORTE_TEST_PAYMETHOD_ONE_TIME_CARD_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_PAYMETHOD_ONE_TIME_CARD_PAYLOAD not set.');
        }
        if (($payload['card']['one_time_token'] ?? '') === 'ott_SAMPLE_TOKEN') {
            $this->markTestSkipped('Placeholder one_time_token for card paymethod; supply a real token.');
        }

        $customerToken = $this->createCustomer('ott-card');
        if (!isset($payload['label'])) {
            $payload['label'] = $this->buildOrderNumber('ott-card');
        }

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create paymethod from one-time token (card)');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        if ($paymethodToken !== '') {
            $delete = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
            );
            $this->assertResponse2xx($delete, 'delete paymethod (ott card)');
        }

        $this->deleteCustomer($customerToken);
    }

    public function testPermanentPaymethodFromOneTimeTokenEcheck(): void
    {
        $payload = $this->optionalJsonPayload('FORTE_TEST_PAYMETHOD_ONE_TIME_ECHECK_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_PAYMETHOD_ONE_TIME_ECHECK_PAYLOAD not set.');
        }
        if (($payload['echeck']['one_time_token'] ?? '') === 'ott_SAMPLE_TOKEN') {
            $this->markTestSkipped('Placeholder one_time_token for echeck paymethod; supply a real token.');
        }

        $customerToken = $this->createCustomer('ott-echeck');
        if (!isset($payload['label'])) {
            $payload['label'] = $this->buildOrderNumber('ott-echeck');
        }

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create paymethod from one-time token (echeck)');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        if ($paymethodToken !== '') {
            $delete = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
            );
            $this->assertResponse2xx($delete, 'delete paymethod (ott echeck)');
        }

        $this->deleteCustomer($customerToken);
    }

    public function testPaymethodToLocationlessCustomer(): void
    {
        if (!$this->optionalEnvFlag('FORTE_TEST_ENABLE_LOCATIONLESS_PAYMETHOD')) {
            $this->markTestSkipped('FORTE_TEST_ENABLE_LOCATIONLESS_PAYMETHOD not set.');
        }

        $customerToken = $this->createLocationlessCustomer();
        $payload = [
            'notes' => 'Locationless paymethod',
            'organization_id' => $this->organizationId,
            'card' => $this->buildCardPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/customers/' . $customerToken . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create locationless customer paymethod');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        if ($paymethodToken !== '') {
            $delete = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
            );
            $this->assertResponse2xx($delete, 'delete locationless paymethod');
        }

        $this->deleteLocationlessCustomer($customerToken);
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

    private function deleteCustomer(string $customerToken): void
    {
        $delete = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
        );
        $this->assertResponse2xx($delete, 'delete customer');
    }

    private function createLocationlessCustomer(): string
    {
        $billing = $this->defaultBillingAddress();
        $payload = [
            'first_name' => $billing['first_name'],
            'last_name' => $billing['last_name'],
            'company_name' => 'Forte Harness',
            'organization_id' => $this->organizationId,
        ];

        $response = $this->client->request(
            'POST',
            '/customers',
            $payload
        );

        $this->assertResponse2xx($response, 'create locationless customer');
        $customerToken = $response['data']['customer_token'] ?? '';
        $this->assertNotSame('', $customerToken, 'Missing customer_token from locationless customer.');

        return $customerToken;
    }

    private function deleteLocationlessCustomer(string $customerToken): void
    {
        $delete = $this->client->request(
            'DELETE',
            '/customers/' . $customerToken
        );
        $this->assertResponse2xx($delete, 'delete locationless customer');
    }
}
