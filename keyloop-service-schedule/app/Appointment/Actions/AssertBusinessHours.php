<?php

namespace App\Appointment\Actions;

use App\Appointment\Exceptions\OutsideBusinessHours;
use App\Appointment\ValueObjects\TimeRange;
use App\Models\Dealership;

final class AssertBusinessHours
{
    public function execute(Dealership $dealership, TimeRange $period): void
    {
        $start = $period->start->setTimezone($dealership->timezone);
        $end = $period->end->setTimezone($dealership->timezone);
        if ($start->toDateString() !== $end->toDateString()) {
            throw new OutsideBusinessHours;
        }

        $hours = $dealership->businessHours()->where('weekday', $start->dayOfWeekIso)->first();
        if ($hours === null || $start->format('H:i:s') < $hours->opens_at || $end->format('H:i:s') > $hours->closes_at) {
            throw new OutsideBusinessHours;
        }
    }
}
