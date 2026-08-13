<?php

namespace App\Appointment\Exceptions;

use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;

final class OutsideBusinessHours extends DomainException
{
    public function __construct()
    {
        parent::__construct(ErrorCode::OutsideBusinessHours);
    }
}
