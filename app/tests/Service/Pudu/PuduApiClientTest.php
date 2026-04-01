<?php

namespace App\Tests\Service\Pudu;

use App\Exception\PuduApiException;
use App\Service\Pudu\PuduApiClient;
use App\Service\Pudu\PuduSignatureService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PuduApiClientTest extends TestCase
{
    private HttpClientInterface&MockObject $httpClient;
    private PuduApiClient $client;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);

        $this->client = new PuduApiClient(
            'test-api-key',
            'test-api-secret',
            'api.pudu.io',
            new PuduSignatureService(),
            $this->httpClient,
        );
    }

    public function testGetSendsSignedRequestAndReturnsArray(): void
    {
        $response = $this->mockResponse(['message' => 'SUCCESS', 'data' => ['ok' => true]]);

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                self::stringContains('https://api.pudu.io/some/path'),
                self::callback(function (array $options): bool {
                    $headers = $options['headers'];
                    self::assertArrayHasKey('Authorization', $headers);
                    self::assertStringStartsWith('hmac id="test-api-key"', $headers['Authorization']);
                    self::assertArrayHasKey('x-date', $headers);

                    return true;
                }),
            )
            ->willReturn($response);

        $result = $this->client->get('/some/path');

        self::assertSame(['message' => 'SUCCESS', 'data' => ['ok' => true]], $result);
    }

    public function testGetAppendsQueryStringToUrl(): void
    {
        $response = $this->mockResponse([]);

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->with('GET', self::stringContains('sn='), self::anything())
            ->willReturn($response);

        $this->client->get('/path', ['sn' => 'ROBOT-1']);
    }

    public function testPostSendsSignedRequestWithBody(): void
    {
        $response = $this->mockResponse(['message' => 'SUCCESS', 'data' => []]);

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'POST',
                self::stringContains('https://api.pudu.io'),
                self::callback(function (array $options): bool {
                    self::assertArrayHasKey('body', $options);
                    self::assertJson($options['body']);
                    $headers = $options['headers'];
                    self::assertArrayHasKey('Content-MD5', $headers);
                    self::assertStringStartsWith('hmac id="test-api-key"', $headers['Authorization']);

                    return true;
                }),
            )
            ->willReturn($response);

        $result = $this->client->post('/some/path', ['key' => 'value']);

        self::assertSame(['message' => 'SUCCESS', 'data' => []], $result);
    }

    public function testGetThrowsPuduApiExceptionOnHttpFailure(): void
    {
        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willThrowException(new \RuntimeException('connection refused'));

        $this->expectException(PuduApiException::class);
        $this->expectExceptionMessageMatches('/Pudu GET .* failed/');

        $this->client->get('/path');
    }

    public function testPostThrowsPuduApiExceptionOnHttpFailure(): void
    {
        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willThrowException(new \RuntimeException('timeout'));

        $this->expectException(PuduApiException::class);
        $this->expectExceptionMessageMatches('/Pudu POST .* failed/');

        $this->client->post('/path', []);
    }

    // -------------------------------------------------------------------------

    private function mockResponse(array $data): ResponseInterface
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('toArray')->willReturn($data);

        return $response;
    }
}
