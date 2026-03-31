<?php

namespace App\Service\Pudu\Enum;

/**
 * Task types returned by the GetCurrentTaskStatus legacy API (FlashBot only).
 */
enum RobotTaskType: string
{
    case DIRECT = 'Direct';
    case ROOM_DELIVERY = 'RoomDelivery';
    case ORDER_DELIVERY = 'OrderDelivery';
    case CHARGE = 'Charge';
    case BACK_HOME = 'BackHome';
    case BACK_MEET = 'BackMeet';
    case CRUISE = 'Cruise';
    case CALL = 'Call';
    case GUEST = 'Guest';
}
