<?php

namespace App\Appointment\Enums;

enum AppointmentStatus: string
{
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';

    /**
     * Returns the statuses that block resource allocation.
     */
    public static function resourceReserving(): array
    {
        return [self::Confirmed];
    }

    /**
     * Returns the string values of resource-reserving statuses.
     */
    public static function resourceReservingValues(): array
    {
        return array_map(
            static fn (self $status): string => $status->value,
            self::resourceReserving(),
        );
    }
}
