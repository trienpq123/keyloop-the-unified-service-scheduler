<?php

namespace App\Appointment\Actions;

use App\Appointment\Exceptions\OutsideBusinessHours;
use App\Appointment\ValueObjects\BusinessHoursSchedule;
use App\Appointment\ValueObjects\TimeRange;
use App\Models\Dealership;

final class AssertBusinessHours
{
    public function execute(Dealership $dealership, TimeRange $period): void
    {
        $weeklyHours = $dealership->businessHours()
            ->get(['weekday', 'opens_at', 'closes_at'])
            ->mapWithKeys(static fn ($hours): array => [$hours->weekday => [
                'opens_at' => $hours->opens_at,
                'closes_at' => $hours->closes_at,
            ]])
            ->all();

        if (! (new BusinessHoursSchedule($weeklyHours))->contains($period, $dealership->timezone)) {
            throw new OutsideBusinessHours;
        }
    }
}
