<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('doctrine')]
final class SyncRobotMessage
{

    public function __construct(
        public readonly int $puduAccountId,
    ) {
    }
}
