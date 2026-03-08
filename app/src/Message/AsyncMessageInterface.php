<?php

namespace App\Message;

/**
 * Marker interface: all classes implementing this will be routed
 * to the async (RabbitMQ) Messenger transport.
 */
interface AsyncMessageInterface
{
}
