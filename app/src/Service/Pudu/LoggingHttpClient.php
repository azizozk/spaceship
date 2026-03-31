<?php

namespace App\Service\Pudu;

use App\Entity\PuduAccount;
use App\Entity\PuduAccountLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class LoggingHttpClient implements HttpClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $inner,
        private readonly EntityManagerInterface $entityManager,
        private readonly PuduAccount $account,
    ) {
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $body = null;
        if (isset($options['body']) && \is_string($options['body'])) {
            $decoded = json_decode($options['body'], true);
            $body = json_last_error() === \JSON_ERROR_NONE ? $decoded : null;
        }

        $response = $this->inner->request($method, $url, $options);

        $log = new PuduAccountLog();
        $log->setPuduAccount($this->account);
        $log->setMethod($method);
        $log->setUri($url);
        $log->setBody($body);
        $log->setExecutedAt(new \DateTimeImmutable());

        try {
            $log->setResponseCode($response->getStatusCode());
            $log->setResponseBody($response->toArray(false));
        } catch (\Throwable) {
            // best-effort: don't let logging break the request
        }

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $response;
    }

    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->inner->stream($responses, $timeout);
    }

    public function withOptions(array $options): static
    {
        return new static($this->inner->withOptions($options), $this->entityManager, $this->account);
    }
}
