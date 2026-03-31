<?php

namespace App\Service\Pudu\Dto;

/**
 * 3-D position vector used in CleanBot responses (robot position, breakpoint vectors, etc.).
 */
final class CleanBotPosition
{
    public function __construct(
        public readonly float $x,
        public readonly float $y,
        public readonly float $z,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            x: (float) ($data['x'] ?? 0),
            y: (float) ($data['y'] ?? 0),
            z: (float) ($data['z'] ?? 0),
        );
    }
}
