<?php

namespace App\Appointment\Enums;

enum IdempotencyStatus: string
{
    case Processing = 'processing';
    case Completed = 'completed';
}
