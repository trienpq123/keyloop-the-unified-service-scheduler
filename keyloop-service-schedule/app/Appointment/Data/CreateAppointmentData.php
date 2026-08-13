<?php

namespace App\Appointment\Data;

use App\Customer\Data\GuestCustomerData;
use App\Vehicle\Data\VehicleData;
use Carbon\CarbonImmutable;

final readonly class CreateAppointmentData
{
    public function __construct(
        public GuestCustomerData $customer,
        public VehicleData $vehicle,
        public int $dealershipId,
        public int $serviceTypeId,
        public CarbonImmutable $requestedStartAt,
        public string $idempotencyKey,
        public string $requestHash,
    ) {}
}
