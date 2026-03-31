<?php

namespace App\Service\Pudu\Dto;

/**
 * Cleaning configuration from `data.cleanbot.clean.config`.
 */
final class CleanBotConfig
{
    public function __construct(
        public readonly int $mode,
        public readonly int $vacuumSpeed,
        public readonly int $vacuumSuction,
        public readonly int $washSpeed,
        public readonly int $washSuction,
        public readonly int $washWater,

        /** 0 custom, 1 carpet, 2 silent */
        public readonly int $type,

        /** Left brush (Monster): 0 off, 1 low, 2 medium, 3 high */
        public readonly ?int $leftBrush,

        /** Right brush (Monster & CC1): 0 off, 1 low, 2 medium, 3 high */
        public readonly ?int $rightBrush,

        /** Right suction: 0 off, 1 low, 2 medium, 3 high */
        public readonly ?int $rightVacuumSuction,

        public readonly ?bool $aiAdaptiveSwitch,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            mode: (int) ($data['mode'] ?? 0),
            vacuumSpeed: (int) ($data['vacuum_speed'] ?? 0),
            vacuumSuction: (int) ($data['vacuum_suction'] ?? 0),
            washSpeed: (int) ($data['wash_speed'] ?? 0),
            washSuction: (int) ($data['wash_suction'] ?? 0),
            washWater: (int) ($data['wash_water'] ?? 0),
            type: (int) ($data['type'] ?? 0),
            leftBrush: isset($data['left_brush']) ? (int) $data['left_brush'] : null,
            rightBrush: isset($data['right_brush']) ? (int) $data['right_brush'] : null,
            rightVacuumSuction: isset($data['right_vacuum_suction']) ? (int) $data['right_vacuum_suction'] : null,
            aiAdaptiveSwitch: isset($data['ai_adaptive_switch']) ? (bool) $data['ai_adaptive_switch'] : null,
        );
    }
}
