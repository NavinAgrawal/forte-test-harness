<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\FormClient;
use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
#[Group('non-rest')]
class NonRestSandboxSmokeTest extends IntegrationTestCase
{
    private FormClient $formClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formClient = new FormClient($this->sslVerifyEnabled());
    }

    public function testSandboxEnvironment(): void
    {
        $this->assertSame('sandbox', forte_env_name(), 'FORTE_ENV must be sandbox for non-REST integration tests.');
    }

    public function testForteJsLoads(): void
    {
        $url = forte_js_url();
        $response = $this->formClient->request('GET', $url, []);
        $this->assertSame(200, $response['status'], 'Forte.js URL not reachable.');
        $this->assertNotSame('', trim((string)($response['body'] ?? '')), 'Forte.js response empty.');
    }

    public function testForteCheckoutJsLoads(): void
    {
        $url = forte_env_name() === 'sandbox'
            ? 'https://sandbox.forte.net/checkout/v2/js'
            : 'https://checkout.forte.net/checkout/v2/js';
        $response = $this->formClient->request('GET', $url, []);
        $this->assertSame(200, $response['status'], 'Forte Checkout JS not reachable.');
        $this->assertNotSame('', trim((string)($response['body'] ?? '')), 'Forte Checkout JS response empty.');
    }

    public function testForteCheckoutUtcEndpoint(): void
    {
        $url = forte_env_name() === 'sandbox'
            ? 'https://sandbox.forte.net/checkout/getUTC?callback=?'
            : 'https://checkout.forte.net/getUTC?callback=?';
        $response = $this->formClient->request('GET', $url, []);
        $this->assertSame(200, $response['status'], 'Checkout getUTC not reachable.');
        $body = (string)($response['body'] ?? '');
        $this->assertStringContainsString('(', $body, 'Checkout getUTC response malformed.');
        $this->assertStringContainsString(')', $body, 'Checkout getUTC response malformed.');
    }

    public function testRiskTagsScriptLoads(): void
    {
        $org = (string)forte_config('organization_id');
        if ($org === '' || strpos($org, 'YOUR_') !== false) {
            $this->markTestSkipped('organization_id not configured for risk tag test.');
        }

        $riskKey = $this->optionalEnvValue('FORTE_RISK_TAG_KEY', '');
        if ($riskKey === '') {
            $this->markTestSkipped('FORTE_RISK_TAG_KEY not configured for risk tag test.');
        }

        if (strpos($org, 'org_') === 0) {
            $org = substr($org, 4);
        }
        $session = uniqid('forte-', true);
        $orgEncoded = rawurlencode($org);
        $url = sprintf('https://img3.forte.net/fp/tags.js?%s=%s&session_id=%s&pageid=1', $orgEncoded, $riskKey, $session);
        $response = $this->formClient->request('GET', $url, []);
        if ($response['status'] !== 200) {
            $this->markTestSkipped('Risk tag script returned status ' . $response['status']);
        }
    }

    public function testRoutingNumberLookup(): void
    {
        $routing = $this->optionalEnvValue('FORTE_TEST_ACH_ROUTING', '');
        if ($routing === '') {
            $this->markTestSkipped('FORTE_TEST_ACH_ROUTING not set for routing lookup test.');
        }

        $url = 'https://www.routingnumbers.info/api/data.json?rn=' . rawurlencode($routing);
        $response = $this->requestWithUserAgent($url, 'ForteHarness/1.0');
        if ($response['status'] !== 200) {
            $this->markTestSkipped('Routing number API returned status ' . $response['status']);
        }

        $data = json_decode((string)($response['body'] ?? ''), true);
        if (!is_array($data)) {
            $this->markTestSkipped('Routing number API did not return JSON.');
        }
        if (isset($data['code'])) {
            $this->assertSame(200, (int)$data['code'], 'Routing number API returned non-200 code.');
        }
    }

    public function testFreshdeskApiHealth(): void
    {
        $domain = (string)forte_config('freshdesk_domain');
        $apiKey = (string)forte_config('freshdesk_api_key');
        if ($domain === '' || $apiKey === '' || strpos($domain, 'YOUR_') !== false || strpos($apiKey, 'YOUR_') !== false) {
            $this->markTestSkipped('Freshdesk credentials not configured.');
        }

        $url = 'https://' . $domain . '.freshdesk.com/api/v2/tickets?per_page=1';
        $response = $this->requestBasicAuth($url, $apiKey, 'X');
        $this->assertSame(200, $response['status'], 'Freshdesk API not reachable.');
    }

    public function testHtml2PdfRocket(): void
    {
        $apiKey = (string)forte_config('html2pdf_api_key');
        if ($apiKey === '' || strpos($apiKey, 'YOUR_') !== false) {
            $this->markTestSkipped('HTML2PDF API key not configured.');
        }

        $url = 'http://api.html2pdfrocket.com/pdf';
        $payload = [
            'apikey' => $apiKey,
            'value' => 'https://example.com',
        ];

        $response = $this->formClient->request('POST', $url, $payload);
        $this->assertSame(200, $response['status'], 'HTML2PDF Rocket not reachable.');
        $body = (string)($response['body'] ?? '');
        $this->assertStringContainsString('%PDF', $body, 'HTML2PDF Rocket did not return a PDF response.');
    }

    public function testWebhookReceiver(): void
    {
        $webhookUrl = $this->optionalEnvValue('FORTE_WEBHOOK_URL', '');
        if ($webhookUrl === '') {
            $this->markTestSkipped('FORTE_WEBHOOK_URL not configured for webhook test.');
        }

        $payload = [
            'event' => 'transaction',
            'transaction' => [
                'organization_id' => $this->organizationId,
                'location_id' => $this->locationId,
                'authorization_amount' => 0.01,
                'order_number' => $this->buildOrderNumber('webhook'),
            ],
        ];
        $response = $this->requestJson($webhookUrl, $payload);
        $this->assertSame(200, $response['status'], 'Webhook endpoint did not return 200.');
    }

    public function testImporterSampleFile(): void
    {
        $path = $this->optionalEnvValue('FORTE_IMPORTER_SAMPLE_CSV', '');
        if ($path === '') {
            $this->markTestSkipped('FORTE_IMPORTER_SAMPLE_CSV not configured for importer test.');
        }
        if (!is_file($path)) {
            $this->fail('Importer sample CSV not found: ' . $path);
        }
        $contents = trim((string)file_get_contents($path));
        $this->assertNotSame('', $contents, 'Importer sample CSV is empty.');
    }

    private function requestBasicAuth(string $url, string $user, string $password): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('Unable to initialize cURL.');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $password);
        if (!$this->sslVerifyEnabled()) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $raw = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL error: ' . $error);
        }

        curl_close($ch);

        return [
            'status' => $status,
            'body' => $raw,
        ];
    }

    private function requestJson(string $url, array $payload): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('Unable to initialize cURL.');
        }

        $body = json_encode($payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        if (!$this->sslVerifyEnabled()) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $raw = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL error: ' . $error);
        }

        curl_close($ch);

        return [
            'status' => $status,
            'body' => $raw,
        ];
    }

    private function requestWithUserAgent(string $url, string $userAgent): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('Unable to initialize cURL.');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (!$this->sslVerifyEnabled()) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $raw = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL error: ' . $error);
        }

        curl_close($ch);

        return [
            'status' => $status,
            'body' => $raw,
        ];
    }
}
