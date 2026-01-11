<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\FormClient;
use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
#[Group('non-rest')]
#[Group('swp')]
class SwpSandboxFlowTest extends IntegrationTestCase
{
    private FormClient $formClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formClient = new FormClient($this->sslVerifyEnabled());
    }

    public function testSandboxEnvironment(): void
    {
        $this->assertSame('sandbox', forte_env_name(), 'FORTE_ENV must be sandbox for SWP integration tests.');
    }

    public function testEmbeddedChargePage(): void
    {
        $payload = $this->buildSwpPayload('swp_embedded_charge', '0.01');
        $response = $this->formClient->request('POST', forte_swp_url('default.aspx'), $payload);
        $this->assertSwpResponse($response, 'SWP embedded charge');
    }

    public function testRedirectCheckoutPage(): void
    {
        $payload = $this->buildSwpPayload('swp_redirect_full', '0.01');
        $response = $this->formClient->request('POST', forte_swp_url('Redirect/default.aspx'), $payload);
        $this->assertSwpResponse($response, 'SWP redirect checkout');
    }

    private function buildSwpPayload(string $keyAlias, string $amount): array
    {
        $apiLoginId = (string)forte_config('api_login_id');
        if ($this->isPlaceholder($apiLoginId)) {
            $this->markTestSkipped('SWP credentials not configured (api_login_id).');
        }
        $this->assertConfigValue('api_login_id', $apiLoginId);

        $secureKeys = (array)forte_config('secure_transaction_keys', []);
        $secureKey = $secureKeys[$keyAlias] ?? forte_config('secure_transaction_key');
        if ($this->isPlaceholder((string)$secureKey)) {
            $this->markTestSkipped('SWP credentials not configured (secure_transaction_key).');
        }
        $this->assertConfigValue('secure_transaction_key', (string)$secureKey);

        $transType = '10';
        $version = '2.0';
        $order = $this->buildOrderNumber('swp');
        $utc = $this->utcTicks();
        $data = sprintf('%s|%s|%s|%s|%s|%s', $apiLoginId, $transType, $version, $amount, $utc, $order);
        $hash = hash_hmac('md5', $data, (string)$secureKey);

        return [
            'pg_api_login_id' => $apiLoginId,
            'pg_transaction_type' => $transType,
            'pg_version_number' => $version,
            'pg_total_amount' => $amount,
            'pg_utc_time' => $utc,
            'pg_transaction_order_number' => $order,
            'pg_ts_hash' => $hash,
            'pg_billto_postal_name_first' => 'Forte',
            'pg_billto_postal_name_last' => 'Harness',
            'pg_billto_postal_street_line1' => '500 Bethany Dr',
            'pg_billto_postal_city' => 'Allen',
            'pg_billto_postal_state' => 'TX',
            'pg_billto_postal_postalcode' => '75013',
            'pg_billto_online_email' => 'integration@forte.net',
        ];
    }

    private function utcTicks(): string
    {
        $ticks = (int)(microtime(true) * 10000) + 621355968000000000;
        return (string)$ticks;
    }

    private function assertSwpResponse(array $response, string $label): void
    {
        $this->assertSame(200, $response['status'], sprintf('%s returned %s', $label, $response['status']));
        $body = strtolower((string)($response['body'] ?? ''));
        foreach (['invalid', 'unauthorized', 'error', 'failed', 'denied', 'hash'] as $token) {
            $this->assertStringNotContainsString($token, $body, sprintf('%s error detected: %s', $label, $token));
        }
        $this->assertNotSame('', trim((string)($response['body'] ?? '')), sprintf('%s returned empty body.', $label));
    }

    private function isPlaceholder(string $value): bool
    {
        return $value === '' || strpos($value, 'YOUR_') !== false;
    }
}
