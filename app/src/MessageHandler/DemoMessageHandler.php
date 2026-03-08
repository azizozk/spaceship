<?php

namespace App\MessageHandler;

use App\Message\DemoMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DemoMessageHandler
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(DemoMessage $message): void
    {
        $this->logger->info('Received DemoMessage', ['content' => $message->content]);

        // TODO: add your business logic here
    }
}
