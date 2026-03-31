<?php

namespace App\Service\Pudu\Dto;

/**
 * Robot status and task info from `data.cleanbot`.
 */
final class CleanBotStatus
{
    public function __construct(
        /** Fresh water remaining (percentage) */
        public readonly int $rising,

        /** Sewage water remaining (percentage) */
        public readonly int $sewage,

        /** Overall task status code */
        public readonly int $task,

        /** Active cleaning task detail */
        public readonly CleanBotCleanInfo $clean,

        public readonly int $lastMode,

        /** Human-readable task detail description */
        public readonly string $detail,

        /** Last task name / ID */
        public readonly string $lastTask,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rising: (int) ($data['rising'] ?? 0),
            sewage: (int) ($data['sewage'] ?? 0),
            task: (int) ($data['task'] ?? 0),
            clean: CleanBotCleanInfo::fromArray($data['clean'] ?? []),
            lastMode: (int) ($data['last_mode'] ?? 0),
            detail: (string) ($data['detail'] ?? ''),
            lastTask: (string) ($data['last_task'] ?? ''),
        );
    }
}
