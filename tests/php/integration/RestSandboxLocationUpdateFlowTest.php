<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxLocationUpdateFlowTest extends IntegrationTestCase
{
    public function testUpdateLocationAddressPayload(): void
    {
        $payload = $this->optionalJsonPayload('FORTE_TEST_LOCATION_UPDATE_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_LOCATION_UPDATE_PAYLOAD not set.');
        }

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId,
            $payload
        );

        $this->assertResponse2xx($response, 'update location address (payload)');
    }

    public function testUpdateLocationLimitsPayload(): void
    {
        $payload = $this->optionalJsonPayload('FORTE_TEST_LOCATION_LIMITS_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_LOCATION_LIMITS_PAYLOAD not set.');
        }

        $response = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId,
            $payload
        );

        $this->assertResponse2xx($response, 'change processing limits (payload)');
    }
}
