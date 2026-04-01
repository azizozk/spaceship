<?php

namespace App\Service\Pudu\Dto;

/**
 * Task execution result from `data.cleanbot.clean.result`.
 *
 * Status codes:
 *   0 not started | 1 in progress | 2 paused | 3 interrupted | 4 finished | 5 abnormal | 6 canceled
 */
final class CleanBotTaskResult
{
    public function __construct(
        /** Duration in seconds */
        public readonly int $time,

        /** Planned area */
        public readonly float $area,

        /**
         * Task status:
         * 0 not started, 1 in progress, 2 paused, 3 interrupted,
         * 4 finished, 5 abnormal, 6 canceled
         */
        public readonly int $status,

        public readonly CleanBotBreakPoint $breakPoint,

        /** Completion percentage (0–100) */
        public readonly int $percentage,

        /** Remaining time in seconds */
        public readonly int $remainingTime,

        /** Actual area cleaned */
        public readonly float $taskArea,

        public readonly int $costWater,
        public readonly int $costBattery,
        public readonly int $chargeCount,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            time: (int) ($data['time'] ?? 0),
            area: (float) ($data['area'] ?? 0),
            status: (int) ($data['status'] ?? 0),
            breakPoint: CleanBotBreakPoint::fromArray($data['break_point'] ?? []),
            percentage: (int) ($data['percentage'] ?? 0),
            remainingTime: (int) ($data['remaining_time'] ?? 0),
            taskArea: (float) ($data['task_area'] ?? 0),
            costWater: (int) ($data['cost_water'] ?? 0),
            costBattery: (int) ($data['cost_battery'] ?? 0),
            chargeCount: (int) ($data['charge_count'] ?? 0),
        );
    }

    public function isInProgress(): bool
    {
        return 1 === $this->status;
    }

    public function isFinished(): bool
    {
        return 4 === $this->status;
    }

    public function isCanceled(): bool
    {
        return 6 === $this->status;
    }
}
