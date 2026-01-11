<?php

namespace ForteTestHarness\Tests\Support;

final class SoapClient
{
    private bool $verifySsl;

    public function __construct(bool $verifySsl)
    {
        $this->verifySsl = $verifySsl;
    }

    public function request(string $url, string $body, ?string $action = null): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('Unable to initialize cURL.');
        }

        $headers = [
            'Content-Type: text/xml; charset=utf-8',
            'Accept: text/xml',
        ];
        if ($action) {
            $headers[] = 'SOAPAction: ' . $action;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        if (!$this->verifySsl) {
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
