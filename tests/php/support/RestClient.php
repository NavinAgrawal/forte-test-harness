<?php

namespace ForteTestHarness\Tests\Support;

final class RestClient
{
    private string $baseUrl;
    private string $organizationId;
    private string $authToken;
    private bool $verifySsl;

    public function __construct(string $baseUrl, string $organizationId, string $apiAccessId, string $apiSecureKey, bool $verifySsl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->organizationId = $organizationId;
        $this->authToken = base64_encode($apiAccessId . ':' . $apiSecureKey);
        $this->verifySsl = $verifySsl;
    }

    public function request(string $method, string $path, ?array $body = null, array $headers = []): array
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('Unable to initialize cURL.');
        }

        $defaultHeaders = [
            'Authorization: Basic ' . $this->authToken,
            'X-Forte-Auth-Organization-Id: ' . $this->organizationId,
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));

        if (!$this->verifySsl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if ($body !== null) {
            $payload = json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
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
            'raw' => $raw,
            'data' => json_decode((string)$raw, true),
        ];
    }

    public function requestRaw(string $method, string $path, string $body, array $headers = []): array
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('Unable to initialize cURL.');
        }

        $defaultHeaders = [
            'Authorization: Basic ' . $this->authToken,
            'X-Forte-Auth-Organization-Id: ' . $this->organizationId,
            'Accept: application/json',
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));

        if (!$this->verifySsl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

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
            'raw' => $raw,
            'data' => json_decode((string)$raw, true),
        ];
    }
}
