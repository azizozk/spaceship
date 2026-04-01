<?php

namespace App\Service\Pudu\Dto;

use App\Service\Pudu\Enum\RobotTaskState;
use App\Service\Pudu\Enum\RobotTaskType;

/**
 * A single task entry from `data.data.tasks` in the GetCurrentTaskStatus response.
 */
final class RobotTask
{
    public function __construct(
        /** Target point name */
        public readonly string $name,

        public readonly RobotTaskType $type,

        public readonly RobotTaskState $state,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            type: RobotTaskType::from((string) ($data['type'] ?? '')),
            state: RobotTaskState::from((string) ($data['state'] ?? '')),
        );
    }

    public function isOngoing(): bool
    {
        return RobotTaskState::ONGOING === $this->state;
    }

    public function isCompleted(): bool
    {
        return RobotTaskState::COMPLETE === $this->state;
    }
}
