<?php

namespace App\Service\Pudu\Dto;

/**
 * Request payload for the CancelCallTask API.
 *
 * Provide either $taskId or $sn (or both):
 * - $taskId alone → cancel that specific task
 * - $sn alone     → cancel all unfinished tasks under that robot
 *
 * Note: the canceling call must use the same APPKEY that initiated the task,
 * otherwise the API returns CLOUD_OPEN_TASK_BELONG_ERROR.
 */
final class CancelCallTaskRequest
{
    /**
     * @param string|null $taskId     Task ID returned by InitiateCallTask
     * @param string|null $sn         Robot serial number; cancels all unfinished tasks for this robot
     * @param bool|null   $isAutoBack Whether the robot should auto-return after the task is canceled
     */
    public function __construct(
        public readonly ?string $taskId = null,
        public readonly ?string $sn = null,
        public readonly ?bool $isAutoBack = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $body = [];

        if (null !== $this->taskId) {
            $body['task_id'] = $this->taskId;
        }
        if (null !== $this->sn) {
            $body['sn'] = $this->sn;
        }
        if (null !== $this->isAutoBack) {
            $body['is_auto_back'] = $this->isAutoBack;
        }

        return $body;
    }
}
