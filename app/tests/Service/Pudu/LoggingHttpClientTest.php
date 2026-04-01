<?php

namespace App\Tests\Service\Pudu;

use App\Entity\PuduAccount;
use App\Entity\PuduAccountLog;
use App\Service\Pudu\LoggingHttpClient;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class LoggingHttpClientTest extends TestCase
{
    private HttpClientInterface&MockObject $inner;
    private EntityManagerInterface&MockObject $entityManager;
    private PuduAccount $account;
    private LoggingHttpClient $client;

    protected function setUp(): void
    {
        $this->inner = $this->createMock(HttpClientInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->account = (new PuduAccount())
            ->setApiKey('key')
            ->setApiSecret('secret')
            ->setApiHost('api.example.com');

        $this->client = new LoggingHttpClient($this->inner, $this->entityManager, $this->account);
    }

    public function testRequestPersistsLogWithMethodAndUri(): void
    {
        $response = $this->stubResponse(200, ['foo' => 'bar']);

        $this->inner->expects(self::once())
            ->method('request')
            ->with('GET', 'https://api.example.com/path', [])
            ->willReturn($response);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with(self::callback(function (PuduAccountLog $log): bool {
                self::assertSame('GET', $log->getMethod());
                self::assertSame('https://api.example.com/path', $log->getUri());
                self::assertSame($this->account, $log->getPuduAccount());
                self::assertNull($log->getBody());

                return true;
            }));
        $this->entityManager->expects(self::once())->method('flush');

        $this->client->request('GET', 'https://api.example.com/path');
    }

    public function testRequestDecodesJsonBodyIntoArray(): void
    {
        $response = $this->stubResponse(200, []);

        $this->inner->expects(self::once())->method('request')->willReturn($response);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with(self::callback(function (PuduAccountLog $log): bool {
                self::assertSame(['foo' => 'bar'], $log->getBody());

                return true;
            }));
        $this->entityManager->expects(self::once())->method('flush');

        $this->client->request('POST', 'https://api.example.com/path', [
            'body' => '{"foo":"bar"}',
        ]);
    }

    public function testRequestLogsResponseCodeAndBody(): void
    {
        $response = $this->stubResponse(201, ['id' => 42]);

        $this->inner->expects(self::once())->method('request')->willReturn($response);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with(self::callback(function (PuduAccountLog $log): bool {
                self::assertSame(201, $log->getResponseCode());
                self::assertSame(['id' => 42], $log->getResponseBody());

                return true;
            }));
        $this->entityManager->expects(self::once())->method('flush');

        $this->client->request('POST', 'https://api.example.com/path');
    }

    public function testRequestReturnsOriginalResponse(): void
    {
        $response = $this->stubResponse(200, []);

        $this->inner->expects(self::once())->method('request')->willReturn($response);
        $this->entityManager->expects(self::once())->method('persist');
        $this->entityManager->expects(self::once())->method('flush');

        self::assertSame($response, $this->client->request('GET', 'https://api.example.com/path'));
    }

    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testRequestStillReturnsResponseWhenLoggingFails(): void
    {
        $response = $this->stubResponse(200, []);

        // toArray throws during logging — should not propagate
        $faultyResponse = $this->createMock(ResponseInterface::class);
        $faultyResponse->method('getStatusCode')->willReturn(500);
        $faultyResponse->method('toArray')->willThrowException(new \RuntimeException('server error'));

        $this->inner->expects(self::once())->method('request')->willReturn($faultyResponse);
        $this->entityManager->expects(self::once())->method('persist');
        $this->entityManager->expects(self::once())->method('flush');

        $result = $this->client->request('GET', 'https://api.example.com/path');

        self::assertSame($faultyResponse, $result);
    }

    // -------------------------------------------------------------------------

    private function stubResponse(int $statusCode, array $body): ResponseInterface
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('toArray')->willReturn($body);

        return $response;
    }
}
