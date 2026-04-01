<?php

namespace App\Service\Pudu\Dto;

/**
 * Current cleaning task detail from `data.cleanbot.clean`.
 */
final class CleanBotCleanInfo
{
    public function __construct(
        /** Work mode: 1 manual, 2 auto */
        public readonly int $mode,

        public readonly string $reportId,

        public readonly string $msg,

        public readonly CleanBotTaskResult $result,

        public readonly CleanBotTaskDef $task,

        public readonly CleanBotMapInfo $map,

        public readonly CleanBotConfig $config,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            mode: (int) ($data['mode'] ?? 0),
            reportId: (string) ($data['report_id'] ?? ''),
            msg: (string) ($data['msg'] ?? ''),
            result: CleanBotTaskResult::fromArray($data['result'] ?? []),
            task: CleanBotTaskDef::fromArray($data['task'] ?? []),
            map: CleanBotMapInfo::fromArray($data['map'] ?? []),
            config: CleanBotConfig::fromArray($data['config'] ?? []),
        );
    }

    public function isManualMode(): bool
    {
        return 1 === $this->mode;
    }

    public function isAutoMode(): bool
    {
        return 2 === $this->mode;
    }
}
