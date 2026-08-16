<?php

declare(strict_types=1);

namespace App\Appointment\ValueObjects;

final readonly class BusinessHoursSchedule
{
    /**
     * @param  array<int, array{opens_at: string, closes_at: string}>  $weeklyHours  ISO weekday => opening window
     */
    public function __construct(
        private array $weeklyHours,
    ) {}

    public function contains(TimeRange $period, string $timezone): bool
    {
        $start = $period->start->setTimezone($timezone);
        $end = $period->end->setTimezone($timezone);

        if ($start->toDateString() !== $end->toDateString()) {
            return false;
        }

        $hours = $this->weeklyHours[$start->dayOfWeekIso] ?? null;

        return $hours !== null
            && $start->format('H:i:s') >= $hours['opens_at']
            && $end->format('H:i:s') <= $hours['closes_at'];
    }
}
