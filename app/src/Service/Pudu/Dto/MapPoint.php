<?php

namespace App\Service\Pudu\Dto;

/**
 * A single waypoint returned by the GetCurrentMap (CurrentMap) API.
 */
final class MapPoint
{
    public function __construct(
        /** Point name, used as the `point` parameter when calling InitiateCallTask */
        public readonly string $name,

        /** Point type, e.g. "table" */
        public readonly string $type,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            type: (string) ($data['type'] ?? ''),
        );
    }
}
