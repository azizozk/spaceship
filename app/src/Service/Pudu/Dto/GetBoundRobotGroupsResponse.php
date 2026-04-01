<?php

namespace App\Service\Pudu\Dto;

/**
 * Typed response from the GetBoundRobotGroups API.
 *
 * Contains the list of robot groups bound to a shop or microservice device.
 */
final class GetBoundRobotGroupsResponse
{
    /**
     * @param RobotGroup[] $groups
     */
    public function __construct(
        public readonly array $groups,
    ) {
    }

    /**
     * Constructs from the raw `data` object inside the API response body.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $groups = array_map(
            static fn(array $item): RobotGroup => RobotGroup::fromArray($item),
            $data['groups'] ?? [],
        );

        return new self(groups: $groups);
    }
}
