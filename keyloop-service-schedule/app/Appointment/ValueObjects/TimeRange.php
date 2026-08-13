<?php

namespace App\Appointment\ValueObjects;

use App\Models\ServiceType;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

final readonly class TimeRange
{
    public function __construct(
        public CarbonImmutable $start,
        public CarbonImmutable $end,
    ) {
        if (! $end->isAfter($start)) {
            throw new InvalidArgumentException(
                sprintf(
                    'TimeRange end (%s) must be strictly after start (%s).',
                    $end->toIso8601String(),
                    $start->toIso8601String(),
                ),
            );
        }
    }

    /**
     * Build a TimeRange from a start instant and a ServiceType.
     *
     * @throws InvalidArgumentException when the service type duration <= 0.
     */
    public static function fromServiceType(
        CarbonImmutable $start,
        ServiceType $serviceType,
    ): self {
        $duration = $serviceType->duration_minutes;

        if ($duration <= 0) {
            throw new InvalidArgumentException(
                "Service type [{$serviceType->id}] has an invalid duration: {$duration} minutes.",
            );
        }

        return new self(
            start: $start->utc(),
            end: $start->addMinutes($duration)->utc(),
        );
    }
}
