<?php

namespace App\Service\Pudu\Dto;

/**
 * Breakpoint position from `data.cleanbot.clean.result.break_point`.
 *
 * Marks where the robot paused or was interrupted so it can resume from
 * the same spot.
 */
final class CleanBotBreakPoint
{
    public function __construct(
        /** Breakpoint index; -1 means no active breakpoint */
        public readonly int $index,

        public readonly CleanBotPosition $vector,

        public readonly int $cleanType,

        /** Edge-start position; null when not applicable */
        public readonly ?CleanBotPosition $start,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $start = null;
        if (isset($data['start']) && is_array($data['start'])) {
            $start = CleanBotPosition::fromArray($data['start']);
        }

        return new self(
            index: (int) ($data['index'] ?? -1),
            vector: CleanBotPosition::fromArray($data['vector'] ?? []),
            cleanType: (int) ($data['clean_type'] ?? 0),
            start: $start,
        );
    }

    public function hasBreakPoint(): bool
    {
        return $this->index >= 0;
    }
}
