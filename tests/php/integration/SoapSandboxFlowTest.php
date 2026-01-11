<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use ForteTestHarness\Tests\Support\SoapClient;

/**
 * @group integration
 * @group non-rest
 * @group soap
 */
class SoapSandboxFlowTest extends IntegrationTestCase
{
    private SoapClient $soapClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->soapClient = new SoapClient($this->sslVerifyEnabled());
    }

    public function testSandboxEnvironment(): void
    {
        $this->assertSame('sandbox', forte_env_name(), 'FORTE_ENV must be sandbox for SOAP integration tests.');
    }

    public function testTransactionSoapRequest(): void
    {
        $loginId = (string)forte_config('api_login_id');
        if ($this->isPlaceholder($loginId)) {
            $this->markTestSkipped('SOAP credentials not configured (api_login_id).');
        }
        $this->assertConfigValue('api_login_id', $loginId);

        $merchantIds = (array)forte_config('merchant_ids', []);
        $merchantId = $merchantIds['soap_hash'] ?? forte_config('pg_merchant_id');
        if ($this->isPlaceholder((string)$merchantId)) {
            $this->markTestSkipped('SOAP credentials not configured (pg_merchant_id).');
        }
        $this->assertConfigValue('pg_merchant_id', (string)$merchantId);

        $secureKeys = (array)forte_config('secure_transaction_keys', []);
        $secureKey = $secureKeys['soap_hash'] ?? forte_config('secure_transaction_key');
        if ($this->isPlaceholder((string)$secureKey)) {
            $this->markTestSkipped('SOAP credentials not configured (secure_transaction_key).');
        }
        $this->assertConfigValue('secure_transaction_key', (string)$secureKey);

        $projectPath = __DIR__ . '/../../soap-projects/sandbox/Transaction-soapui-project.xml';
        if (!is_file($projectPath)) {
            $this->markTestSkipped('SOAP project file missing: ' . $projectPath);
        }
        $xml = (string)file_get_contents($projectPath);
        $endpoint = $this->extractSoapEndpoint($xml);
        $action = $this->extractSoapAction($xml);
        $request = $this->extractSoapRequest($xml);

        $this->assertNotSame('', $endpoint, 'Missing SOAP endpoint in soapui project.');
        $this->assertNotSame('', $request, 'Missing SOAP request template in soapui project.');

        $utc = $this->utcTicks();
        $hash = hash_hmac('md5', $loginId . '|' . $utc, (string)$secureKey);
        $request = $this->fillSoapRequest($request, $loginId, (string)$merchantId, $hash, $utc);

        $response = $this->soapClient->request($endpoint, $request, $action);
        $this->assertSoapResponse($response, 'SOAP transaction service');
    }

    private function extractSoapEndpoint(string $xml): string
    {
        if (preg_match('/<con:endpoint>([^<]+)<\/con:endpoint>/i', $xml, $match)) {
            return trim($match[1]);
        }
        return '';
    }

    private function extractSoapAction(string $xml): ?string
    {
        if (preg_match('/<con:operation[^>]*action="([^"]+)"/i', $xml, $match)) {
            return trim($match[1]);
        }
        return null;
    }

    private function extractSoapRequest(string $xml): string
    {
        if (!preg_match_all('/<con:request><!\[CDATA\[(.*?)\]\]><\/con:request>/s', $xml, $matches)) {
            return '';
        }

        $best = '';
        $bestScore = PHP_INT_MAX;
        foreach ($matches[1] as $candidate) {
            if (stripos($candidate, 'APILoginID') === false) {
                continue;
            }
            if (stripos($candidate, 'MerchantID') === false && stripos($candidate, 'MerchantIDs') === false) {
                continue;
            }
            $score = substr_count($candidate, '?>') + substr_count($candidate, '>?</');
            if ($score < $bestScore) {
                $bestScore = $score;
                $best = $candidate;
            }
        }

        if ($best !== '') {
            return $best;
        }

        return $matches[1][0] ?? '';
    }

    private function fillSoapRequest(string $xml, string $loginId, string $merchantId, string $hash, string $utc): string
    {
        $out = $xml;
        $out = preg_replace('/<v1:APILoginID>[^<]*<\/v1:APILoginID>/', '<v1:APILoginID>' . $loginId . '</v1:APILoginID>', $out);
        $out = preg_replace('/<v1:TSHash>[^<]*<\/v1:TSHash>/', '<v1:TSHash>' . $hash . '</v1:TSHash>', $out);
        $out = preg_replace('/<v1:UTCTime>[^<]*<\/v1:UTCTime>/', '<v1:UTCTime>' . $utc . '</v1:UTCTime>', $out);
        $out = preg_replace('/<v1:MerchantID>[^<]*<\/v1:MerchantID>/', '<v1:MerchantID>' . $merchantId . '</v1:MerchantID>', $out);
        $out = preg_replace('/<v1:MerchantIDs>[^<]*<\/v1:MerchantIDs>/', '<v1:MerchantIDs>' . $merchantId . '</v1:MerchantIDs>', $out);
        $out = str_replace('YOUR_API_LOGIN_ID', $loginId, $out);

        $out = preg_replace_callback('/<v1:([A-Za-z0-9_]+)>\?<\/v1:\1>/', function ($match) {
            $tag = $match[1];
            if (in_array($tag, ['StartDate', 'EndDate', 'Day'], true)) {
                return '<v1:' . $tag . '>2020-01-01</v1:' . $tag . '>';
            }
            if ($tag === 'PageIndex') {
                return '<v1:PageIndex>0</v1:PageIndex>';
            }
            if ($tag === 'PageSize') {
                return '<v1:PageSize>50</v1:PageSize>';
            }
            return '<v1:' . $tag . '>0</v1:' . $tag . '>';
        }, $out);

        return $out;
    }

    private function utcTicks(): string
    {
        $ticks = (int)(microtime(true) * 10000) + 621355968000000000;
        return (string)$ticks;
    }

    private function assertSoapResponse(array $response, string $label): void
    {
        $this->assertSame(200, $response['status'], sprintf('%s returned %s', $label, $response['status']));
        $body = strtolower((string)($response['body'] ?? ''));
        $this->assertStringNotContainsString('<fault', $body, $label . ' returned SOAP fault.');
        foreach (['invalid login', 'invalid api', 'invalid apiloginid', 'invalid tshash', 'unauthorized', 'security'] as $token) {
            $this->assertStringNotContainsString($token, $body, sprintf('%s error detected: %s', $label, $token));
        }
        $this->assertNotSame('', trim((string)($response['body'] ?? '')), sprintf('%s returned empty body.', $label));
    }

    private function isPlaceholder(string $value): bool
    {
        return $value === '' || strpos($value, 'YOUR_') !== false;
    }
}
