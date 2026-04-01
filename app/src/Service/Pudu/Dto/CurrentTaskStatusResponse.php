<?php

namespace App\Service\Pudu\Dto;

/**
 * Typed response from the GetCurrentTaskStatus API (FlashBot legacy interface).
 *
 * The API wraps the robot's own response inside a nested `data.data` structure:
 *   response.data.code    — robot return code (0 = success)
 *   response.data.message — robot return message
 *   response.data.data.tasks — the actual task list
 */
final class CurrentTaskStatusResponse
{
    /**
     * @param RobotTask[] $tasks
     */
    public function __construct(
        /** Robot return code; 0 indicates success */
        public readonly int $robotCode,

        /** Robot return message */
        public readonly string $robotMessage,

        /** Tasks currently being executed by the robot */
        public readonly array $tasks,
    ) {
    }

    /**
     * Constructs from the raw `data` object inside the API response body.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $tasks = array_map(
            static fn(array $item): RobotTask => RobotTask::fromArray($item),
            $data['data']['tasks'] ?? [],
        );

        return new self(
            robotCode: (int) ($data['code'] ?? 0),
            robotMessage: (string) ($data['message'] ?? ''),
            tasks: $tasks,
        );
    }

    public function isRobotSuccess(): bool
    {
        return 0 === $this->robotCode;
    }
}
