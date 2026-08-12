<?php

declare(strict_types=1);

namespace App\Customer\Exceptions;

use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;

final class CustomerIdentityConflict extends DomainException
{
    public function __construct(string $message = '', ?\Throwable $previous = null)
    {
        parent::__construct(
            errorCode: ErrorCode::CustomerIdentityConflict,
            message: $message,
            previous: $previous,
        );
    }
}
