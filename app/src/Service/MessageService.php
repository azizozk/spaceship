<?php

namespace App\Service;

use App\Message\SyncRobotMessage;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessageService
{
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
        $lock = $this->lockFactory->createLock("sync_robot_{$message->puduAccountId}");

        if (!$lock->acquire()) {
            throw new \RuntimeException('Message in progress, try again later.');
        }

        $this->bus->dispatch($message);
    }

    public function finishSyncRobotMessage(SyncRobotMessage $message): void
    {
        $lock = $this->lockFactory->createLock("sync_robot_{$message->puduAccountId}");
        $lock->release();
    }
}
