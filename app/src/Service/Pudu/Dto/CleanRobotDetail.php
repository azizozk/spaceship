<?php

namespace App\Service\Pudu\Dto;

/**
 * Typed response from the GetCleanRobotStatusDetail API (`data` field).
 *
 * Supported models: CC1, CC1 Pro, MT1, MT1 Vac, MT1 Max.
 */
final class CleanRobotDetail
{
    public function __construct(
        public readonly string $sn,

        /** Device MAC address */
        public readonly string $mac,

        /** Robot nickname */
        public readonly string $nickname,

        /** Battery level (0–100) */
        public readonly int $battery,

        /** Current map loaded on the robot */
        public readonly CleanBotMapInfo $map,

        /** Robot status and task information */
        public readonly CleanBotStatus $cleanbot,

        /** Shop the robot belongs to */
        public readonly CleanBotShop $shop,

        /** Current robot position */
        public readonly CleanBotPosition $position,
    ) {
    }

    /**
     * Constructs from the raw `data` object inside the API response body.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sn: (string) ($data['sn'] ?? ''),
            mac: (string) ($data['mac'] ?? ''),
            nickname: (string) ($data['nickname'] ?? ''),
            battery: (int) ($data['battery'] ?? 0),
            map: CleanBotMapInfo::fromArray($data['map'] ?? []),
            cleanbot: CleanBotStatus::fromArray($data['cleanbot'] ?? []),
            shop: CleanBotShop::fromArray($data['shop'] ?? []),
            position: CleanBotPosition::fromArray($data['position'] ?? []),
        );
    }
}
