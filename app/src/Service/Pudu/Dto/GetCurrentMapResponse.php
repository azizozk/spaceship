<?php

namespace App\Service\Pudu\Dto;

/**
 * Typed response from the GetCurrentMap (CurrentMap) API.
 *
 * Contains the current map name for the robot and a paginated list of its waypoints.
 */
final class GetCurrentMapResponse
{
    /**
     * @param MapPoint[] $points
     */
    public function __construct(
        /** Current map name loaded on the robot */
        public readonly string $mapName,

        /** Total number of points on the map (for pagination) */
        public readonly int $total,

        /** Points returned for this page */
        public readonly array $points,
    ) {
    }

    /**
     * Constructs from the raw `data` object inside the API response body.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $points = array_map(
            static fn(array $item): MapPoint => MapPoint::fromArray($item),
            $data['list'] ?? [],
        );

        return new self(
            mapName: (string) ($data['map_name'] ?? ''),
            total: (int) ($data['total'] ?? count($points)),
            points: $points,
        );
    }

    public function hasMorePages(int $offset, int $limit): bool
    {
        return ($offset + $limit) < $this->total;
    }
}
