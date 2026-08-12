<?php

declare(strict_types=1);

namespace App\Vehicle\Exceptions;

use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;

final class VehicleOwnershipConflict extends DomainException
{
    public function __construct(string $message = '', ?\Throwable $previous = null)
    {
        parent::__construct(
            errorCode: ErrorCode::VehicleOwnershipConflict,
            message: $message,
            previous: $previous,
        );
    }
}
