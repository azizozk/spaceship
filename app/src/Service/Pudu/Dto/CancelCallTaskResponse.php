<?php

namespace App\Service\Pudu\Dto;

/**
 * Response from the CancelCallTask API.
 */
final class CancelCallTaskResponse
{
    public function __construct(
        /** Unique trace ID for this request, format: {AppKey}_{uuid} */
        public readonly string $traceId,

        /** "SUCCESS" on success, otherwise an error code */
        public readonly string $message,
    ) {
    }

    /**
     * @param array<string, mixed> $raw Full API response body
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            traceId: (string) ($raw['trace_id'] ?? ''),
            message: (string) ($raw['message'] ?? ''),
        );
    }

    public function isSuccess(): bool
    {
        return 'SUCCESS' === $this->message;
    }
}
