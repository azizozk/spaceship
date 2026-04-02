<?php

namespace App\Tests\Service;

use App\Message\SyncRobotMessage;
use App\Service\MessageService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\SharedLockInterface as LockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageServiceTest extends TestCase
{
    private LockFactory $lockFactory;
    private MessageBusInterface $bus;
    private MessageService $service;

    protected function setUp(): void
    {
        $this->lockFactory = $this->createMock(LockFactory::class);
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->service = new MessageService($this->lockFactory, $this->bus);
    }

    public function testSendSyncRobotMessageDispatchesWhenLockAcquired(): void
    {
        $message = new SyncRobotMessage(42);
        $lock = $this->createMock(LockInterface::class);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('sync_robot_42')
            ->willReturn($lock);

        $lock->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->bus->expects(self::once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message));

        $this->service->sendSyncRobotMessage($message);
    }

    public function testSendSyncRobotMessageThrowsWhenLockNotAcquired(): void
    {
        $message = new SyncRobotMessage(42);
        $lock = $this->createMock(LockInterface::class);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('sync_robot_42')
            ->willReturn($lock);

        $lock->expects(self::once())
            ->method('acquire')
            ->willReturn(false);

        $this->bus->expects(self::never())->method('dispatch');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Message in progress, try again later.');

        $this->service->sendSyncRobotMessage($message);
    }

    public function testFinishSyncRobotMessageReleasesLock(): void
    {
        $message = new SyncRobotMessage(7);
        $lock = $this->createMock(LockInterface::class);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('sync_robot_7')
            ->willReturn($lock);

        $lock->expects(self::once())->method('release');
        $this->bus->expects(self::never())->method('dispatch');

        $this->service->finishSyncRobotMessage($message);
    }
}
