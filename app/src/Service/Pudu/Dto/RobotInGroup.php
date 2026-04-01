<?php

namespace App\Service\Pudu\Dto;

/**
 * A single robot entry returned by the GetRobotsInGroup API.
 */
final class RobotInGroup
{
    public function __construct(
        public readonly string $mac,
        public readonly string $sn,
        public readonly string $robotName,
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
            mac: (string) ($data['mac'] ?? ''),
            sn: (string) ($data['sn'] ?? ''),
            robotName: (string) ($data['robot_name'] ?? ''),
            shopId: (int) ($data['shop_id'] ?? 0),
            shopName: (string) ($data['shop_name'] ?? ''),
        );
    }
}
