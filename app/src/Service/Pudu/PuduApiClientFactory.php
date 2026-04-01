<?php

namespace App\Service\Pudu;

use App\Entity\PuduAccount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PuduApiClientFactory
{
    public function __construct(
        private readonly PuduSignatureService $signatureService,
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createFromAccount(PuduAccount $account): PuduApiClient
    {
        $loggingClient = new LoggingHttpClient($this->httpClient, $this->entityManager, $account);

        return new PuduApiClient(
            $account->getApiKey(),
            $account->getApiSecret(),
            $account->getApiHost(),
            $this->signatureService,
            $loggingClient,
        );
    }
}
