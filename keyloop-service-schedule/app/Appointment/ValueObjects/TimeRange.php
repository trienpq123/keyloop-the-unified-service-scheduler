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

    public static function fromIso8601(string $start, string $end): self
    {
        return new self(
            CarbonImmutable::parse($start)->utc(),
            CarbonImmutable::parse($end)->utc(),
        );
    }

    public function overlaps(self $other): bool
    {
        return $this->start->lt($other->end) && $this->end->gt($other->start);
    }

    public function contains(CarbonImmutable $instant): bool
    {
        return ! $instant->lt($this->start) && $instant->lt($this->end);
    }

    public function durationInMinutes(): int
    {
        return (int) $this->start->diffInMinutes($this->end);
    }
}
