<?php

namespace App\Service\Pudu\Dto;

/**
 * Map information returned inside CleanBot detail responses.
 *
 * Used both at the top-level `data.map` and inside `data.cleanbot.clean.map`.
 */
final class CleanBotMapInfo
{
    public function __construct(
        /** Map name */
        public readonly string $name,

        /** Floor level */
        public readonly int $lv,

        /** Floor label */
        public readonly string $floor,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            lv: (int) ($data['lv'] ?? 0),
            floor: (string) ($data['floor'] ?? ''),
        );
    }
}
