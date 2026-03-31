<?php

namespace App\Service\Pudu;

use App\Entity\PuduAccount;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PuduApiClientFactory
{
    public function __construct(
        private readonly PuduSignatureService $signatureService,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function createFromAccount(PuduAccount $account): PuduApiClient
    {
        return new PuduApiClient(
            $account->getApiKey(),
            $account->getApiSecret(),
            $account->getApiHost(),
            $this->signatureService,
            $this->httpClient,
        );
    }
}
