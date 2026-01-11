<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxVendorPaypalFlowTest extends IntegrationTestCase
{
    private static ?string $agreementToken = null;
    private static ?string $orderNumber = null;

    public function testCreateVendorAgreementPayload(): void
    {
        $vendorId = $this->vendorAccountId();
        $payload = $this->optionalJsonPayload('FORTE_TEST_VENDOR_AGREEMENT_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_VENDOR_AGREEMENT_PAYLOAD not set.');
        }

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/vendors/' . $vendorId . '/agreements',
            $payload
        );

        $this->assertResponse2xx($response, 'create vendor agreement token');
        $token = $response['data']['vendor_billing_agreement_token'] ?? $response['data']['agreement_token'] ?? '';
        if ($token !== '') {
            self::$agreementToken = $token;
        }
    }

    public function testGetVendorAgreement(): void
    {
        $vendorId = $this->vendorAccountId();
        $token = self::$agreementToken ?: $this->optionalEnvValue('FORTE_TEST_VENDOR_BILLING_AGREEMENT_TOKEN', '');
        if ($token === '') {
            $this->markTestSkipped('No vendor agreement token available.');
        }

        $response = $this->client->request(
            'GET',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/vendors/' . $vendorId . '/agreements/' . $token
        );

        $this->assertResponse2xx($response, 'get vendor agreement token');
    }

    public function testCreateVendorOrderPayload(): void
    {
        $vendorId = $this->vendorAccountId();
        $payload = $this->optionalJsonPayload('FORTE_TEST_VENDOR_ORDER_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_VENDOR_ORDER_PAYLOAD not set.');
        }

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/vendors/' . $vendorId . '/orders',
            $payload
        );

        $this->assertResponse2xx($response, 'create vendor order');
        $orderNumber = $response['data']['vendor_order_number'] ?? $response['data']['order_number'] ?? '';
        if ($orderNumber !== '') {
            self::$orderNumber = $orderNumber;
        }
    }

    public function testCreateVendorOrderNoShippingPayload(): void
    {
        $vendorId = $this->vendorAccountId();
        $payload = $this->optionalJsonPayload('FORTE_TEST_VENDOR_ORDER_NO_SHIP_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_VENDOR_ORDER_NO_SHIP_PAYLOAD not set.');
        }

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/vendors/' . $vendorId . '/orders',
            $payload
        );

        $this->assertResponse2xx($response, 'create vendor order (no shipping)');
    }

    public function testGetVendorOrder(): void
    {
        $vendorId = $this->vendorAccountId();
        $orderNumber = self::$orderNumber ?: $this->optionalEnvValue('FORTE_TEST_VENDOR_ORDER_NUMBER', '');
        if ($orderNumber === '') {
            $this->markTestSkipped('No vendor order number available.');
        }

        $response = $this->client->request(
            'GET',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/vendors/' . $vendorId . '/orders/' . $orderNumber
        );

        $this->assertResponse2xx($response, 'get vendor order');
    }

    public function testUpdateVendorOrder(): void
    {
        $vendorId = $this->vendorAccountId();
        $orderNumber = self::$orderNumber ?: $this->optionalEnvValue('FORTE_TEST_VENDOR_ORDER_NUMBER', '');
        if ($orderNumber === '') {
            $this->markTestSkipped('No vendor order number available.');
        }

        $payload = $this->optionalJsonPayload('FORTE_TEST_VENDOR_ORDER_UPDATE_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_VENDOR_ORDER_UPDATE_PAYLOAD not set.');
        }

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/vendors/' . $vendorId . '/orders/' . $orderNumber,
            $payload
        );

        $this->assertResponse2xx($response, 'update vendor order');
    }

    private function vendorAccountId(): string
    {
        $vendorId = $this->optionalEnvValue('FORTE_TEST_VENDOR_ACCOUNT_ID', '');
        if ($vendorId === '') {
            $this->markTestSkipped('FORTE_TEST_VENDOR_ACCOUNT_ID not set.');
        }
        if (strpos($vendorId, 'ven_') !== 0) {
            $vendorId = 'ven_' . $vendorId;
        }
        return $vendorId;
    }
}
