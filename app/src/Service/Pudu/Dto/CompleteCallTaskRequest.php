<?php

namespace App\Service\Pudu\Dto;

/**
 * Request payload for the CompleteCallTask API.
 *
 * Used when the task is NOT in auto-completion mode.
 * The completing call must use the same APPKEY that initiated the task.
 */
final class CompleteCallTaskRequest
{
    /**
     * @param string           $taskId      Task ID returned by InitiateCallTask (required)
     * @param NextCallTask|null $nextCallTask Optional next task to dispatch immediately after completion
     */
    public function __construct(
        public readonly string $taskId,
        public readonly ?NextCallTask $nextCallTask = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $body = ['task_id' => $this->taskId];

        if (null !== $this->nextCallTask) {
            $body['next_call_task'] = $this->nextCallTask->toArray();
        }

        return $body;
    }
}
