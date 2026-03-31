<?php

namespace App\Service\Pudu\Dto;

use App\Service\Pudu\Enum\CallTaskState;

/**
 * Typed representation of the `data` payload inside a notifyCustomCall callback.
 */
final class CallStatusCallbackData
{
    public function __construct(
        /** Task ID */
        public readonly string $taskId,

        /** Shop ID */
        public readonly int $shopId,

        /** Map name */
        public readonly string $mapName,

        /** Target point name */
        public readonly string $point,

        /** Current call task state */
        public readonly CallTaskState $state,

        /** Robot serial number */
        public readonly string $sn,

        /** Point type (e.g. "table") */
        public readonly ?string $pointType,

        /**
         * Queue position; only meaningful when state = QUEUEING.
         */
        public readonly ?int $queue,

        /** Robot response code when the message is a robot reply */
        public readonly ?int $robotResponseCode,

        /** Robot response message when the message is a robot reply */
        public readonly ?string $robotResponseMessage,
    ) {
    }

    /**
     * Constructs from the raw `data` array received in the callback body.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            taskId: (string) $data['task_id'],
            shopId: (int) $data['shop_id'],
            mapName: (string) $data['map_name'],
            point: (string) $data['point'],
            state: CallTaskState::from((string) $data['state']),
            sn: (string) $data['sn'],
            pointType: isset($data['point_type']) ? (string) $data['point_type'] : null,
            queue: isset($data['queue']) ? (int) $data['queue'] : null,
            robotResponseCode: isset($data['robot_response_code']) ? (int) $data['robot_response_code'] : null,
            robotResponseMessage: isset($data['robot_response_message']) ? (string) $data['robot_response_message'] : null,
        );
    }

    public function isQueued(): bool
    {
        return CallTaskState::QUEUEING === $this->state;
    }

    public function isSuccessful(): bool
    {
        return CallTaskState::CALL_SUCCESS === $this->state;
    }

    public function isCompleted(): bool
    {
        return CallTaskState::CALL_COMPLETE === $this->state;
    }

    public function hasFailed(): bool
    {
        return CallTaskState::CALL_FAILED === $this->state;
    }

    public function isCanceled(): bool
    {
        return \in_array($this->state, [
            CallTaskState::QUEUING_CANCEL,
            CallTaskState::TASK_CANCEL,
            CallTaskState::ROBOT_CANCEL,
        ], true);
    }
}
