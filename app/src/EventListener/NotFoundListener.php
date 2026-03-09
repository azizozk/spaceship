<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
class NotFoundListener
{
    public function __construct(
        private LoggerInterface $notfoundLogger,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof NotFoundHttpException) {
            return;
        }

        $request = $event->getRequest();

        $this->notfoundLogger->warning('404 Not Found', [
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
