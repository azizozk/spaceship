<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 0)]
class RequestListener
{
    public function __construct(
        private LoggerInterface $requestLogger,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $this->requestLogger->info('Incoming request', [
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'headers' => $this->filterHeaders($request),
            'body' => $request->getContent() ?: null,
            'ip' => $request->getClientIp(),
        ]);
    }

    private function filterHeaders(Request $request): array
    {
        $headers = $request->headers->all();
        unset($headers['cookie'], $headers['authorization']);

        return array_map(
            fn(array $values) => count($values) === 1 ? $values[0] : $values,
            $headers,
        );
    }
}
