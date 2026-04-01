<?php

namespace App\Factory;

use App\Entity\PuduAccount;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PuduAccount>
 */
final class PuduAccountFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return PuduAccount::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'apiHost' => self::faker()->domainName(),
            'apiKey' => self::faker()->uuid(),
            'apiSecret' => self::faker()->sha256(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(PuduAccount $puduAccount): void {})
        ;
    }
}
