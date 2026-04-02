<?php

namespace App\Service;

use App\Message\SyncRobotMessage;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessageService
{
    private const SYNC_ROBOT_THROTTLE_TTL = 60;

    public function __construct(
        private LockFactory $lockFactory,
        private MessageBusInterface $bus,
    ) {
    }

    /**
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    public function sendSyncRobotMessage(SyncRobotMessage $message): void
    {
        $rateLimitLock = $this->lockFactory->createLock(
            "sync_robot_throttle_{$message->puduAccountId}",
            ttl: self::SYNC_ROBOT_THROTTLE_TTL,
            autoRelease: false
        );

        if (!$rateLimitLock->acquire()) {
            throw new \RuntimeException('Rate limit exceeded, only one sync per minute is allowed.');
        }


        $this->bus->dispatch($message);
    }

    public function finishSyncRobotMessage(SyncRobotMessage $message): void
    {
        $rateLimitLock = $this->lockFactory->createLock(
            "sync_robot_throttle_{$message->puduAccountId}",
            ttl: self::SYNC_ROBOT_THROTTLE_TTL,
            autoRelease: false
        );
        $rateLimitLock->release();
    }
}
