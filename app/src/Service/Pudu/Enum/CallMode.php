<?php

namespace App\Service\Pudu\Enum;

/**
 * Call modes for the InitiateCallTask API.
 *
 * Note: P-ONE robots support all modes.
 * For non P-ONE robots, any mode other than CALL requires the legacy platform flow.
 */
enum CallMode: string
{
    /** Non-custom call — task ends on arrival (same as omitting the field) */
    case EMPTY = '';

    /** Image mode — displays a slideshow of images on the robot screen */
    case IMG = 'IMG';

    /** Payment QR code mode — shows a scannable QR code */
    case QR_CODE = 'QR_CODE';

    /** Video mode — plays a video on the robot screen */
    case VIDEO = 'VIDEO';

    /** Arrival confirmation mode — waits for explicit human confirmation */
    case CALL_CONFIRM = 'CALL_CONFIRM';

    /** Arrival completion mode — task completes on arrival */
    case CALL = 'CALL';
}
