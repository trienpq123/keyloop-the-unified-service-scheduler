<?php

declare(strict_types=1);

namespace App\Appointment\Exceptions;

use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;

final class AppointmentSlotUnavailable extends DomainException
{
    public function __construct(string $message = '', ?\Throwable $previous = null)
    {
        parent::__construct(
            errorCode: ErrorCode::AppointmentSlotUnavailable,
            message: $message,
            previous: $previous,
        );
    }
}
