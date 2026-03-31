<?php

namespace App\Service\Pudu\Dto;

use App\Service\Pudu\Enum\CallMode;

/**
 * Defines the next custom call task to execute after completing the current one.
 * Used as an optional field in CompleteCallTaskRequest.
 */
final class NextCallTask
{
    /**
     * @param int           $shopId    Shop ID the robot belongs to (required)
     * @param string        $mapName   Map name (required)
     * @param string        $point     Target point name (required)
     * @param string        $pointType Point type, e.g. "table" (required)
     * @param CallMode|null $callMode  Call mode; null means non-custom (task ends on arrival)
     * @param ModeData|null $modeData  Extra content for the chosen call mode
     */
    public function __construct(
        public readonly int $shopId,
        public readonly string $mapName,
        public readonly string $point,
        public readonly string $pointType,
        public readonly ?CallMode $callMode = null,
        public readonly ?ModeData $modeData = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $body = [
            'shop_id'    => $this->shopId,
            'map_name'   => $this->mapName,
            'point'      => $this->point,
            'point_type' => $this->pointType,
        ];

        if (null !== $this->callMode && CallMode::EMPTY !== $this->callMode) {
            $body['call_mode'] = $this->callMode->value;
        }

        if (null !== $this->modeData) {
            $modeDataArray = $this->modeData->toArray();
            if (!empty($modeDataArray)) {
                $body['mode_data'] = $modeDataArray;
            }
        }

        return $body;
    }
}
