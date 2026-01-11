<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxTransactionVariantFlowTest extends IntegrationTestCase
{
    public function testTransactionWithLineItems(): void
    {
        $payload = [
            'action' => 'sale',
            'authorization_amount' => 0.01,
            'card' => $this->buildCardPayload(),
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('line-items'),
            'line_items' => [
                'line_item_header' => 'item,qty,price',
                'line_item_1' => 'widget,1,0.01',
            ],
        ];

        $payload = $this->withServiceFee($payload);
        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'transaction with line items');
    }

    public function testTransactionWithXdata(): void
    {
        $payload = [
            'action' => 'sale',
            'authorization_amount' => 0.01,
            'card' => $this->buildCardPayload(),
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('xdata'),
            'xdata' => [
                'xdata_1' => 'forte-harness',
                'xdata_2' => 'integration-test',
            ],
        ];

        $payload = $this->withServiceFee($payload);
        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'transaction with xdata');
    }

    public function testTransactionWithServiceFee(): void
    {
        $serviceFee = $this->serviceFeeAmount();
        if ($serviceFee === null) {
            $this->markTestSkipped('FORTE_TEST_SERVICE_FEE_AMOUNT not set.');
        }

        $payload = [
            'action' => 'sale',
            'authorization_amount' => 0.01,
            'service_fee_amount' => $serviceFee,
            'card' => $this->buildCardPayload(),
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('service-fee'),
        ];

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'transaction with service fee');
    }

    public function testTransactionWithSurchargePayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_SURCHARGE_PAYLOAD',
            'transaction with surcharge'
        );
    }

    public function testTransactionAlternativeUri(): void
    {
        if (!$this->optionalEnvFlag('FORTE_TEST_ENABLE_TRANSACTION_ALT_URI')) {
            $this->markTestSkipped('FORTE_TEST_ENABLE_TRANSACTION_ALT_URI not set.');
        }

        $payload = [
            'action' => 'sale',
            'authorization_amount' => 0.01,
            'card' => $this->buildCardPayload(),
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('alt-uri'),
        ];
        $payload = $this->withServiceFee($payload);

        $response = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/transactions',
            $payload
        );

        $this->assertResponse2xx($response, 'transaction (alternative URI)');
    }

    public function testPartialRefundTransaction(): void
    {
        if (!$this->optionalEnvFlag('FORTE_TEST_ENABLE_PARTIAL_REFUND')) {
            $this->markTestSkipped('FORTE_TEST_ENABLE_PARTIAL_REFUND not set.');
        }

        $salePayload = [
            'action' => 'sale',
            'authorization_amount' => 0.02,
            'card' => $this->buildCardPayload(),
            'billing_address' => $this->defaultBillingAddress(),
            'order_number' => $this->buildOrderNumber('partial-refund'),
        ];
        $salePayload = $this->withServiceFee($salePayload);

        $sale = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $salePayload
        );
        $this->assertResponse2xx($sale, 'sale for partial refund');

        $transactionId = $sale['data']['transaction_id'] ?? '';
        $authCode = $sale['data']['authorization_code'] ?? ($sale['data']['response']['authorization_code'] ?? '');
        if ($transactionId === '' || $authCode === '') {
            $this->markTestSkipped('Partial refund missing transaction_id or authorization_code.');
        }

        $refundPayload = [
            'action' => 'reverse',
            'original_transaction_id' => $transactionId,
            'authorization_code' => $authCode,
            'authorization_amount' => 0.01,
        ];
        $refundPayload = $this->withServiceFee($refundPayload);

        $refund = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions',
            $refundPayload
        );

        $this->assertResponse2xx($refund, 'partial refund transaction');
    }

    public function testTransactionPaypalPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_PAYPAL_PAYLOAD',
            'transaction (paypal)'
        );
    }

    public function testTransactionPaypalTokenPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_PAYPAL_TOKEN_PAYLOAD',
            'transaction with paypal token'
        );
    }

    public function testTransactionDigitalWalletInitialPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_DIGITAL_WALLET_INITIAL_PAYLOAD',
            'initial digital wallet transaction'
        );
    }

    public function testTransactionDigitalWalletDpanPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_DIGITAL_WALLET_DPAN_PAYLOAD',
            'digital wallet dpan transaction'
        );
    }

    public function testTransactionDigitalWalletTokenPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_DIGITAL_WALLET_TOKEN_PAYLOAD',
            'digital wallet token transaction'
        );
    }

    public function testTransactionSwipedPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_SWIPED_PAYLOAD',
            'swiped card transaction'
        );
    }

    public function testTransactionSwipedPaymethodTokenPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_SWIPED_TOKEN_PAYLOAD',
            'paymethod token from swiped transaction'
        );
    }

    public function testTransactionEmvEdynamoPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_EMV_EDYNAMO_PAYLOAD',
            'emv transaction edynamo'
        );
    }

    public function testTransactionEmvDynaflexPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_EMV_DYNAFLEX_PAYLOAD',
            'emv transaction dynaflex'
        );
    }

    public function testTransactionEmvV400cPayload(): void
    {
        $this->runOptionalPayload(
            'FORTE_TEST_TRANSACTION_EMV_V400C_PAYLOAD',
            'emv transaction v400c'
        );
    }

    private function runOptionalPayload(string $envKey, string $label, ?string $path = null): void
    {
        $payload = $this->optionalJsonPayload($envKey);
        if ($payload === null) {
            $this->markTestSkipped($envKey . ' not set.');
        }

        $payload = $this->normalizePayload($payload);
        $skipReason = $payload['_skip_reason'] ?? null;
        if (is_string($skipReason) && $skipReason !== '') {
            $this->markTestSkipped($skipReason);
        }

        $cleanup = $payload['_cleanup'] ?? null;
        if (is_array($cleanup)) {
            unset($payload['_cleanup']);
        }
        if (!isset($payload['action'])) {
            $payload['action'] = 'sale';
        }
        if (!isset($payload['authorization_amount'])) {
            $payload['authorization_amount'] = 0.01;
        }
        if (!isset($payload['order_number'])) {
            $payload['order_number'] = $this->buildOrderNumber('variant');
        }
        $payload = $this->withServiceFee($payload);

        $target = $path ?: '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/transactions';
        $response = $this->client->request('POST', $target, $payload);

        $this->assertResponse2xx($response, $label);

        if (is_array($cleanup)) {
            $this->cleanupPaymethodTokens($cleanup);
        }
    }

    private function cleanupPaymethodTokens(array $tokens): void
    {
        $paymethodToken = $tokens['paymethod_token'] ?? '';
        $customerToken = $tokens['customer_token'] ?? '';

        if ($paymethodToken !== '') {
            $response = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods/' . $paymethodToken
            );
            $this->assertResponse2xx($response, 'cleanup paymethod (variant)');
        }

        if ($customerToken !== '') {
            $response = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
            );
            $this->assertResponse2xx($response, 'cleanup customer (variant)');
        }
    }

    private function normalizePayload(array $payload): array
    {
        if (isset($payload['card']['one_time_token']) && $payload['card']['one_time_token'] === 'ott_SAMPLE_TOKEN') {
            $payload['_skip_reason'] = 'Placeholder one_time_token in payload; supply a real token.';
            return $payload;
        }

        if (isset($payload['vendor']['vendor_order_number']) && $payload['vendor']['vendor_order_number'] === 'ORDER_NUMBER_SAMPLE') {
            $payload['_skip_reason'] = 'Placeholder vendor_order_number in payload; supply a real vendor order number.';
            return $payload;
        }

        if (isset($payload['vendor']['vendor_billing_agreement_id']) && $payload['vendor']['vendor_billing_agreement_id'] === 'B-AGREEMENT-SAMPLE') {
            $payload['_skip_reason'] = 'Placeholder vendor_billing_agreement_id in payload; supply a real PayPal agreement id.';
            return $payload;
        }

        if (isset($payload['card']) && is_array($payload['card'])) {
            $card = $payload['card'];
            $isSwipe = isset($card['card_data']) || isset($card['card_emv_data']);
            if (!$isSwipe) {
                $envCard = $this->buildCardPayload();
                $card['account_number'] = $envCard['account_number'] ?? ($card['account_number'] ?? null);
                $card['expire_month'] = $envCard['expire_month'] ?? ($card['expire_month'] ?? null);
                $card['expire_year'] = $envCard['expire_year'] ?? ($card['expire_year'] ?? null);
                $card['card_verification_value'] = $envCard['card_verification_value'] ?? ($card['card_verification_value'] ?? null);
                $card['card_type'] = $envCard['card_type'] ?? ($card['card_type'] ?? null);
                $card['name_on_card'] = $envCard['name_on_card'] ?? ($card['name_on_card'] ?? null);
                $payload['card'] = $card;
            }
        }

        if (isset($payload['echeck']) && is_array($payload['echeck'])) {
            $echeck = $payload['echeck'];
            $envEcheck = $this->buildEcheckPayload();
            $echeck['routing_number'] = $envEcheck['routing_number'] ?? ($echeck['routing_number'] ?? null);
            $echeck['account_number'] = $envEcheck['account_number'] ?? ($echeck['account_number'] ?? null);
            $echeck['account_type'] = $envEcheck['account_type'] ?? ($echeck['account_type'] ?? null);
            $echeck['sec_code'] = $envEcheck['sec_code'] ?? ($echeck['sec_code'] ?? null);
            $echeck['account_holder'] = $envEcheck['account_holder'] ?? ($echeck['account_holder'] ?? null);
            $payload['echeck'] = $echeck;
        }

        if (isset($payload['paymethod_token']) && is_string($payload['paymethod_token'])) {
            $token = trim($payload['paymethod_token']);
            if ($token === '' || $token === 'mth_SAMPLE') {
                $tokens = $this->createPaymethodTokenWithCustomer();
                $payload['paymethod_token'] = $tokens['paymethod_token'];
                $payload['_cleanup'] = $tokens;
            }
        }

        return $payload;
    }

    private function createPaymethodTokenWithCustomer(): array
    {
        $customerPayload = [
            'first_name' => 'Forte',
            'last_name' => 'Harness',
            'company_name' => 'Forte Harness',
            'customer_id' => $this->buildOrderNumber('variant-customer'),
            'addresses' => [
                [
                    'label' => 'Billing',
                    'first_name' => 'Forte',
                    'last_name' => 'Harness',
                    'email' => 'integration@forte.net',
                    'phone' => '555-555-5555',
                    'address_type' => 'default_billing',
                    'physical_address' => $this->defaultBillingAddress()['physical_address'],
                ],
            ],
        ];

        $customerResponse = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers',
            $customerPayload
        );
        $this->assertResponse2xx($customerResponse, 'create customer (variant paymethod)');
        $customerToken = $customerResponse['data']['customer_token'] ?? '';
        if ($customerToken === '') {
            $this->fail('Failed to create customer for paymethod token.');
        }

        $paymethodResponse = $this->client->request(
            'POST',
            '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/paymethods',
            [
                'label' => $this->buildOrderNumber('variant-paymethod'),
                'customer_token' => $customerToken,
                'location_id' => $this->locationId,
                'card' => $this->buildCardPayload(),
            ]
        );

        $this->assertResponse2xx($paymethodResponse, 'create paymethod (variant token)');
        $paymethodToken = $paymethodResponse['data']['paymethod_token'] ?? '';
        if ($paymethodToken === '') {
            $this->fail('Failed to create paymethod for token payload.');
        }

        return [
            'customer_token' => $customerToken,
            'paymethod_token' => $paymethodToken,
        ];
    }
}
