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
            'apiHost' => self::faker()->text(255),
            'apiKey' => self::faker()->text(255),
            'apiSecret' => self::faker()->text(255),
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
