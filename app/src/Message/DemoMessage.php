<?php

namespace App\Message;

final class DemoMessage implements AsyncMessageInterface
{
    public function __construct(
        public readonly string $content,
    ) {
    }
}
