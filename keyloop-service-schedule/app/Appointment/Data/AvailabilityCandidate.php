<?php

declare(strict_types=1);

namespace App\Appointment\Data;

final readonly class AvailabilityCandidate
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
