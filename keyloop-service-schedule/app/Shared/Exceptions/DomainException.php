<?php

namespace App\Shared\Exceptions;

use RuntimeException;

abstract class DomainException extends RuntimeException
{
    public function __construct(
        private readonly ErrorCode $errorCode,
        string $message = '',
        private readonly array $details = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            $message !== '' ? $message : $errorCode->defaultMessage(),
            0,
            $previous,
        );
    }

    public function errorCode(): ErrorCode
    {
        return $this->errorCode;
    }

    public function httpStatus(): int
    {
        return $this->errorCode->httpStatus();
    }

    /**
     * @return array<string, mixed>
     */
    public function details(): array
    {
        return $this->details;
    }
}
