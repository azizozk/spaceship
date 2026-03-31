<?php

namespace App\Tests\Service\Pudu;

use App\Entity\PuduAccount;
use App\Service\Pudu\PuduApiClient;
use App\Service\Pudu\PuduApiClientFactory;
use App\Service\Pudu\PuduSignatureService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PuduApiClientFactoryTest extends TestCase
{
    private PuduApiClientFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new PuduApiClientFactory(
            new PuduSignatureService(),
            $this->createStub(HttpClientInterface::class),
            $this->createStub(EntityManagerInterface::class),
        );
    }

    public function testCreateFromAccountReturnsConfiguredClient(): void
    {
        $account = (new PuduAccount())->setApiKey('k')->setApiSecret('s')->setApiHost('h');

        self::assertInstanceOf(PuduApiClient::class, $this->factory->createFromAccount($account));
    }

    public function testEachAccountGetsSeparateClientInstance(): void
    {
        $account1 = (new PuduAccount())->setApiKey('k1')->setApiSecret('s1')->setApiHost('h1');
        $account2 = (new PuduAccount())->setApiKey('k2')->setApiSecret('s2')->setApiHost('h2');

        self::assertNotSame(
            $this->factory->createFromAccount($account1),
            $this->factory->createFromAccount($account2),
        );
    }
}
