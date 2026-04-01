<?php

namespace App\Service\Pudu\Dto;

/**
 * Typed response from the GetRobotsInGroup API.
 *
 * Contains the list of robots belonging to a robot group.
 */
final class GetRobotsInGroupResponse
{
    /**
     * @param RobotInGroup[] $robots
     */
    public function __construct(
        public readonly array $robots,
    ) {
    }

    /**
     * Constructs from the raw `data` object inside the API response body.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $robots = array_map(
            static fn(array $item): RobotInGroup => RobotInGroup::fromArray($item),
            $data['robots'] ?? [],
        );

        return new self(robots: $robots);
    }
}
