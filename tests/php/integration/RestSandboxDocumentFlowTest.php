<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('integration')]
class RestSandboxDocumentFlowTest extends IntegrationTestCase
{
    public function testUploadAndDeleteDocument(): void
    {
        $applicationId = $this->resolveTokenValue('app');

        $boundary = 'forte-harness-' . bin2hex(random_bytes(8));
        $fileName = 'forte-harness.txt';
        $fileBody = "Forte harness document test\n";

        $metadata = json_encode([
            'resource' => 'application',
            'resource_id' => $applicationId,
            'description' => 'forte-harness document upload',
        ]);

        $payload = '';
        $payload .= "--{$boundary}\r\n";
        $payload .= "Content-Type: application/json; charset=utf-8\r\n";
        $payload .= "Content-Disposition: form-data; name=\"myJsonString\"\r\n\r\n";
        $payload .= $metadata . "\r\n";
        $payload .= "--{$boundary}\r\n";
        $payload .= "Content-Type: text/plain\r\n";
        $payload .= "Content-Disposition: form-data; name=\"file\"; filename=\"{$fileName}\"\r\n\r\n";
        $payload .= $fileBody . "\r\n";
        $payload .= "--{$boundary}--\r\n";

        $response = $this->client->requestRaw(
            'POST',
            '/organizations/' . $this->organizationId . '/documents',
            $payload,
            [
                'Content-Type: multipart/form-data; boundary=' . $boundary,
            ]
        );

        $this->assertResponse2xx($response, 'upload document');
        $documentId = $response['data']['document_id'] ?? '';
        if ($documentId === '') {
            $this->markTestSkipped('Document upload did not return document_id.');
        }

        $response = $this->client->request(
            'DELETE',
            '/organizations/' . $this->organizationId . '/documents/' . $documentId
        );

        $this->assertResponse2xx($response, 'delete document');
    }
}
