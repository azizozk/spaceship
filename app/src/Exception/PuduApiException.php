<?php

namespace App\Exception;

use Symfony\Contracts\HttpClient\ResponseInterface;

class PuduApiException extends \RuntimeException
{
    public function __construct(
        string $message,
        private readonly ?ResponseInterface $response = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $response?->getStatusCode() ?? 0, $previous);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
