<?php

namespace App\Service\Pudu\Dto;

use App\Service\Pudu\Enum\CallTaskState;

/**
 * Typed response from the InitiateCallTask API.
 */
final class InitiateCallTaskResponse
{
    public function __construct(
        /** Task ID — cache this to cancel, complete, or track via callbacks */
        public readonly string $taskId,

        /** Current task state immediately after dispatch */
        public readonly CallTaskState $state,

        /**
         * Queue position when state=QUEUEING; null otherwise.
         * Monitor callbacks or poll to know when the robot dequeues.
         */
        public readonly ?int $queue,

        /** Human-readable remark from the server */
        public readonly ?string $remark,
    ) {
    }

    /**
     * Constructs a response from the raw API array (the "data" key of the response body).
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            taskId: (string) $data['task_id'],
            state: CallTaskState::from((string) $data['state']),
            queue: isset($data['queue']) ? (int) $data['queue'] : null,
            remark: isset($data['remark']) ? (string) $data['remark'] : null,
        );
    }

    public function isQueued(): bool
    {
        return CallTaskState::QUEUEING === $this->state;
    }

    public function isCalling(): bool
    {
        return CallTaskState::CALLING === $this->state;
    }

    public function hasFailed(): bool
    {
        return CallTaskState::CALL_FAILED === $this->state;
    }
}
