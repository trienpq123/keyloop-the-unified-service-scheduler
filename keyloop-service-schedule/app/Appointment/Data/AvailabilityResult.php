<?php

namespace App\Appointment\Data;

use App\Appointment\ValueObjects\TimeRange;

final readonly class AvailabilityResult
{
    public function __construct(
        public bool $available,
        public TimeRange $period,
        public int $availableTechnicians,
        public int $availableServiceBays,
    ) {}
}
