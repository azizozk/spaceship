<?php

namespace App\Service\Pudu\Dto;

/**
 * Task definition from `data.cleanbot.clean.task`.
 */
final class CleanBotTaskDef
{
    public function __construct(
        /** Task ID (large integer, stored as string to avoid precision loss) */
        public readonly string $taskId,

        /**
         * Task version; the API documents this as float but the example value
         * (1693571382985) is a Unix timestamp in milliseconds.
         */
        public readonly int $version,

        public readonly string $name,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            taskId: (string) ($data['task_id'] ?? ''),
            version: (int) ($data['version'] ?? 0),
            name: (string) ($data['name'] ?? ''),
        );
    }
}
