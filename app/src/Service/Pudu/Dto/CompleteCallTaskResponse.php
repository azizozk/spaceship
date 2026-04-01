<?php

namespace App\Service\Pudu\Dto;

/**
 * Response from the CompleteCallTask API.
 */
final class CompleteCallTaskResponse
{
    public function __construct(
        /** Unique trace ID for this request, format: {AppKey}_{uuid} */
        public readonly string $traceId,

        /** "SUCCESS" on success, otherwise an error code */
        public readonly string $message,

        /**
         * The immediate dispatch result for the next task, if one was requested.
         * null when no next_call_task was provided or the API returned no data.
         */
        public readonly ?NextCallResult $nextCallResult,
    ) {
    }

    /**
     * @param array<string, mixed> $raw Full API response body
     */
    public static function fromArray(array $raw): self
    {
        $nextCallResult = null;

        $resultData = $raw['data']['next_call_result'] ?? null;
        if (is_array($resultData)) {
            $nextCallResult = NextCallResult::fromArray($resultData);
        }

        return new self(
            traceId: (string) ($raw['trace_id'] ?? ''),
            message: (string) ($raw['message'] ?? ''),
            nextCallResult: $nextCallResult,
        );
    }

    public function isSuccess(): bool
    {
        return 'SUCCESS' === $this->message;
    }
}
