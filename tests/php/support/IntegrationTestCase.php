<?php

namespace ForteTestHarness\Tests\Support;

use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected RestClient $client;
    protected string $baseUrl;
    protected string $organizationId;
    protected string $locationId;
    private array $tokenCache = [];

    protected function setUp(): void
    {
        $this->baseUrl = rtrim((string)forte_base_url(), '/');
        $this->organizationId = $this->ensurePrefix((string)forte_config('organization_id'), 'org_');
        $this->locationId = $this->ensurePrefix((string)forte_config('location_id'), 'loc_');
        $apiAccessId = (string)forte_config('api_access_id');
        $apiSecureKey = (string)forte_config('api_secure_key');

        $this->assertConfigValue('organization_id', $this->organizationId);
        $this->assertConfigValue('location_id', $this->locationId);
        $this->assertConfigValue('api_access_id', $apiAccessId);
        $this->assertConfigValue('api_secure_key', $apiSecureKey);

        $this->client = new RestClient(
            $this->baseUrl,
            $this->organizationId,
            $apiAccessId,
            $apiSecureKey,
            $this->sslVerifyEnabled()
        );
    }

    protected function sslVerifyEnabled(): bool
    {
        $value = getenv('FORTE_SSL_VERIFY');
        if ($value === false || $value === '') {
            return false;
        }
        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }

    protected function ensurePrefix(string $value, string $prefix): string
    {
        if ($value === '') {
            return $value;
        }
        if (strpos($value, $prefix) === 0) {
            return $value;
        }
        return $prefix . $value;
    }

    protected function assertConfigValue(string $key, string $value): void
    {
        $this->assertNotSame('', $value, sprintf('Missing config value for %s.', $key));
        $this->assertStringNotContainsString('YOUR_', $value, sprintf('Placeholder value for %s.', $key));
    }

    protected function replacePathTokens(string $path): string
    {
        $matches = [];
        if (!preg_match_all('/\\{([a-z]+)\\}/', $path, $matches)) {
            return $path;
        }

        $replacements = [
            '{org}' => $this->organizationId,
            '{loc}' => $this->locationId,
        ];

        foreach ($matches[1] as $token) {
            $placeholder = '{' . $token . '}';
            if (isset($replacements[$placeholder])) {
                continue;
            }
            $replacements[$placeholder] = $this->resolveToken($token);
        }

        return strtr($path, $replacements);
    }

    protected function assertResponse2xx(array $response, string $label): void
    {
        $this->assertGreaterThanOrEqual(
            200,
            $response['status'],
            sprintf('Expected 2xx for %s, got %s', $label, $response['status'])
        );
        $this->assertLessThan(
            300,
            $response['status'],
            sprintf('Expected 2xx for %s, got %s', $label, $response['status'])
        );
    }

    protected function buildOrderNumber(string $suffix): string
    {
        return sprintf('forte-harness-%s-%s', $suffix, date('YmdHis'));
    }

    protected function defaultBillingAddress(): array
    {
        return [
            'first_name' => 'Forte',
            'last_name' => 'Harness',
            'email' => 'integration@forte.net',
            'phone' => '555-555-5555',
            'physical_address' => [
                'street_line1' => '5058 Test Street',
                'locality' => 'Testville',
                'region' => 'TX',
                'postal_code' => '75013',
            ],
        ];
    }

    protected function buildCardPayload(): array
    {
        $number = $this->requireEnvValue('FORTE_TEST_CARD_NUMBER');
        $expMonth = $this->requireEnvValue('FORTE_TEST_CARD_EXP_MONTH');
        $expYear = $this->requireEnvValue('FORTE_TEST_CARD_EXP_YEAR');
        $cvv = $this->requireEnvValue('FORTE_TEST_CARD_CVV');
        $cardType = $this->optionalEnvValue('FORTE_TEST_CARD_TYPE', 'visa');
        $name = $this->optionalEnvValue('FORTE_TEST_CARD_NAME', 'Forte Test');

        return [
            'card_type' => $cardType,
            'name_on_card' => $name,
            'account_number' => $number,
            'expire_month' => $expMonth,
            'expire_year' => $expYear,
            'card_verification_value' => $cvv,
        ];
    }

    protected function buildEcheckPayload(): array
    {
        $routing = $this->requireEnvValue('FORTE_TEST_ACH_ROUTING');
        $account = $this->requireEnvValue('FORTE_TEST_ACH_ACCOUNT');
        $accountType = $this->requireEnvValue('FORTE_TEST_ACH_ACCOUNT_TYPE');
        $secCode = $this->optionalEnvValue('FORTE_TEST_ACH_ENTRY_CLASS', 'WEB');
        $holder = $this->optionalEnvValue('FORTE_TEST_ACH_ACCOUNT_HOLDER', 'Forte Test');

        $accountType = strtoupper($accountType);
        $accountType = $accountType === 'S' ? 'savings' : 'checking';

        return [
            'account_holder' => $holder,
            'routing_number' => $routing,
            'account_number' => $account,
            'account_type' => $accountType,
            'sec_code' => $secCode,
        ];
    }

    protected function serviceFeeAmount(): ?string
    {
        $value = $this->optionalEnvValue('FORTE_TEST_SERVICE_FEE_AMOUNT', '');
        if ($value === '') {
            return null;
        }
        return $value;
    }

    protected function withServiceFee(array $payload): array
    {
        $fee = $this->serviceFeeAmount();
        if ($fee === null) {
            return $payload;
        }
        if (!array_key_exists('service_fee_amount', $payload)) {
            $payload['service_fee_amount'] = $fee;
        }
        return $payload;
    }

    protected function extractTransactionTokens($data): array
    {
        if (!is_array($data)) {
            return [];
        }

        return [
            'transaction_id' => $data['transaction_id']
                ?? $data['id']
                ?? ($data['transaction']['transaction_id'] ?? ''),
            'authorization_code' => $data['authorization_code']
                ?? ($data['response']['authorization_code'] ?? ''),
            'customer_token' => $data['customer_token']
                ?? ($data['customer']['customer_token'] ?? ''),
            'paymethod_token' => $data['paymethod_token']
                ?? ($data['paymethod']['paymethod_token'] ?? ''),
        ];
    }

    protected function requireEnvValue(string $baseKey): string
    {
        $key = $this->envKey($baseKey);
        $value = getenv($key);
        if ($value === false || $value === '') {
            $this->fail('Missing required env var: ' . $key);
        }
        return $value;
    }

    protected function optionalEnvValue(string $baseKey, string $default): string
    {
        $key = $this->envKey($baseKey);
        $value = getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }
        return $value;
    }

    protected function optionalEnvFlag(string $baseKey): bool
    {
        $value = getenv($this->envKey($baseKey));
        if ($value === false || $value === '') {
            $value = getenv($baseKey);
        }
        if ($value === false || $value === '') {
            return false;
        }
        return !in_array(strtolower((string)$value), ['0', 'false', 'no', 'off'], true);
    }

    protected function optionalJsonPayload(string $baseKey): ?array
    {
        $key = $this->envKey($baseKey);
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            $payload = $value;
            if (is_file($value)) {
                $payload = (string)file_get_contents($value);
            }

            $decoded = json_decode($payload, true);
            if (!is_array($decoded)) {
                $this->fail('Invalid JSON payload for ' . $key);
            }
            return $decoded;
        }

        $payloadsPath = getenv($this->envKey('FORTE_TEST_PAYLOADS_PATH'));
        if ($payloadsPath === false || $payloadsPath === '') {
            $payloadsPath = getenv('FORTE_TEST_PAYLOADS_PATH');
        }
        if ($payloadsPath === false || $payloadsPath === '' || !is_file($payloadsPath)) {
            return null;
        }

        static $payloadCache = [];
        if (!array_key_exists($payloadsPath, $payloadCache)) {
            $decoded = json_decode((string)file_get_contents($payloadsPath), true);
            $payloadCache[$payloadsPath] = is_array($decoded) ? $decoded : [];
        }

        $payloads = $payloadCache[$payloadsPath];
        $env = forte_env_name();
        if (isset($payloads[$env]) && is_array($payloads[$env]) && isset($payloads[$env][$baseKey])) {
            return $payloads[$env][$baseKey];
        }
        if (isset($payloads[$baseKey])) {
            return $payloads[$baseKey];
        }

        return null;
    }

    private function envKey(string $baseKey): string
    {
        $suffix = strtoupper(forte_env_name());
        return $baseKey . '_' . $suffix;
    }

    protected function resolveTokenValue(string $token): string
    {
        return $this->resolveToken($token);
    }

    private function resolveToken(string $token): string
    {
        if (isset($this->tokenCache[$token])) {
            return $this->tokenCache[$token];
        }

        $resourceMap = [
            'trn' => [
                'path' => '/organizations/{org}/locations/{loc}/transactions',
                'keys' => ['transaction_id', 'id'],
                'prefix' => 'trn_',
            ],
            'cst' => [
                'path' => '/organizations/{org}/locations/{loc}/customers',
                'keys' => ['customer_token', 'id'],
                'prefix' => 'cst_',
            ],
            'mth' => [
                'path' => '/organizations/{org}/locations/{loc}/paymethods',
                'keys' => ['paymethod_token', 'id'],
                'prefix' => 'mth_',
            ],
            'sch' => [
                'path' => '/organizations/{org}/locations/{loc}/schedules',
                'keys' => ['schedule_id', 'id'],
                'prefix' => 'sch_',
            ],
            'sci' => [
                'path' => '/organizations/{org}/locations/{loc}/scheduleitems',
                'keys' => ['scheduleitem_id', 'id'],
                'prefix' => 'sci_',
            ],
            'add' => [
                'path' => '/organizations/{org}/locations/{loc}/addresses',
                'keys' => ['address_token', 'id'],
                'prefix' => 'add_',
            ],
            'app' => [
                'path' => '/organizations/{org}/applications',
                'keys' => ['application_id', 'id'],
                'prefix' => 'app_',
            ],
            'doc' => [
                'path' => '/organizations/{org}/documents',
                'keys' => ['document_id', 'id'],
                'prefix' => 'doc_',
            ],
            'fnd' => [
                'path' => '/organizations/{org}/fundings',
                'keys' => ['funding_id', 'id'],
                'prefix' => 'fnd_',
            ],
            'dsp' => [
                'path' => '/organizations/{org}/disputes',
                'keys' => ['dispute_id', 'id'],
                'prefix' => 'dsp_',
            ],
        ];

        if (!isset($resourceMap[$token])) {
            $this->markTestSkipped(sprintf('No resolver configured for token {%s}.', $token));
        }

        $config = $resourceMap[$token];
        $path = strtr($config['path'], [
            '{org}' => $this->organizationId,
            '{loc}' => $this->locationId,
        ]);

        $response = $this->client->request('GET', $path);
        if ($response['status'] < 200 || $response['status'] >= 300) {
            $this->markTestSkipped(sprintf('Unable to resolve token {%s}; list request failed.', $token));
        }

        $results = $response['data']['results'] ?? $response['data']['result'] ?? $response['data'] ?? [];
        if (!is_array($results) || count($results) === 0) {
            $this->markTestSkipped(sprintf('No data available to resolve token {%s}.', $token));
        }

        foreach ($results as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            foreach ($config['keys'] as $key) {
                if (!empty($entry[$key])) {
                    return $this->tokenCache[$token] = (string)$entry[$key];
                }
            }
            if (!empty($entry['links']['self'])) {
                $match = [];
                if (preg_match(sprintf('/(%s[^\\/\\s]+)/', preg_quote($config['prefix'], '/')), $entry['links']['self'], $match)) {
                    return $this->tokenCache[$token] = $match[1];
                }
            }
        }

        $this->markTestSkipped(sprintf('No usable ID found to resolve token {%s}.', $token));
    }
}
