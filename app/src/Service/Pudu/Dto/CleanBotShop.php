<?php

namespace App\Service\Pudu\Dto;

/**
 * Shop information returned inside CleanBot detail responses.
 */
final class CleanBotShop
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
        );
    }
}
