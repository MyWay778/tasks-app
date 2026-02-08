<?php

namespace App\Enums;

enum TaskStatus: string
{
    case COMPLETED = 'complete';
    case NOT_COMPLETED = 'not completed';
}
