<?php

namespace App\Service\Pudu\Dto;

/**
 * A single robot group returned by the GetBoundRobotGroups API.
 */
final class RobotGroup
{
    public function __construct(
        public readonly string $groupId,
        public readonly string $groupName,
        public readonly int $shopId,
        public readonly string $shopName,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            groupId: (string) ($data['group_id'] ?? ''),
            groupName: (string) ($data['group_name'] ?? ''),
            shopId: (int) ($data['shop_id'] ?? 0),
            shopName: (string) ($data['shop_name'] ?? ''),
        );
    }
}
