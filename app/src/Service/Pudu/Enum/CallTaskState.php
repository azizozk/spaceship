<?php

namespace App\Service\Pudu\Enum;

/**
 * Possible states returned by the InitiateCallTask API and task status callbacks.
 */
enum CallTaskState: string
{
    /** Robot is on its way to the target point */
    case CALLING = 'CALLING';

    /** Robot arrived and responded successfully */
    case CALL_SUCCESS = 'CALL_SUCCESS';

    /** Task queued because robot is currently busy */
    case QUEUEING = 'QUEUEING';

    /** Call failed (robot offline, disabled, etc.) */
    case CALL_FAILED = 'CALL_FAILED';

    /** Task fully completed */
    case CALL_COMPLETE = 'CALL_COMPLETE';

    /** Canceled while waiting in queue */
    case QUEUING_CANCEL = 'QUEUING_CANCEL';

    /** Task canceled via API */
    case TASK_CANCEL = 'TASK_CANCEL';

    /** Task canceled by the robot itself */
    case ROBOT_CANCEL = 'ROBOT_CANCEL';

    /** Robot paused en route (P-ONE models only) */
    case PAUSE = 'PAUSE';

    /** Robot arrived at the target point (P-ONE models only) */
    case ARRIVE = 'ARRIVE';
}
