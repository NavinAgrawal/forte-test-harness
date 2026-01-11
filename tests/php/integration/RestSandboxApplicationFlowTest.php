<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxApplicationFlowTest extends IntegrationTestCase
{
    public function testCreateApplicationPayload(): void
    {
        $payload = $this->optionalJsonPayload('FORTE_TEST_APPLICATION_PAYLOAD');
        if ($payload === null) {
            $this->markTestSkipped('FORTE_TEST_APPLICATION_PAYLOAD not set.');
        }

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/applications',
            $payload
        );

        $this->assertResponse2xx($response, 'create application (payload)');
    }
}
