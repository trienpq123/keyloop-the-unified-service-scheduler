<?php

declare(strict_types=1);

namespace App\Appointment\Exceptions;

use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;

final class IdempotencyRequestInProgress extends DomainException
{
    public function __construct(string $message = '', ?\Throwable $previous = null)
    {
        parent::__construct(
            errorCode: ErrorCode::IdempotencyRequestInProgress,
            message: $message,
            previous: $previous,
        );
    }
}
