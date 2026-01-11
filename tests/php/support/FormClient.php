<?php

namespace ForteTestHarness\Tests\Support;

final class FormClient
{
    private bool $verifySsl;

    public function __construct(bool $verifySsl)
    {
        $this->verifySsl = $verifySsl;
    }

    public function request(string $method, string $url, array $fields): array
    {
        $method = strtoupper($method);
        $query = http_build_query($fields);
        if ($method === 'GET' && $query !== '') {
            $separator = (strpos($url, '?') === false) ? '?' : '&';
            $url .= $separator . $query;
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('Unable to initialize cURL.');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: */*',
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (!$this->verifySsl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        }

        $raw = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = (string)curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL error: ' . $error);
        }

        curl_close($ch);

        $headerText = substr($raw, 0, $headerSize);
        $body = substr($raw, $headerSize);
        $headers = $this->parseHeaders($headerText);

        return [
            'status' => $status,
            'url' => $effectiveUrl,
            'headers' => $headers,
            'body' => $body,
            'raw' => $raw,
        ];
    }

    private function parseHeaders(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }
        $blocks = preg_split("/\r\n\r\n/", $raw);
        $headerBlock = $blocks ? end($blocks) : $raw;
        $lines = preg_split("/\r\n/", (string)$headerBlock);
        $headers = [];
        foreach ($lines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }
            [$key, $value] = explode(':', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if ($key === '') {
                continue;
            }
            if (isset($headers[$key])) {
                if (!is_array($headers[$key])) {
                    $headers[$key] = [$headers[$key]];
                }
                $headers[$key][] = $value;
            } else {
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}
