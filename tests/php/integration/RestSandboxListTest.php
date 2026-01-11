<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxListTest extends IntegrationTestCase
{
    private const CASES_PATH = __DIR__ . '/../integration/rest_sandbox_cases.json';

    public function testSandboxEnvironment(): void
    {
        $this->assertSame('sandbox', forte_env_name(), 'FORTE_ENV must be sandbox for integration tests.');
        $this->assertStringContainsString('sandbox', $this->baseUrl, 'Base URL should point to sandbox.');
    }

    /**
     * @dataProvider restCases
     * @group integration
     */
    public function testRestListEndpoints(array $case): void
    {
        $method = strtoupper($case['method'] ?? 'GET');
        $path = $this->replacePathTokens((string)($case['path'] ?? ''));
        $label = (string)($case['name'] ?? $path);

        $response = $this->client->request($method, $path, $case['body'] ?? null);

        $this->assertGreaterThanOrEqual(
            200,
            $response['status'],
            sprintf('Expected 2xx for %s %s, got %s', $label, $path, $response['status'])
        );
        $this->assertLessThan(
            300,
            $response['status'],
            sprintf('Expected 2xx for %s %s, got %s', $label, $path, $response['status'])
        );
    }

    public static function restCases(): array
    {
        $casesPath = self::CASES_PATH;
        if (!is_file($casesPath)) {
            throw new \RuntimeException('Missing integration cases file: ' . $casesPath);
        }

        $data = json_decode((string)file_get_contents($casesPath), true);
        if (!is_array($data) || !isset($data['rest']) || !is_array($data['rest'])) {
            throw new \RuntimeException('Invalid integration cases file: ' . $casesPath);
        }

        return array_map(static fn ($case) => [$case], $data['rest']);
    }

}
