<?php

namespace App\Service\Pudu\Dto;

/**
 * Extra content for custom call modes (mode_data).
 *
 * Fields are mode-dependent:
 * - IMG mode:      urls, switch_time, cancel_btn_time, show_timeout
 * - VIDEO mode:    urls, play_count, cancel_btn_time, show_timeout
 * - QR_CODE mode:  qrcode, text, cancel_btn_time, show_timeout
 */
final class ModeData
{
    /**
     * @param list<string>|null $urls         Image URLs (IMG mode) or video URLs (VIDEO mode)
     * @param int|null          $switchTime    IMG mode: seconds between image switches
     * @param int|null          $playCount     VIDEO mode: number of play loops
     * @param int|null          $cancelBtnTime Seconds before cancel button appears
     * @param int|null          $showTimeout   Content display timeout in seconds
     * @param string|null       $qrcode        QR code content (QR_CODE mode)
     * @param string|null       $text          Text shown alongside QR code (QR_CODE mode)
     */
    public function __construct(
        public readonly ?array $urls = null,
        public readonly ?int $switchTime = null,
        public readonly ?int $playCount = null,
        public readonly ?int $cancelBtnTime = null,
        public readonly ?int $showTimeout = null,
        public readonly ?string $qrcode = null,
        public readonly ?string $text = null,
    ) {
    }

    /**
     * Serializes to the snake_case array format expected by the Pudu API.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if (null !== $this->urls) {
            $data['urls'] = $this->urls;
        }
        if (null !== $this->switchTime) {
            $data['switch_time'] = $this->switchTime;
        }
        if (null !== $this->playCount) {
            $data['play_count'] = $this->playCount;
        }
        if (null !== $this->cancelBtnTime) {
            $data['cancel_btn_time'] = $this->cancelBtnTime;
        }
        if (null !== $this->showTimeout) {
            $data['show_timeout'] = $this->showTimeout;
        }
        if (null !== $this->qrcode) {
            $data['qrcode'] = $this->qrcode;
        }
        if (null !== $this->text) {
            $data['text'] = $this->text;
        }

        return $data;
    }
}
