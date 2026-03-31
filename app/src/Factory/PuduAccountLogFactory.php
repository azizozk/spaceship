<?php

namespace App\Factory;

use App\Entity\PuduAccountLog;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PuduAccountLog>
 */
final class PuduAccountLogFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    // public function __construct()
    // {
    // }

    #[\Override]
    public static function class(): string
    {
        return PuduAccountLog::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'executedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'method' => self::faker()->text(8),
            'puduAccount' => PuduAccountFactory::new(),
            'uri' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(PuduAccountLog $puduAccountLog): void {})
        ;
    }
}
