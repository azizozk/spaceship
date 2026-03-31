<?php

namespace App\Tests\Service\Pudu;

use App\Entity\PuduAccount;
use App\Service\Pudu\PuduApiClient;
use App\Service\Pudu\PuduApiClientFactory;
use App\Service\Pudu\PuduSignatureService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PuduApiClientFactoryTest extends TestCase
{
    public function testCreateFromAccountReturnsConfiguredClient(): void
    {
        $account = new PuduAccount();
        $account->setApiKey('account-key');
        $account->setApiSecret('account-secret');
        $account->setApiHost('api.example.com');

        $factory = new PuduApiClientFactory(
            new PuduSignatureService(),
            $this->createStub(HttpClientInterface::class),
        );

        $client = $factory->createFromAccount($account);

        self::assertInstanceOf(PuduApiClient::class, $client);
    }

    public function testEachAccountGetsSeparateClientInstance(): void
    {
        $factory = new PuduApiClientFactory(
            new PuduSignatureService(),
            $this->createStub(HttpClientInterface::class),
        );

        $account1 = (new PuduAccount())->setApiKey('k1')->setApiSecret('s1')->setApiHost('h1');
        $account2 = (new PuduAccount())->setApiKey('k2')->setApiSecret('s2')->setApiHost('h2');

        $client1 = $factory->createFromAccount($account1);
        $client2 = $factory->createFromAccount($account2);

        self::assertNotSame($client1, $client2);
    }
}
