<?php

namespace App\Service\Pudu\Dto;

/**
 * Immediate dispatch result for the next custom call task.
 * Populated in CompleteCallTaskResponse when next_call_task was provided.
 */
final class NextCallResult
{
    public function __construct(
        /** "SUCCESS" if the next task was dispatched successfully */
        public readonly string $message,

        /** Task ID of the newly dispatched next task */
        public readonly string $taskId,
    ) {
    }

    /**
     * @param array<string, mixed> $data The next_call_result object from the API response
     */
    public static function fromArray(array $data): self
    {
        return new self(
            message: (string) ($data['message'] ?? ''),
            taskId:  (string) ($data['task_id'] ?? ''),
        );
    }

    public function isSuccess(): bool
    {
        return 'SUCCESS' === $this->message;
    }
}
