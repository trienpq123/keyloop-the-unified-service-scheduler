<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

final class ResourceNotFoundException extends DomainException
{
    public function __construct(string $resource = '', int|string $id = '', ?\Throwable $previous = null)
    {
        $message = ($resource !== '' && $id !== '')
            ? "{$resource} with identifier [{$id}] was not found."
            : ErrorCode::ResourceNotFound->defaultMessage();

        parent::__construct(
            errorCode: ErrorCode::ResourceNotFound,
            message: $message,
            previous: $previous,
        );
    }
}
