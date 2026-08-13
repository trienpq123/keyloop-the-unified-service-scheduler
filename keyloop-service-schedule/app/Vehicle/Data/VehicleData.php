<?php

namespace App\Vehicle\Data;

final readonly class VehicleData
{
    public function __construct(
        public string $registrationNumber,
        public ?string $make,
        public ?string $model,
        public ?int $manufacturedYear,
    ) {}
}
