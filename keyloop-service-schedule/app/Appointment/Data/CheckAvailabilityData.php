<?php

namespace App\Appointment\Data;

use Carbon\CarbonImmutable;

final readonly class CheckAvailabilityData
{
    public function __construct(
        public int $dealershipId,
        public int $serviceTypeId,
        public CarbonImmutable $requestedStartAt,
    ) {}
}
