<?php

namespace App\Service\Pudu\Dto;

use App\Service\Pudu\Enum\CallMode;

/**
 * Request payload for the InitiateCallTask API.
 *
 * Either $sn or $shopId must be provided:
 * - $sn alone   → call that specific robot
 * - $shopId alone → randomly dispatch any available robot in the shop
 */
final class InitiateCallTaskRequest
{
    /**
     * @param string        $mapName     Map name (required)
     * @param string        $point       Target point name (required)
     * @param string        $pointType   Point type, e.g. "table" (required)
     * @param string|null   $sn          Robot serial number; provide to target a specific robot
     * @param int|null      $shopId      Shop ID; provide to randomly call any available robot in the shop
     * @param CallMode|null $callMode    Call mode; null or EMPTY means non-custom (task ends on arrival)
     * @param ModeData|null $modeData    Extra content for the chosen call mode
     * @param bool|null     $doNotQueue  If true, fail immediately instead of queuing when robot is busy
     */
    public function __construct(
        public readonly string $mapName,
        public readonly string $point,
        public readonly string $pointType,
        public readonly ?string $sn = null,
        public readonly ?int $shopId = null,
        public readonly ?CallMode $callMode = null,
        public readonly ?ModeData $modeData = null,
        public readonly ?bool $doNotQueue = null,
    ) {
    }

    /**
     * Serializes to the snake_case array format expected by the Pudu API.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $body = [
            'map_name'   => $this->mapName,
            'point'      => $this->point,
            'point_type' => $this->pointType,
        ];

        if (null !== $this->sn) {
            $body['sn'] = $this->sn;
        }
        if (null !== $this->shopId) {
            $body['shop_id'] = $this->shopId;
        }
        if (null !== $this->callMode && CallMode::EMPTY !== $this->callMode) {
            $body['call_mode'] = $this->callMode->value;
        }
        if (null !== $this->modeData) {
            $modeDataArray = $this->modeData->toArray();
            if (!empty($modeDataArray)) {
                $body['mode_data'] = $modeDataArray;
            }
        }
        if (null !== $this->doNotQueue) {
            $body['do_not_queue'] = $this->doNotQueue;
        }

        return $body;
    }
}
