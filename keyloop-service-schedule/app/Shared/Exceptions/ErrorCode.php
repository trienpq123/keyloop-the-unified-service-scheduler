<?php

namespace App\Shared\Exceptions;

enum ErrorCode: string
{
    case ValidationFailed = 'VALIDATION_FAILED';
    case ResourceNotFound = 'RESOURCE_NOT_FOUND';
    case CustomerIdentityConflict = 'CUSTOMER_IDENTITY_CONFLICT';
    case VehicleOwnershipConflict = 'VEHICLE_OWNERSHIP_CONFLICT';
    case AppointmentSlotUnavailable = 'APPOINTMENT_SLOT_UNAVAILABLE';
    case IdempotencyConflict = 'IDEMPOTENCY_CONFLICT';
    case IdempotencyRequestInProgress = 'IDEMPOTENCY_REQUEST_IN_PROGRESS';
    case OutsideBusinessHours = 'OUTSIDE_BUSINESS_HOURS';
    case InternalServerError = 'INTERNAL_SERVER_ERROR';

    public function httpStatus(): int
    {
        return match ($this) {
            self::ValidationFailed => 422,
            self::ResourceNotFound => 404,
            self::CustomerIdentityConflict,
            self::VehicleOwnershipConflict,
            self::AppointmentSlotUnavailable,
            self::IdempotencyConflict,
            self::IdempotencyRequestInProgress => 409,
            self::OutsideBusinessHours => 422,
            self::InternalServerError => 500,
        };
    }

    public function defaultMessage(): string
    {
        return match ($this) {
            self::ValidationFailed => 'The request input is invalid.',
            self::ResourceNotFound => 'The requested resource was not found.',
            self::CustomerIdentityConflict => 'The supplied contact identifiers match conflicting customer profiles.',
            self::VehicleOwnershipConflict => 'The vehicle registration number belongs to a different customer.',
            self::AppointmentSlotUnavailable => 'No qualified technician or service bay is available for the requested period.',
            self::IdempotencyConflict => 'The idempotency key has already been used with a different request payload.',
            self::IdempotencyRequestInProgress => 'The same operation is currently being processed.',
            self::OutsideBusinessHours => 'The requested appointment period is outside dealership business hours.',
            self::InternalServerError => 'An unexpected error occurred. Please try again later.',
        };
    }
}
