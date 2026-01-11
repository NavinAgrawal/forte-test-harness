<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxPaymethodClientlessFlowTest extends IntegrationTestCase
{
    public function testClientlessPaymethodsFlow(): void
    {
        $cardToken = $this->createClientlessCardPaymethod();
        $echeckToken = $this->createClientlessEcheckPaymethod();

        $updatePayload = [
            'label' => $this->buildOrderNumber('clientless-update'),
            'notes' => 'Updated by forte-harness',
        ];

        $updateCard = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $cardToken,
            $updatePayload
        );
        $this->assertResponse2xx($updateCard, 'update clientless card paymethod');

        $updateCardDetails = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $cardToken,
            [
                'card' => [
                    'expire_month' => 12,
                    'expire_year' => 2030,
                    'card_verification_value' => '123',
                ],
            ]
        );
        $this->assertResponse2xx($updateCardDetails, 'update clientless card details');

        $suppressUpdater = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $cardToken,
            [
                'card' => [
                    'suppress_account_updater' => 'true',
                ],
            ]
        );
        $this->assertResponse2xx($suppressUpdater, 'exclude paymethod from account updater');

        $updateEcheck = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $echeckToken,
            $updatePayload
        );
        $this->assertResponse2xx($updateEcheck, 'update clientless echeck paymethod');

        $updateEcheckDetails = $this->client->request(
            'PUT',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $echeckToken,
            [
                'echeck' => [
                    'account_holder' => 'Forte Harness',
                    'routing_number' => $this->requireEnvValue('FORTE_TEST_ACH_ROUTING'),
                    'account_type' => 'checking',
                ],
            ]
        );
        $this->assertResponse2xx($updateEcheckDetails, 'update clientless echeck details');

        $deleteCard = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $cardToken
        );
        $this->assertResponse2xx($deleteCard, 'delete clientless card paymethod');

        $deleteEcheck = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $echeckToken
        );
        $this->assertResponse2xx($deleteEcheck, 'delete clientless echeck paymethod');
    }

    private function createClientlessCardPaymethod(): string
    {
        $payload = [
            'label' => $this->buildOrderNumber('clientless-card'),
            'card' => $this->buildCardPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create clientless card paymethod');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return $paymethodToken;
    }

    private function createClientlessEcheckPaymethod(): string
    {
        $payload = [
            'label' => $this->buildOrderNumber('clientless-echeck'),
            'echeck' => $this->buildEcheckPayload(),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            $payload
        );

        $this->assertResponse2xx($response, 'create clientless echeck paymethod');
        $paymethodToken = $response['data']['paymethod_token'] ?? '';
        $this->assertNotSame('', $paymethodToken, 'Missing paymethod_token from create paymethod response.');

        return $paymethodToken;
    }
}
