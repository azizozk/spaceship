<?php

namespace App\Service\Pudu\Enum;

/**
 * Task states returned by the GetCurrentTaskStatus legacy API (FlashBot only).
 */
enum RobotTaskState: string
{
    case AWAIT = 'Await';
    case ONGOING = 'Ongoing';
    case ARRIVE = 'Arrive';
    case COMPLETE = 'Complete';
    case FAIL = 'Fail';
    case CANCEL = 'Cancel';
}
